<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || $_SESSION['user']['type'] !== 'candidate') {
  header("Location: ../auth/login.php");
  exit;
}

if (!isset($_GET['job_id']) || !is_numeric($_GET['job_id'])) {
  die("Invalid Job ID");
}

$job_id = (int) $_GET['job_id'];
$candidate_id = $_SESSION['user']['id'];

// Check if job exists and is approved
$stmt = $pdo->prepare("SELECT id FROM jobs WHERE id = ? AND status = 'approved'");
$stmt->execute([$job_id]);
$job = $stmt->fetch();
if (!$job) {
  die("Job not found.");
}

// Check if already applied
$stmt = $pdo->prepare("SELECT id FROM job_applications WHERE job_id = ? AND candidate_id = ?");
$stmt->execute([$job_id, $candidate_id]);
if ($stmt->rowCount() > 0) {
  die("You have already applied to this job.");
}

// Insert application
$stmt = $pdo->prepare("INSERT INTO job_applications (job_id, candidate_id) VALUES (?, ?)");
$stmt->execute([$job_id, $candidate_id]);

// ✅ Notify Employer
$job_stmt = $pdo->prepare("SELECT title, user_id FROM jobs WHERE id = ?");
$job_stmt->execute([$job_id]);
$job = $job_stmt->fetch();

$emp_id = $job['user_id'];
$title = $job['title'];
$candidate_name = $_SESSION['user']['name'];

$notify = $pdo->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
$notify->execute([
    $emp_id,
    "$candidate_name has applied to your job: $title"
]);

// After inserting into `applications` table
$jobStmt = $pdo->prepare("SELECT employer_id FROM jobs WHERE id = ?");
$jobStmt->execute([$job_id]);
$job = $jobStmt->fetch();

$note = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
$note->execute([$job['employer_id'], "A new application was received for your job listing."]);

echo "<!DOCTYPE html><html><head><title>Applied</title>
<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'></head><body>
<div class='container py-5'>
  <h3 class='text-success'>Application Submitted!</h3>
  <p>You’ve successfully applied to this job.</p>
  <a href='../index.php' class='btn btn-primary mt-3'>Back to Home</a>
</div></body></html>";