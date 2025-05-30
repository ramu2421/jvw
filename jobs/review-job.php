<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$data = $_SESSION['job_post'] ?? null;
if (!$data) exit("No job data found.");

// Insert job into DB with status 'pending'
$stmt = $pdo->prepare("INSERT INTO jobs (user_id, title, description, location, type, status) VALUES (?, ?, ?, ?, ?, 'pending')");
$stmt->execute([
    $_SESSION['user_id'],
    $data['job_title'],
    $data['job_description'],
    $data['location'],
    $data['job_type']
]);

// Clear session job data
unset($_SESSION['job_post']);
header("Location: ../dashboard/employer.php?posted=1");
exit;