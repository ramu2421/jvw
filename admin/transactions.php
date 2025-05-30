<?php
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../auth/login.php');
}

$stmt = $conn->prepare("
    SELECT t.id, t.user_id, t.amount, t.currency, t.type, t.status, t.created_at, u.email
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Transactions</title>
    <link rel='stylesheet' href='../assets/bootstrap.min.css'>
</head>
<body>
<div class='container mt-5'>
    <h2>Transaction History</h2>
    <table class='table table-bordered'>
        <thead>
            <tr>
                <th>ID</th>
                <th>User Email</th>
                <th>Amount</th>
                <th>Currency</th>
                <th>Type</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($tx = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $tx['id'] ?></td>
                <td><?= htmlspecialchars($tx['email']) ?></td>
                <td><?= number_format($tx['amount'], 2) ?></td>
                <td><?= strtoupper($tx['currency']) ?></td>
                <td><?= htmlspecialchars($tx['type']) ?></td>
                <td><?= htmlspecialchars($tx['status']) ?></td>
                <td><?= date('d M Y H:i', strtotime($tx['created_at'])) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>