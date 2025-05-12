<?php
class Util {
    const GO_BACK = "98";
    const GO_TO_MAIN_MENU = "99";
    const HOST = "localhost";
    const DBNAME = "momo";
    const USERNAME = "root";
    const PASSWORD = "";

    const USER_BALANCE = 400;
    const AGENT_INITIAL_BALANCE = 50000; // ✅ Agent initial balance
    const TRANSACTION_FEE = 100;
    const AGENT_COMMISSION = 50;

    // Africa's Talking SMS settings
    const AT_USERNAME = "sandbox";
    const AT_API_KEY = "atsk_0777ca10bd8047303f61e49baa170df0eb7c259f0f32e11ec9b4bf5edc46a5f34a9c4af8";
    const SMS_SENDER = "XYZ MOMO Ltd";

    private $pdo;

    public function __construct() {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT => true,
        ];

        try {
            $this->pdo = new PDO(
                "mysql:host=" . self::HOST . ";dbname=" . self::DBNAME,
                self::USERNAME,
                self::PASSWORD,
                $options
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("System temporarily unavailable. Please try again later.");
        }
    }

    public function getConnection() {
        return $this->pdo;
    }

    public static function hashPin($pin) {
        return password_hash($pin, PASSWORD_BCRYPT);
    }

    public static function verifyPin($inputPin, $hashedPin) {
        return password_verify($inputPin, $hashedPin);
    }

    public static function generateReference() {
        return uniqid('TX-');
    }

    public static function formatAmount($amount) {
        return number_format($amount, 2);
    }
}
?>
