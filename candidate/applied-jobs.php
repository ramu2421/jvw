<?php
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'candidate') {
    redirect('../auth/login.php');
}

$candidate_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT j.id, j.title, j.company, j.location, j.created_at, a.applied_at
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    WHERE a.candidate_id = ?
    ORDER BY a.applied_at DESC
");
$stmt->bind_param("i", $candidate_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Applied Jobs</title>
    <link rel='stylesheet' href='../assets/bootstrap.min.css'>
</head>
<body>
<div class='container mt-5'>
    <h2>Applied Jobs</h2>
    <div class='list-group'>
        <?php while ($job = $result->fetch_assoc()): ?>
            <a href='../job/view.php?id=<?= $job['id'] ?>' class='list-group-item list-group-item-action'>
                <h5><?= htmlspecialchars($job['title']) ?> - <?= htmlspecialchars($job['company']) ?></h5>
                <p><?= htmlspecialchars($job['location']) ?> | Applied on <?= date('d M Y', strtotime($job['applied_at'])) ?></p>
            </a>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>