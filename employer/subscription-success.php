<?php
require_once '../includes/db.php';
require_once '../includes/session.php';
require_once '../vendor/autoload.php';

\Stripe\Stripe::setApiKey('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');

$sessionId = $_GET['session_id'];
$session = \Stripe\Checkout\Session::retrieve($sessionId);
$subscriptionId = $session->subscription;

$subscription = \Stripe\Subscription::retrieve($subscriptionId);
$current_period_end = date('Y-m-d H:i:s', $subscription->current_period_end);

$pdo->prepare("INSERT INTO subscriptions (employer_id, stripe_customer_id, stripe_subscription_id, status, current_period_end) VALUES (?, ?, ?, ?, ?)")
    ->execute([
        $_SESSION['employer_id'],
        $session->customer,
        $subscriptionId,
        $subscription->status,
        $current_period_end
    ]);

header("Location: dashboard.php?subscription=active");
exit;