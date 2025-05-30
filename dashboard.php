<?php
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    redirect('auth/login.php');
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= ucfirst($role) ?> Dashboard - JobVisaWorld</title>
    <link href="assets/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Welcome, <?= htmlspecialchars($user['name']) ?></h2>
    <p>Email: <?= htmlspecialchars($user['email']) ?></p>
    <p>Role: <?= ucfirst($role) ?></p>

    <?php if ($role === 'employer'): ?>
        <a href="employer/post-job.php" class="btn btn-primary mb-2">Post New Job</a>
        <a href="employer/jobs.php" class="btn btn-secondary mb-2">Manage My Jobs</a>
        <a href="employer/subscription.php" class="btn btn-warning mb-2">Subscription & Billing</a>
    <?php elseif ($role === 'candidate'): ?>
        <a href="candidate/search-jobs.php" class="btn btn-primary mb-2">Search Jobs</a>
        <a href="candidate/applications.php" class="btn btn-secondary mb-2">My Applications</a>
        <a href="candidate/saved.php" class="btn btn-success mb-2">Saved Jobs</a>
    <?php endif; ?>

    <br><br>
    <a href="auth/logout.php" class="btn btn-danger">Logout</a>
</div>
</body>
</html>