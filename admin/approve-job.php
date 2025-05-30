<?php
require_once '../includes/functions.php';
require_once '../includes/mailer.php';
require_once '../stripe/stripe-php/init.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../auth/login.php');
}

if (isset($_GET['job_id'])) {
    $job_id = intval($_GET['job_id']);

    $stmt = $conn->prepare("UPDATE jobs SET status = 'approved' WHERE id = ?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("SELECT j.title, u.email, j.payment_type, j.stripe_payment_intent_id FROM jobs j JOIN users u ON j.employer_id = u.id WHERE j.id = ?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $job = $result->fetch_assoc();
    $stmt->close();

    // Charge if pay-per-post
    if ($job['payment_type'] === 'pay_per_post' && $job['stripe_payment_intent_id']) {
        \Stripe\Stripe::setApiKey('YOUR_SECRET_KEY');
        \Stripe\PaymentIntent::retrieve($job['stripe_payment_intent_id'])->confirm();
    }

    sendMail($job['email'], "Job Approved", "Your job post '{$job['title']}' has been approved and is now live.");
    redirect('dashboard.php');
}
?>