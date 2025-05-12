<?php
require_once '../Util.php';
// session_start();

// // Simple authentication
// if(!isset($_SESSION['admin_logged_in'])) {
//     header('Location: login.php');
//     exit;
// }

$util = new Util();
$pdo = $util->getConnection();

// Handle Agent Registration
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register_agent'])) {
        $agentCode = $_POST['agent_code'];
        $phone = $_POST['phone'];
        $fullName = $_POST['full_name'];
        $pin = $_POST['pin'];

        try {
            $stmt = $pdo->prepare("
                INSERT INTO agents 
                (agent_code, phone_number, full_name, pin_hash, approved, balance)
                VALUES (?, ?, ?, ?, 1, 0)
            ");
            $stmt->execute([
                $agentCode,
                $phone,
                $fullName,
                Util::hashPin($pin)
            ]);
            $success = "Agent registered successfully!";
        } catch (PDOException $e) {
            $error = "Registration failed: " . $e->getMessage();
        }
    }




    if (isset($_POST['update_balance'])) {
        $agentCode = $_POST['agent_code'];
        $amount = $_POST['amount'];

   
        if ($amount < 0) {
            $error = "Amount to add cannot be negative.";
        } else {
            try {
                $stmt = $pdo->prepare("
                    UPDATE agents 
                    SET balance = balance + ? 
                    WHERE agent_code = ?
                ");
                $stmt->execute([$amount, $agentCode]);
                $success = "Balance updated successfully for agent $agentCode!";
            } catch (PDOException $e) {
                $error = "Balance update failed: " . $e->getMessage();
            }
        }
    }
}



// Get all agents
$agents = $pdo->query("
    SELECT * FROM agents 
    ORDER BY created_at DESC
")->fetchAll();


?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Panel</title>
</head>

<body>
    <h1>Agent Management</h1>

    <?php if (isset($success)): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <h2>Register New Agent</h2>
    <form method="POST">
        <input type="text" name="agent_code" placeholder="Agent Code" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="password" name="pin" placeholder="4-digit PIN" required>
        <button type="submit" name="register_agent">Register Agent</button>
    </form>

    <h2>Update Agent's Balance</h2>
    <form method="POST">
        <input type="text" name="agent_code" placeholder="Agent Code" required>
        <input type="number" name="amount" placeholder="Amount to Add" required>
        <button type="submit" name="update_balance">Update Balance</button>
    </form>



    <h2>Registered Agents</h2>
    <table>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Balance</th>
            <th>Date Registered</th>
        </tr>
        <?php foreach ($agents as $agent): ?>
        <tr>
            <td><?= htmlspecialchars($agent['agent_code']) ?></td>
            <td><?= htmlspecialchars($agent['full_name']) ?></td>
            <td><?= htmlspecialchars($agent['phone_number']) ?></td>
            <td><?= Util::formatAmount($agent['balance']) ?></td>
            <td><?= htmlspecialchars($agent['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>


</body>

</html>