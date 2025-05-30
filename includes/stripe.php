<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';
\Stripe\Stripe::setApiKey(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);

function createSubscriptionIntent(\$customerId, \$priceId) {
    return \Stripe\Subscription::create([
        'customer' => \$customerId,
        'items' => [['price' => \$priceId]],
        'payment_behavior' => 'default_incomplete',
        'expand' => ['latest_invoice.payment_intent'],
    ]);
}

function chargePerPost(\$customerId, \$amountCents) {
    return \Stripe\PaymentIntent::create([
        'amount' => \$amountCents,
        'currency' => 'usd',
        'customer' => \$customerId,
    ]);
}