<?php
require_once '../includes/db.php';
require_once '../includes/session.php';

// Protect if not employer
if ($_SESSION['user_type'] !== 'employer') {
    header("Location: ../login.php");
    exit;
}

// Fetch employer subscription (if any)
$stmt = $pdo->prepare("SELECT * FROM subscriptions WHERE employer_id = ? AND status = 'active' AND current_period_end > NOW()");
$stmt->execute([$_SESSION['employer_id']]);
$subscription = $stmt->fetch();
$hasSubscription = $subscription ? true : false;

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];

    // Set status = pending for all
    $status = 'pending';

    $stmt = $pdo->prepare("INSERT INTO jobs (employer_id, title, description, location, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['employer_id'], $title, $description, $location, $status]);
    $jobId = $pdo->lastInsertId();

    $_SESSION['job_id'] = $jobId;

    if ($hasSubscription) {
        // Go straight to dashboard (no payment)
        header("Location: ../employer/dashboard.php?job=submitted");
        exit;
    } else {
        // Go to Stripe card saving flow
        header("Location: ../employer/payment.php");
        exit;
    }
}
?>

<?php include '../templates/header.php'; ?>
<div class="container my-5">
    <h2>Post a Job</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Job Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="5" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" required>
        </div>
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <button type="submit" class="btn btn-primary">Submit Job</button>
    </form>
</div>
<?php include '../templates/footer.php'; ?>