<?php
require_once '../config.php';
require_once '../auth/session.php';

// Enforce admin
if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (empty($_POST['csrf_token']) || 
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }

    // Validate inputs
    $job_id = filter_var($_POST['job_id'], FILTER_VALIDATE_INT);
    $action = ($_POST['action'] === 'approve') ? 'approved' : 'rejected';

    if ($job_id) {
        // Update status
        $stmt = $conn->prepare("UPDATE jobs SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $action, $job_id);
        $stmt->execute();
        $stmt->close();

        // If approved and pay-per-post, trigger Stripe charge here
        if ($action === 'approved') {
            // include Stripe logic module (abstracted for clarity)
            require_once '../stripe/charge-job.php';
            charge_job_post($job_id);
        }
    }
}

header('Location: jobs.php');
exit();