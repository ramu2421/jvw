<?php
require_once '../includes/db.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    redirect('../dashboard.php');
}

// Handle login submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $errors[] = 'Invalid CSRF token.';
    }

    $email = clean_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = clean_input($_POST['role'] ?? '');

    if (empty($email) || empty($password) || empty($role)) {
        $errors[] = 'All fields are required.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, password, role, email_verified FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                if ($user['email_verified'] != 1) {
                    $errors[] = "Please verify your email before logging in.";
                } elseif ($user['role'] !== $role) {
                    $errors[] = "Invalid role selected.";
                } else {
                    // Login success
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    redirect('../dashboard.php');
                }
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "No account found with that email.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - JobVisaWorld</title>
    <link href="../assets/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Login</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) echo "<div>$error</div>"; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
        <div class="mb-3">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Login as:</label>
            <select name="role" class="form-control" required>
                <option value="">Select Role</option>
                <option value="employer">Employer</option>
                <option value="candidate">Candidate</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
        <p class="text-center mt-3">
            <a href="register.php">Don't have an account? Register</a>
        </p>
    </form>
</div>
</body>
</html>