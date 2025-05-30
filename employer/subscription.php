<?php
require_once '../includes/db.php';
require_once '../includes/session.php';
require_once '../vendor/autoload.php';

\Stripe\Stripe::setApiKey('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'); // Replace with live key

// Create Stripe customer if not exists
$userId = $_SESSION['employer_id'];
$stmt = $pdo->prepare("SELECT * FROM employers WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user['stripe_customer_id']) {
    $customer = \Stripe\Customer::create([
        'email' => $user['email']
    ]);

    $pdo->prepare("UPDATE employers SET stripe_customer_id = ? WHERE id = ?")
        ->execute([$customer->id, $userId]);
    $stripeCustomerId = $customer->id;
} else {
    $stripeCustomerId = $user['stripe_customer_id'];
}

// Stripe Checkout
$priceId = $_GET['plan'] === 'quarterly'
    ? 'prod_SEpDWuj6WnSBUw' // Replace with your actual ID
    : 'prod_SEpC08ucJYlUDS'; // Replace with your actual ID

$session = \Stripe\Checkout\Session::create([
    'customer' => $stripeCustomerId,
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price' => $priceId,
        'quantity' => 1,
    ]],
    'mode' => 'subscription',
    'success_url' => 'https://jobvisaworld.com/jobportal/employer/subscription-success.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'https://jobvisaworld.com/jobportal/employer/dashboard.php',
]);

header("Location: " . $session->url);
exit;