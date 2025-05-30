<?php
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../auth/login.php');
}

$stmt = $conn->prepare("
    SELECT n.id, n.user_id, n.message, n.created_at, u.email
    FROM notifications n
    JOIN users u ON n.user_id = u.id
    ORDER BY n.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Notifications</title>
    <link rel='stylesheet' href='../assets/bootstrap.min.css'>
</head>
<body>
<div class='container mt-5'>
    <h2>Notifications</h2>
    <div class='list-group'>
        <?php while ($note = $result->fetch_assoc()): ?>
            <div class='list-group-item'>
                <strong><?= htmlspecialchars($note['email']) ?></strong>: <?= htmlspecialchars($note['message']) ?>
                <span class='float-end'><?= date('d M Y H:i', strtotime($note['created_at'])) ?></span>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>