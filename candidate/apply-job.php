<?php
require_once '../includes/functions.php';
require_once '../includes/mailer.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'candidate') {
    redirect('../auth/login.php');
}

if (isset($_GET['job_id'])) {
    $job_id = intval($_GET['job_id']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO job_applications (job_id, candidate_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $job_id, $user_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("SELECT u.email, j.title FROM jobs j JOIN users u ON j.employer_id = u.id WHERE j.id = ?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $job = $result->fetch_assoc();
    $stmt->close();

    sendMail($job['email'], "New Job Application", "Someone has applied to your job post '{$job['title']}'");
    redirect('dashboard.php');
}
?>