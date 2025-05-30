<?php
require_once '../vendor/autoload.php';
require_once '../includes/db.php';

\Stripe\Stripe::setApiKey('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'); // Replace with your actual secret key

$endpoint_secret = 'whsec_2SEN1t6XPZCtIesAsnFy7LkY996bR51m'; // Replace with your webhook secret

$payload = @file_get_contents("php://input");
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$event = null;

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
    );
} catch (\UnexpectedValueException $e) {
    // Invalid payload
    http_response_code(400);
    exit();
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    http_response_code(400);
    exit();
}

// Optional: Log payload (disable in production)
file_put_contents("webhook_log.txt", $payload . PHP_EOL, FILE_APPEND);

switch ($event->type) {

    case 'checkout.session.completed':
        $session = $event->data->object;
        $payment_status = $session->payment_status ?? '';
        $metadata = $session->metadata;

        if ($metadata && isset($metadata->type)) {
            $type = $metadata->type;

            if ($type === 'pay_per_post') {
                $job_id = $metadata->job_id;

                // Mark job as paid and approved
                $stmt = $pdo->prepare("UPDATE jobs SET is_paid = 1, status = 'approved' WHERE id = ?");
                $stmt->execute([$job_id]);

                $stmtJob = $pdo->prepare("SELECT user_id FROM jobs WHERE id = ?");
                $stmtJob->execute([$job_id]);
                $job = $stmtJob->fetch();

                $note = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
                $note->execute([$job['user_id'], 'Your job post was approved and payment was received.']);
            }

            elseif ($type === 'subscription') {
                $user_id = $metadata->user_id;
                $sub_id = $session->subscription;

                $stmt = $pdo->prepare("UPDATE users SET subscription_status = 'active', stripe_subscription_id = ? WHERE id = ?");
                $stmt->execute([$sub_id, $user_id]);

                $note = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
                $note->execute([$user_id, 'Your subscription has been activated.']);
            }
        }
        break;

    case 'invoice.payment_failed':
        $subscription = $event->data->object;
        $sub_id = $subscription->subscription;

        $stmt = $pdo->prepare("UPDATE users SET subscription_status = 'failed' WHERE stripe_subscription_id = ?");
        $stmt->execute([$sub_id]);
        break;

    case 'customer.subscription.deleted':
        $subscription = $event->data->object;
        $sub_id = $subscription->id;

        $stmt = $pdo->prepare("UPDATE users SET subscription_status = 'cancelled' WHERE stripe_subscription_id = ?");
        $stmt->execute([$sub_id]);
        break;
}

http_response_code(200);