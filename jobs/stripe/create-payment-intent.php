<?php
require_once '../../vendor/autoload.php';
\Stripe\Stripe::setApiKey('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');

header('Content-Type: application/json');

$intent = \Stripe\PaymentIntent::create([
    'amount' => 19900,
    'currency' => 'aud',
]);

echo json_encode(['clientSecret' => $intent->client_secret]);