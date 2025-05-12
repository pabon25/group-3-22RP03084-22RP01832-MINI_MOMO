<?php
require_once 'Util.php';
require_once 'Sms.php';

class Menu {
    protected $text;
    protected $sessionId;
    protected $phoneNumber;
    protected $util;
    protected $pdo;

    public function __construct($text, $sessionId, $phoneNumber) {
        $this->text = $text;
        $this->sessionId = $sessionId;
        $this->phoneNumber = $phoneNumber;
        $this->util = new Util();
        $this->pdo = $this->util->getConnection();
        $this->storeSession();
    }

    private function storeSession() {
        $stmt = $this->pdo->prepare("
            INSERT INTO sessions (session_id, phone_number, menu_state) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE menu_state = ?
        ");
        $stmt->execute([$this->sessionId, $this->phoneNumber, $this->text, $this->text]);
    }

    public function isRegistered() {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE phone_number = ?");
        $stmt->execute([$this->phoneNumber]);
        return $stmt->fetch() !== false;
    }

    public function isAgent() {
        $stmt = $this->pdo->prepare("
            SELECT id FROM agents 
            WHERE phone_number = ? AND approved = 1
        ");
        $stmt->execute([$this->phoneNumber]);
        return $stmt->fetch() !== false;
    }

    public function mainMenuUnregistered() {
        $response = "CON Welcome to XYZ MOMO\n";
        $response .= "1. Register\n";
        echo $response;
    }

    public function menuRegister($textArray) {
        $level = count($textArray);

        if($level == 1) {
            echo "CON Enter your full name:";
        } elseif($level == 2) {
            echo "CON Enter 4-digit PIN:";
        } elseif($level == 3) {
            echo "CON Confirm your PIN:";
        } elseif($level == 4) {
            $name = $textArray[1];
            $pin = $textArray[2];
            $confirmPin = $textArray[3];

            if($pin != $confirmPin) {
                echo "END PINs do not match";
            } elseif(strlen($pin) != 4 || !is_numeric($pin)) {
                echo "END PIN must be 4 digits";
            } else {
                try {
                    $stmt = $this->pdo->prepare("
                        INSERT INTO users (phone_number, full_name, pin_hash, balance)
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $this->phoneNumber,
                        $name,
                        Util::hashPin($pin),
                        Util::USER_BALANCE
                    ]);
                    
                    // Send welcome SMS
                    $sms = new Sms($this->phoneNumber);
                    $welcomeMsg = "Welcome to MOMO, $name! Your account is ready with ".Util::formatAmount(Util::USER_BALANCE)." balance.";
                    $sms->sendSMS($welcomeMsg, $this->phoneNumber);
                    
                    echo "END Registration successful. Welcome $name!";
                } catch(PDOException $e) {
                    echo "END Registration failed. Try again.";
                }
            }
        }
    }

    public function mainMenuRegistered() {
        if($this->isAgent()) {
            $this->agentMainMenu();
        } else {
            $response = "CON Main Menu\n";
            $response .= "1. Send Money\n";
            $response .= "2. Withdraw Money\n";
            $response .= "3. Check Balance\n";
            echo $response;
        }
    }

    public function agentMainMenu() {
        $response = "CON AGENT MENU\n";
        $response .= "1. Approve Withdrawals\n";
        $response .= "2. Check My Balance\n";
        $response .= "3. View Transactions\n";
        echo $response;
    }

    public function menuSendMoney($textArray) {
        $level = count($textArray);

        if($level == 1) {
            echo "CON Enter recipient number:";
        } elseif($level == 2) {
            echo "CON Enter amount:";
        } elseif($level == 3) {
            echo "CON Enter your PIN:";
        } elseif($level == 4) {
            $recipient = $textArray[1];
            $amount = $textArray[2];
            $pin = $textArray[3];

            if(!$this->verifyUserPin($this->phoneNumber, $pin)) {
                echo "END Invalid PIN";
                return;
            }

            if(!$this->userExists($recipient)) {
                echo "END Recipient not registered";
                return;
            }

            $totalAmount = $amount + Util::TRANSACTION_FEE;
            if(!$this->hasSufficientBalance($this->phoneNumber, $totalAmount)) {
                echo "END Insufficient balance (Fee: ".Util::TRANSACTION_FEE.")";
                return;
            }

            $response = "CON Send ".Util::formatAmount($amount)." to $recipient?\n";
            $response .= "Fee: ".Util::formatAmount(Util::TRANSACTION_FEE)."\n";
            $response .= "1. Confirm\n";
            $response .= "2. Cancel\n";
            echo $response;
        } elseif($level == 5 && $textArray[4] == 1) {
            $recipient = $textArray[1];
            $amount = $textArray[2];
            $reference = Util::generateReference();

            try {
                $this->pdo->beginTransaction();

                // Deduct from sender (amount + fee)
                $stmt = $this->pdo->prepare("
                    UPDATE users SET balance = balance - ? 
                    WHERE phone_number = ?
                ");
                $stmt->execute([$amount + Util::TRANSACTION_FEE, $this->phoneNumber]);

                // Add to recipient
                $stmt = $this->pdo->prepare("
                    UPDATE users SET balance = balance + ? 
                    WHERE phone_number = ?
                ");
                $stmt->execute([$amount, $recipient]);

                // Record transaction
                $stmt = $this->pdo->prepare("
                    INSERT INTO transactions 
                    (reference, user_phone, amount, type, status, fee)
                    VALUES (?, ?, ?, 'send', 'completed', ?)
                ");
                $stmt->execute([
                    $reference,
                    $this->phoneNumber,
                    $amount,
                    Util::TRANSACTION_FEE
                ]);

                $this->pdo->commit();
                
                // Send SMS to sender
                $senderSms = new Sms($this->phoneNumber);
                $senderMessage = "You sent ".Util::formatAmount($amount)." to $recipient\n";
                $senderMessage .= "Fee: ".Util::formatAmount(Util::TRANSACTION_FEE)."\n";
                $senderMessage .= "Ref: $reference\nNew balance: ".Util::formatAmount($this->getUserBalance($this->phoneNumber));
                $senderSms->sendSMS($senderMessage, $this->phoneNumber);
                
                // Send SMS to recipient
                $recipientSms = new Sms($recipient);
                $recipientMessage = "You received ".Util::formatAmount($amount)." from ".$this->phoneNumber."\n";
                $recipientMessage .= "Ref: $reference\nNew balance: ".Util::formatAmount($this->getUserBalance($recipient));
                $recipientSms->sendSMS($recipientMessage, $recipient);
                
                echo "END Sent ".Util::formatAmount($amount)." to $recipient\nRef: $reference";
            } catch(Exception $e) {
                $this->pdo->rollBack();
                echo "END Transaction failed";
            }
        } else {
            echo "END Transaction cancelled";
        }
    }

    public function menuWithdrawMoney($textArray) {
        $level = count($textArray);

        if($level == 1) {
            echo "CON Enter amount:";
        } elseif($level == 2) {
            echo "CON Enter agent code:";
        } elseif($level == 3) {
            echo "CON Enter your PIN:";
        } elseif($level == 4) {
            $amount = $textArray[1];
            $agentCode = $textArray[2];
            $pin = $textArray[3];

            if(!$this->verifyUserPin($this->phoneNumber, $pin)) {
                echo "END Invalid PIN";
                return;
            }

            if(!$this->agentExists($agentCode)) {
                echo "END Agent not found";
                return;
            }
    
            $totalAmount = $amount + Util::TRANSACTION_FEE;
            if(!$this->hasSufficientBalance($this->phoneNumber, $totalAmount)) {
                echo "END Insufficient balance (Fee: ".Util::TRANSACTION_FEE.")";
                return;
            }

            $reference = Util::generateReference();
            try {
                $stmt = $this->pdo->prepare("
                    INSERT INTO transactions 
                    (reference, user_phone, agent_code, amount, type, status, fee)
                    VALUES (?, ?, ?, ?, 'withdraw', 'pending', ?)
                ");
                $stmt->execute([
                    $reference,
                    $this->phoneNumber,
                    $agentCode,
                    $amount,
                    Util::TRANSACTION_FEE
                ]);
                
                // Send SMS to user
                $userSms = new Sms($this->phoneNumber);
                $userMessage = "Withdrawal request of ".Util::formatAmount($amount)." submitted\n";
                $userMessage .= "Agent: $agentCode\nRef: $reference\n";
                $userMessage .= "Visit agent to complete transaction";
                $userSms->sendSMS($userMessage, $this->phoneNumber);
                
                // Send SMS to agent
                $agentPhone = $this->getAgentPhone($agentCode);
                if($agentPhone) {
                    $agentSms = new Sms($agentPhone);
                    $agentMessage = "New withdrawal request\n";
                    $agentMessage .= "Amount: ".Util::formatAmount($amount)."\n";
                    $agentMessage .= "From: ".$this->phoneNumber."\n";
                    $agentMessage .= "Ref: $reference";
                    $agentSms->sendSMS($agentMessage, $agentPhone);
                }
                
                echo "END Withdrawal request submitted\nRef: $reference\nVisit agent to complete";
            } catch(Exception $e) {
                echo "END Withdrawal failed. Try again.";
            }
        }
    }

    public function menuCheckBalance($textArray) {
        $level = count($textArray);

        if($level == 1) {
            echo "CON Enter your PIN:";
        } elseif($level == 2) {
            $pin = $textArray[1];
            
            if($this->isAgent()) {
                // Agent balance check
                if(!$this->verifyAgentPin($this->phoneNumber, $pin)) {
                    echo "END Invalid agent PIN";
                    return;
                }
                
                $balance = $this->getAgentBalance($this->phoneNumber);
                $response = "END Your agent balance: ".Util::formatAmount($balance)."\n";
                $response .= "Commission rate: ".Util::AGENT_COMMISSION." per transaction";
                
                // Send SMS to agent
                $sms = new Sms($this->phoneNumber);
                $smsMessage = "Your agent balance: ".Util::formatAmount($balance);
                $sms->sendSMS($smsMessage, $this->phoneNumber);
                
                echo $response;
            } else {
                // User balance check
                if(!$this->verifyUserPin($this->phoneNumber, $pin)) {
                    echo "END Invalid user PIN";
                    return;
                }

                $balance = $this->getUserBalance($this->phoneNumber);
                $response = "END Your balance: ".Util::formatAmount($balance);
                
                // Send SMS to user
                $sms = new Sms($this->phoneNumber);
                $smsMessage = "Your balance: ".Util::formatAmount($balance);
                $smsResult = $sms->sendSMS($smsMessage, $this->phoneNumber);
                
                if($smsResult['status'] == "success" || $smsResult['status'] == "Success") {
                    echo $response;
                } else {
                    echo "END Balance info displayed. SMS notification failed.";
                }
            }
        }
    }

    public function menuAgentApprove($textArray) {
        $level = count($textArray);

        if($level == 1) {
            echo "CON Enter withdrawal reference:";
        } elseif($level == 2) {
            $reference = $textArray[1];
            $withdrawal = $this->getPendingWithdrawal($reference);
            
            if(!$withdrawal) {
                echo "END Invalid reference";
                return;
            }

            echo "CON Approve ".Util::formatAmount($withdrawal['amount'])." for ".$withdrawal['user_phone']."?\n1. Yes\n2. No";
        } elseif($level == 3 && $textArray[2] == 1) {
            $reference = $textArray[1];
            try {
                $this->processWithdrawal($reference);
                
                // Get updated withdrawal info
                $withdrawal = $this->getTransactionByReference($reference);
                
                // Send SMS to user
                $userSms = new Sms($withdrawal['user_phone']);
                $userMessage = "Your withdrawal of ".Util::formatAmount($withdrawal['amount'])." has been approved\n";
                $userMessage .= "Agent: ".$withdrawal['agent_code']."\n";
                $userMessage .= "Ref: $reference\n";
                $userMessage .= "New balance: ".Util::formatAmount($this->getUserBalance($withdrawal['user_phone']));
                $userSms->sendSMS($userMessage, $withdrawal['user_phone']);
                
                // Send SMS to agent
                $agentPhone = $this->getAgentPhone($withdrawal['agent_code']);
                if($agentPhone) {
                    $agentSms = new Sms($agentPhone);
                    $agentMessage = "Withdrawal completed\n";
                    $agentMessage .= "Amount: ".Util::formatAmount($withdrawal['amount'])."\n";
                    $agentMessage .= "For: ".$withdrawal['user_phone']."\n";
                    $agentMessage .= "Ref: $reference\n";
                    $agentMessage .= "Commission earned: ".Util::formatAmount(Util::AGENT_COMMISSION);
                    $agentSms->sendSMS($agentMessage, $agentPhone);
                }
                
                echo "END Withdrawal approved";
            } catch(Exception $e) {
                echo "END Approval failed: ".$e->getMessage();
            }
        } else {
            echo "END Operation cancelled";
        }
    }

    public function menuAgentTransactions($textArray) {
        $level = count($textArray);
        
        if($level == 1) {
            echo "CON Enter your PIN:";
        } elseif($level == 2) {
            $pin = $textArray[1];
            
            if(!$this->verifyAgentPin($this->phoneNumber, $pin)) {
                echo "END Invalid agent PIN";
                return;
            }
            
            $transactions = $this->getAgentTransactions($this->phoneNumber);
            $response = "END Your recent transactions:\n";
            
            foreach($transactions as $tx) {
                $response .= $tx['type']." ".Util::formatAmount($tx['amount'])." (".$tx['status'].")\n";
                $response .= "Ref: ".$tx['reference']."\n";
                $response .= "Date: ".$tx['created_at']."\n\n";
            }
            
            echo $response;
        }
    }

    private function processWithdrawal($reference) {
        $this->pdo->beginTransaction();

        try {
            // Get and lock withdrawal record
            $withdrawal = $this->getPendingWithdrawal($reference, true);
            if(!$withdrawal) throw new Exception("Invalid withdrawal");

            // Verify agent is authorized
            if(!$this->isAgentAuthorized($this->phoneNumber, $withdrawal['agent_code'])) {
                throw new Exception("Unauthorized agent");
            }

            // Deduct from user (amount + fee)
            $stmt = $this->pdo->prepare("
                UPDATE users SET balance = balance - ? 
                WHERE phone_number = ? AND balance >= ?
            ");
            $stmt->execute([
                $withdrawal['amount'] + $withdrawal['fee'],
                $withdrawal['user_phone'],
                $withdrawal['amount'] + $withdrawal['fee']
            ]);
            if($stmt->rowCount() == 0) throw new Exception("Insufficient balance");

            // Add commission to agent
            $stmt = $this->pdo->prepare("
                UPDATE agents SET balance = balance + ? 
                WHERE agent_code = ?
            ");
            $stmt->execute([
                Util::AGENT_COMMISSION,
                $withdrawal['agent_code']
            ]);

            // Update transaction status
            $stmt = $this->pdo->prepare("
                UPDATE transactions 
                SET status = 'completed' 
                WHERE reference = ?
            ");
            $stmt->execute([$reference]);

            $this->pdo->commit();
        } catch(Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function getPendingWithdrawal($reference, $forUpdate = false) {
        $sql = "
            SELECT * FROM transactions 
            WHERE reference = ? AND type = 'withdraw' AND status = 'pending'
        ";
        if($forUpdate) $sql .= " FOR UPDATE";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$reference]);
        return $stmt->fetch();
    }

    private function getTransactionByReference($reference) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM transactions 
            WHERE reference = ?
        ");
        $stmt->execute([$reference]);
        return $stmt->fetch();
    }

    private function getAgentTransactions($agentPhone) {
        $stmt = $this->pdo->prepare("
            SELECT t.* FROM transactions t
            JOIN agents a ON t.agent_code = a.agent_code
            WHERE a.phone_number = ?
            ORDER BY t.created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$agentPhone]);
        return $stmt->fetchAll();
    }

    private function getAgentPhone($agentCode) {
        $stmt = $this->pdo->prepare("
            SELECT phone_number FROM agents 
            WHERE agent_code = ? AND approved = 1
        ");
        $stmt->execute([$agentCode]);
        $agent = $stmt->fetch();
        return $agent ? $agent['phone_number'] : null;
    }

    private function verifyAgentPin($phone, $pin) {
        $stmt = $this->pdo->prepare("
            SELECT pin_hash FROM agents 
            WHERE phone_number = ? AND approved = 1
        ");
        $stmt->execute([$phone]);
        $agent = $stmt->fetch();
        return $agent && Util::verifyPin($pin, $agent['pin_hash']);
    }

    private function getAgentBalance($phone) {
        $stmt = $this->pdo->prepare("
            SELECT balance FROM agents 
            WHERE phone_number = ? AND approved = 1
        ");
        $stmt->execute([$phone]);
        $agent = $stmt->fetch();
        return $agent ? $agent['balance'] : 0;
    }

    private function isAgentAuthorized($agentPhone, $agentCode) {
        $stmt = $this->pdo->prepare("
            SELECT id FROM agents 
            WHERE phone_number = ? AND agent_code = ? AND approved = 1
        ");
        $stmt->execute([$agentPhone, $agentCode]);
        return $stmt->fetch() !== false;
    }

    private function verifyUserPin($phone, $pin) {
        $stmt = $this->pdo->prepare("
            SELECT pin_hash FROM users 
            WHERE phone_number = ?
        ");
        $stmt->execute([$phone]);
        $user = $stmt->fetch();
        return $user && Util::verifyPin($pin, $user['pin_hash']);
    }

    private function userExists($phone) {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE phone_number = ?");
        $stmt->execute([$phone]);
        return $stmt->fetch() !== false;
    }

    private function agentExists($agentCode) {
        $stmt = $this->pdo->prepare("
            SELECT id FROM agents 
            WHERE agent_code = ? AND approved = 1
        ");
        $stmt->execute([$agentCode]);
        return $stmt->fetch() !== false;
    }

    private function hasSufficientBalance($phone, $amount) {
        $stmt = $this->pdo->prepare("
            SELECT balance FROM users 
            WHERE phone_number = ? AND balance >= ?
        ");
        $stmt->execute([$phone, $amount]);
        return $stmt->fetch() !== false;
    }

    private function getUserBalance($phone) {
        $stmt = $this->pdo->prepare("
            SELECT balance FROM users 
            WHERE phone_number = ?
        ");
        $stmt->execute([$phone]);
        $user = $stmt->fetch();
        return $user ? $user['balance'] : 0;
    }

    public function middleware($text) {
        return $this->goBack($this->goToMainMenu($text));
    }

    private function goBack($text) {
        $exploded = explode("*", $text);
        while(($index = array_search(Util::GO_BACK, $exploded)) !== false) {
            array_splice($exploded, $index - 1, 2);
        }
        return implode("*", $exploded);
    }

    private function goToMainMenu($text) {
        $exploded = explode("*", $text);
        if(($index = array_search(Util::GO_TO_MAIN_MENU, $exploded)) !== false) {
            return implode("*", array_slice($exploded, $index + 1));
        }
        return $text;
    }
}
