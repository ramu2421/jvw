<?php
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../auth/login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $template_name = trim($_POST['template_name']);
    $subject = trim($_POST['subject']);
    $body = trim($_POST['body']);

    $stmt = $conn->prepare("INSERT INTO email_templates (template_name, subject, body) VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE subject = VALUES(subject), body = VALUES(body)");
    $stmt->bind_param("sss", $template_name, $subject, $body);
    $stmt->execute();
    $success = "Template saved successfully.";
}

$result = $conn->query("SELECT * FROM email_templates ORDER BY template_name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Email Templates</title>
    <link rel='stylesheet' href='../assets/bootstrap.min.css'>
</head>
<body>
<div class='container mt-5'>
    <h2>Email Templates</h2>
    <?php if (isset($success)): ?>
        <div class='alert alert-success'><?= $success ?></div>
    <?php endif; ?>
    <form method='post' class='mb-4'>
        <div class='mb-3'>
            <input type='text' name='template_name' class='form-control' placeholder='Template Name (e.g., welcome_email)' required>
        </div>
        <div class='mb-3'>
            <input type='text' name='subject' class='form-control' placeholder='Email Subject' required>
        </div>
        <div class='mb-3'>
            <textarea name='body' class='form-control' placeholder='Email Body (HTML allowed)' rows='6' required></textarea>
        </div>
        <button type='submit' class='btn btn-primary'>Save Template</button>
    </form>
    <table class='table table-bordered'>
        <thead>
            <tr>
                <th>Template Name</th>
                <th>Subject</th>
                <th>Body (Preview)</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($template = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($template['template_name']) ?></td>
                <td><?= htmlspecialchars($template['subject']) ?></td>
                <td><?= htmlspecialchars(substr($template['body'], 0, 100)) ?>...</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>