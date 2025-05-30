<?php
require_once '../includes/db.php';
require_once '../stripe/stripe-php/init.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../auth/login.php');
}

$job_id = clean_input($_POST['job_id']);
$action = clean_input($_POST['action']);

// Fetch job details
$stmt = $conn->prepare("SELECT j.*, u.stripe_customer_id FROM jobs j JOIN users u ON j.employer_id = u.id WHERE j.id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();
$stmt->close();

// Update job status
$status = ($action === 'approve') ? 'approved' : 'rejected';
$stmt = $conn->prepare("UPDATE jobs SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $job_id);
$stmt->execute();
$stmt->close();

// Charge via Stripe ONLY if job is pay-per-post and status is approved
if ($status === 'approved' && $job['payment_type'] === 'pay_per_post' && !$job['paid']) {
    \Stripe\Stripe::setApiKey(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);

    try {
        $charge = \Stripe\PaymentIntent::create([
            'amount' => $job['19900'], // stored in DB
            'currency' => $job['currency'],
            'customer' => $job['stripe_customer_id'],
            'payment_method' => $job['payment_method_id'],
            'off_session' => true,
            'confirm' => true
