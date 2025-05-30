<?php
require_once 'includes/db.php';

$type = $_GET['type'] ?? 'employer';
$email = $_GET['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp'];

    $table = $type === 'candidate' ? 'candidates' : 'employers';

    $stmt = $pdo->prepare("SELECT * FROM $table WHERE email = ? AND otp_code = ?");
    $stmt->execute([$email, $otp]);
    $user = $stmt->fetch();

    if ($user) {
        $pdo->prepare("UPDATE $table SET is_verified = 1 WHERE email = ?")->execute([$email]);
        echo "<script>alert('Verified! Please login.');window.location='login.php';</script>";
        exit;
    } else {
        $error = "Invalid OTP.";
    }
}
?>

<?php include 'templates/header.php'; ?>
<div class="container my-5">
    <h2>Email Verification</h2>
    <p>We've sent an OTP to <strong><?= htmlspecialchars($email) ?></strong></p>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Enter OTP</label>
            <input type="text" name="otp" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Verify</button>
    </form>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger mt-3"><?= $error ?></div>
    <?php endif; ?>
</div>
<?php include 'templates/footer.php'; ?>