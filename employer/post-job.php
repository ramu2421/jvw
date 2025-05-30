<?php
require_once '../includes/functions.php';
require_once '../stripe/stripe-php/init.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employer') {
    redirect('../auth/login.php');
}

// Get country code from subdomain or session
$country = $_SESSION['country_code'] ?? 'au';
$currencyMap = [
    'au' => 'aud',
    'in' => 'inr',
    'us' => 'usd',
    'uk' => 'gbp'
];
$currency = $currencyMap[$country] ?? 'aud';

$base_amount_aud = 199.00;
$converted = convertCurrency('aud', $currency, $base_amount_aud);
$amount_cents = round($converted * 100);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean_input($_POST['title']);
    $desc = clean_input($_POST['description']);
    $location = clean_input($_POST['location']);

    $paymentType = $_POST['payment_type']; // 'subscription' or 'pay_per_post'
    $employer_id = $_SESSION['user_id'];
    $job_status = 'pending';

    $stmt = $conn->prepare("INSERT INTO jobs (title, description, location, employer_id, status, payment_type, currency, amount_cents, country) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisssis", $title, $desc, $location, $employer_id, $job_status, $paymentType, $currency, $amount_cents, $country);
    $stmt->execute();
    $job_id = $stmt->insert_id;
    $stmt->close();

    if ($paymentType === 'pay_per_post') {
        $user_id = $_SESSION['user_id'];

        // Get Stripe customer ID
        $stmt = $conn->prepare("SELECT stripe_customer_id FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user['stripe_customer_id']) {
            redirect('billing-setup.php');
        }

        // Create PaymentIntent to confirm later after admin approval
        \Stripe\Stripe::setApiKey('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');

        $intent = \Stripe\PaymentIntent::create([
            'amount' => $amount_cents,
            'currency' => $currency,
            'customer' => $user['stripe_customer_id'],
            'setup_future_usage' => 'off_session',
            'description' => "Job Post Payment (Pre-Approved)",
            'metadata' => ['job_id' => $job_id]
        ]);

        // Store PaymentIntent ID
        $stmt = $conn->prepare("UPDATE jobs SET stripe_payment_intent_id = ? WHERE id = ?");
        $stmt->bind_param("si", $intent->id, $job_id);
        $stmt->execute();
        $stmt->close();
    }

    redirect('dashboard.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post Job</title>
    <link href="../assets/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h3>Post a Job</h3>
    <form method="POST">
        <div class="mb-3">
            <label>Job Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Job Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>Location</label>
            <input type="text" name="location" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Payment Type</label>
            <select name="payment_type" class="form-select" required>
                <option value="subscription">Active Subscription</option>
                <option value="pay_per_post">Pay Per Post (<?php echo strtoupper($currency) . ' ' . $converted; ?>)</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Submit Job</button>
    </form>
</div>
</body>
</html>