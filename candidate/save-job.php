<?php
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'candidate') {
    redirect('../auth/login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['job_id'])) {
    $job_id = intval($_POST['job_id']);
    $candidate_id = $_SESSION['user_id'];

    // Prevent duplicate saves
    $stmt = $conn->prepare("SELECT id FROM saved_jobs WHERE job_id = ? AND candidate_id = ?");
    $stmt->bind_param("ii", $job_id, $candidate_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO saved_jobs (job_id, candidate_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $job_id, $candidate_id);
        $stmt->execute();
    } else {
        $stmt->close();
    }

    redirect('../candidate/dashboard.php');
}
?>