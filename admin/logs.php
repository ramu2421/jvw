<?php
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../auth/login.php');
}

$result = $conn->query("
    SELECT l.id, l.user_id, u.email, l.action, l.details, l.created_at
    FROM logs l
    JOIN users u ON l.user_id = u.id
    ORDER BY l.created_at DESC
    LIMIT 200
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Logs</title>
    <link rel='stylesheet' href='../assets/bootstrap.min.css'>
</head>
<body>
<div class='container mt-5'>
    <h2>User Activity Logs</h2>
    <table class='table table-bordered'>
        <thead>
            <tr>
                <th>ID</th>
                <th>User Email</th>
                <th>Action</th>
                <th>Details</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($log = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $log['id'] ?></td>
                <td><?= htmlspecialchars($log['email']) ?></td>
                <td><?= htmlspecialchars($log['action']) ?></td>
                <td><?= htmlspecialchars($log['details']) ?></td>
                <td><?= date('d M Y H:i', strtotime($log['created_at'])) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>