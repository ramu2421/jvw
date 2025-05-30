<?php
require_once '../includes/session.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'employer') {
    header("Location: ../auth/login.php");
    exit;
}

$employer_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE employer_id = ? ORDER BY created_at DESC");
$stmt->execute([$employer_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>
<div class="container py-5">
    <h2 class="mb-4">Notifications</h2>
    <?php if (empty($notifications)): ?>
        <div class="alert alert-info">No notifications yet.</div>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($notifications as $note): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($note['message']) ?>
                    <span class="text-muted small"><?= date('d M Y, h:i A', strtotime($note['created_at'])) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>