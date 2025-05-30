<?php
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../auth/login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = trim($_POST['site_name']);
    $admin_email = trim($_POST['admin_email']);
    $items_per_page = intval($_POST['items_per_page']);

    $stmt = $conn->prepare("UPDATE settings SET site_name = ?, admin_email = ?, items_per_page = ? WHERE id = 1");
    $stmt->bind_param("ssi", $site_name, $admin_email, $items_per_page);
    $stmt->execute();
    $success = "Settings updated successfully.";
}

$stmt = $conn->prepare("SELECT * FROM settings WHERE id = 1");
$stmt->execute();
$result = $stmt->get_result();
$settings = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Settings</title>
    <link rel='stylesheet' href='../assets/bootstrap.min.css'>
</head>
<body>
<div class='container mt-5'>
    <h2>General Settings</h2>
    <?php if (isset($success)): ?>
        <div class='alert alert-success'><?= $success ?></div>
    <?php endif; ?>
    <form method='post'>
        <div class='mb-3'>
            <label class='form-label'>Site Name</label>
            <input type='text' name='site_name' class='form-control' value='<?= htmlspecialchars($settings['site_name']) ?>' required>
        </div>
        <div class='mb-3'>
            <label class='form-label'>Admin Email</label>
            <input type='email' name='admin_email' class='form-control' value='<?= htmlspecialchars($settings['admin_email']) ?>' required>
        </div>
        <div class='mb-3'>
            <label class='form-label'>Items Per Page</label>
            <input type='number' name='items_per_page' class='form-control' value='<?= intval($settings['items_per_page']) ?>' required>
        </div>
        <button type='submit' class='btn btn-primary'>Save Settings</button>
    </form>
</div>
</body>
</html>