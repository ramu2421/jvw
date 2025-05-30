<?php
require_once '../includes/functions.php';
require_once '../includes/mailer.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../auth/login.php');
}

if (isset($_GET['job_id'])) {
    $job_id = intval($_GET['job_id']);

    $stmt = $conn->prepare("UPDATE jobs SET status = 'rejected' WHERE id = ?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("SELECT j.title, u.email FROM jobs j JOIN users u ON j.employer_id = u.id WHERE j.id = ?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $job = $result->fetch_assoc();
    $stmt->close();

    sendMail($job['email'], "Job Rejected", "Your job post '{$job['title']}' was rejected. Please revise and try again.");
    redirect('dashboard.php');
}
?>