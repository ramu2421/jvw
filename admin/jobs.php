<?php
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../auth/login.php');
}

$result = $conn->query("SELECT j.*, u.name AS employer FROM jobs j JOIN users u ON j.employer_id = u.id WHERE j.status = 'pending' ORDER BY j.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Jobs - Admin</title>
    <link href="../assets/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Pending Job Listings</h3>
    <?php while ($job = $result->fetch_assoc()): ?>
        <div class="border p-3 mb-3">
            <h5><?= htmlspecialchars($job['title']) ?> (<?= htmlspecialchars($job['country']) ?>)</h5>
            <p><strong>Employer:</strong> <?= htmlspecialchars($job['employer']) ?></p>
            <form method="post" action="process_job.php">
                <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                <button name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                <button name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>