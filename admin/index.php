<?php
require_once '../Util.php';
session_start();

// Check authentication
if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

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
                VALUES (?, ?, ?, ?, 1, ?)
            ");
            $stmt->execute([
                $agentCode,
                $phone,
                $fullName,
                Util::hashPin($pin),
                Util::AGENT_INITIAL_BALANCE
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

    if (isset($_POST['delete_agent'])) {
        $agentCode = $_POST['agent_code'];

        try {
            // First check if agent has any pending transactions
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM transactions 
                WHERE agent_code = ? AND status = 'pending'
            ");
            $stmt->execute([$agentCode]);
            $pendingTransactions = $stmt->fetchColumn();

            if ($pendingTransactions > 0) {
                $error = "Cannot delete agent with pending transactions!";
            } else {
                $stmt = $pdo->prepare("DELETE FROM agents WHERE agent_code = ?");
                $stmt->execute([$agentCode]);
                
                if ($stmt->rowCount() > 0) {
                    $success = "Agent deleted successfully!";
                } else {
                    $error = "Agent not found!";
                }
            }
        } catch (PDOException $e) {
            $error = "Deletion failed: " . $e->getMessage();
        }
    }
}

// Get all agents
$agents = $pdo->query("
    SELECT * FROM agents 
    ORDER BY created_at DESC
")->fetchAll();

// Get recent transactions
$transactions = $pdo->query("
    SELECT t.*, u.full_name as user_name, a.full_name as agent_name 
    FROM transactions t
    LEFT JOIN users u ON t.user_phone = u.phone_number
    LEFT JOIN agents a ON t.agent_code = a.agent_code
    ORDER BY t.created_at DESC
    LIMIT 50
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f5f5f5; }
    .card-header { background-color: #fff; }
  </style>
</head>
<body>
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3">Admin Dashboard</h1>
      <a href="logout.php" class="btn btn-success text-white">Logout</a>
    </div>

    <?php if (isset($success)): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
      <div class="alert alert-success"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Agent Management -->
    <div class="card mb-5">
      <div class="card-header">
        <h2 class="mb-0">Agent Management</h2>
      </div>
      <div class="card-body bg-white">
        <form method="POST" class="row g-3 mb-4">
          <div class="col-md-3"><input type="text" name="agent_code" class="form-control" placeholder="Agent Code" required></div>
          <div class="col-md-3"><input type="text" name="phone" class="form-control" placeholder="Phone Number" required></div>
          <div class="col-md-3"><input type="text" name="full_name" class="form-control" placeholder="Full Name" required></div>
          <div class="col-md-2"><input type="password" name="pin" class="form-control" placeholder="4-digit PIN" minlength="4" maxlength="4" required></div>
          <div class="col-md-1 d-grid"><button type="submit" name="register_agent" class="btn btn-success">Register</button></div>
        </form>

        <form method="POST" class="row g-3 mb-4">
          <div class="col-md-4"><input type="text" name="agent_code" class="form-control" placeholder="Agent Code" required></div>
          <div class="col-md-4"><input type="number" name="amount" class="form-control" placeholder="Amount to Add" min="0" step="0.01" required></div>
          <div class="col-md-4 d-grid"><button type="submit" name="update_balance" class="btn btn-success">Update Balance</button></div>
        </form>

        <form method="POST" class="row g-3 mb-0" onsubmit="return confirm('Are you sure you want to delete this agent?');">
          <div class="col-md-8"><input type="text" name="agent_code" class="form-control" placeholder="Agent Code to Delete" required></div>
          <div class="col-md-4 d-grid"><button type="submit" name="delete_agent" class="btn btn-success">Delete</button></div>
        </form>
      </div>
    </div>

    <!-- Registered Agents Table -->
    <div class="card mb-5">
      <div class="card-header">
        <h2 class="mb-0">Registered Agents</h2>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Code</th><th>Name</th><th>Phone</th><th>Balance</th><th>Registered At</th><th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($agents as $agent): ?>
              <tr>
                <td><?= htmlspecialchars($agent['agent_code']) ?></td>
                <td><?= htmlspecialchars($agent['full_name']) ?></td>
                <td><?= htmlspecialchars($agent['phone_number']) ?></td>
                <td><?= Util::formatAmount($agent['balance']) ?></td>
                <td><?= htmlspecialchars($agent['created_at']) ?></td>
                <td class="text-nowrap">
                  <form method="POST" class="d-inline">
                    <input type="hidden" name="agent_code" value="<?= htmlspecialchars($agent['agent_code']) ?>">
                    <button name="update_balance" class="btn btn-sm btn-success me-1">Update</button>
                  </form>
                  <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                    <input type="hidden" name="agent_code" value="<?= htmlspecialchars($agent['agent_code']) ?>">
                    <button name="delete_agent" class="btn btn-sm btn-success">Delete</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Transaction History Table -->
    <div class="card">
      <div class="card-header">
        <h2 class="mb-0">Transaction History</h2>
      </div>
      <div class="card-body p-0">
        <form method="GET" class="row g-3 p-3 bg-white">
          <div class="col-md-4">
            <select name="type" class="form-select">
              <option value="">All Types</option>
              <option value="send">Send Money</option>
              <option value="withdraw">Withdrawals</option>
              <option value="deposit">Deposits</option>
            </select>
          </div>
          <div class="col-md-4">
            <select name="status" class="form-select">
              <option value="">All Statuses</option>
              <option value="pending">Pending</option>
              <option value="completed">Completed</option>
              <option value="failed">Failed</option>
            </select>
          </div>
          <div class="col-md-4 d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search ref or phone">
            <button type="submit" class="btn btn-success">Filter</button>
          </div>
        </form>
        <div class="table-responsive">
          <table class="table table-striped mb-0">
            <thead class="table-light">
              <tr>
                <th>Reference</th><th>Type</th><th>User</th><th>Agent</th>
                <th>Amount</th><th>Fee</th><th>Status</th><th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($transactions as $tx): ?>
              <tr>
                <td><?= htmlspecialchars($tx['reference']) ?></td>
                <td><?= htmlspecialchars($tx['type']) ?></td>
                <td>
                  <?= htmlspecialchars($tx['user_phone']) ?>
                  <?= $tx['user_name'] ? '('.htmlspecialchars($tx['user_name']).')' : '' ?>
                </td>
                <td>
                  <?= htmlspecialchars($tx['agent_code'] ?: 'N/A') ?>
                  <?= $tx['agent_name'] ? '('.htmlspecialchars($tx['agent_name']).')' : '' ?>
                </td>
                <td><?= Util::formatAmount($tx['amount']) ?></td>
                <td><?= Util::formatAmount($tx['fee']) ?></td>
                <td><span class="badge bg-success"><?= ucfirst(htmlspecialchars($tx['status'])) ?></span></td>
                <td><?= htmlspecialchars($tx['created_at']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
