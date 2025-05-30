<?php
require_once '../includes/db.php';

$errors = [];
$success = '';

if (!isset($_SESSION['pending_email'])) {
    redirect('register.php');
}

$email = $_SESSION['pending_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = clean_input($_POST['otp'] ?? '');

    if (empty($entered_otp)) {
        $errors[] = "Please enter the OTP.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, otp_code, email_verified FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if ($user['email_verified'] == 1) {
                $success = "Email already verified.";
                unset($_SESSION['pending_email']);
            } elseif ($user['otp_code'] == $entered_otp) {
                $update = $conn->prepare("UPDATE users SET email_verified = 1, otp_code = NULL WHERE id = ?");
                $update->bind_param("i", $user['id']);
                $update->execute();
                $update->close();

                unset($_SESSION['pending_email']);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = getUserRole($user['id']);

                redirect('../dashboard.php');
            } else {
                $errors[] = "Invalid OTP.";
            }
        } else {
            $errors[] = "User not found.";
        }
        $stmt->close();
    }
}

function getUserRole($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($role);
    $stmt->fetch();
    $stmt->close();
    return $role;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Email - JobVisaWorld</title>
    <link href="../assets/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Verify Your Email</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) echo "<div>$error</div>"; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Enter the OTP sent to your email:</label>
            <input type="text" name="otp" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Verify</button>
    </form>
</div>
</body>
</html>