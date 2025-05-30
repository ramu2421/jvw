<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

if ($_SESSION['role'] !== 'employer') exit("Unauthorized");

// Save posted job data to session temporarily
$_SESSION['job_post'] = $_POST;

// Now check if employer has active subscription
$stmt = $pdo->prepare("SELECT * FROM subscriptions WHERE user_id = ? AND expires_at >= NOW()");
$stmt->execute([$_SESSION['user_id']]);
$subscription = $stmt->fetch();

if ($subscription) {
    // Go to review page directly
    header("Location: review-job.php");
} else {
    // Go to payment page (Stripe)
    header("Location: payment.php");
}
exit;