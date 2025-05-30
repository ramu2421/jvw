<?php
require_once '../includes/db.php';

// Generate OTP
function generateOTP() {
    return rand(100000, 999999);
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $errors[] = 'Invalid CSRF token.';
    }

    $name = clean_input($_POST['name'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = clean_input($_POST['role'] ?? '');

    // Validate inputs
    if (!$name || !$email || !$password || !$confirm_password || !$role) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (!in_array($role, ['employer', 'candidate'])) {
        $errors[] = "Invalid user role.";
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Email is already registered.";
    }
    $stmt->close();

    // If no errors, insert user with OTP
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $otp = generateOTP();

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, email_verified, otp_code) VALUES (?, ?, ?, ?, 0, ?)");
        $stmt->bind_param("ssssi", $name, $email, $hashed_password, $role, $otp);
        if ($stmt->execute()) {
            // Send OTP email
            $to = $email;
            $subject = "Verify Your Email - JobVisaWorld";
            $message = "Your OTP code is: $otp";
            $headers = "From: no-reply@jobvisaworld.com";
            mail($to, $subject, $message, $headers);

            $_SESSION['pending_email'] = $email;
            redirect('verify.php');
        } else {
            $errors[] = "Registration failed. Try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - JobVisaWorld</title>
    <link href="../assets/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Register</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) echo "<div>$error</div>"; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">

        <div class="mb-3">
            <label>Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Register as:</label>
            <select name="role" class="form-control" required>
                <option value="">Select Role</option>
                <option value="employer">Employer</option>
                <option value="candidate">Candidate</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">Register</button>
        <p class="text-center mt-3">
            <a href="login.php">Already have an account? Login</a>
        </p>
    </form>
</div>
</body>
</html>