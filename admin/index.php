<?php
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../auth/login.php');
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT name FROM users WHERE id = ? AND role = 'admin'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - JobVisaWorld</title>
    <link href="../assets/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Welcome Admin, <?= htmlspecialchars($admin['name']) ?></h2>
    <a href="jobs.php" class="btn btn-primary">Review Jobs</a>
    <a href="users.php" class="btn btn-secondary">Manage Users</a>
    <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
</div>
</body>
</html>
