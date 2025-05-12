<?php
require_once '../Util.php';
session_start();

if(isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$util = new Util();
$pdo = $util->getConnection();

// Create default admin if not exists
$stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
if($stmt->fetchColumn() == 0) {
    $defaultPassword = 'admin123';
    $stmt = $pdo->prepare("
        INSERT INTO admin_users (username, password_hash) 
        VALUES (?, ?)
    ");
    $stmt->execute([
        'admin', 
        password_hash($defaultPassword, PASSWORD_DEFAULT)
    ]);
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Get admin user from database
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        
        // Update last login
        $stmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$admin['id']]);
        
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 60px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        .btn-login {
            background-color: #4CAF50;
            border: none;
        }
        .btn-login:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2 class="mb-4 text-center">XYZ Admin Login</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-login w-100 text-white">Login</button>
    </form>
</div>

</body>
</html>
