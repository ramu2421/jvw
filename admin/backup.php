<?php
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../auth/login.php');
}

$backup_message = '';

if (isset($_GET['action']) && $_GET['action'] === 'download') {
    $backup_file = '../backup/jobportal_backup_' . date('Ymd_His') . '.sql';
    $dbhost = DB_HOST;
    $dbuser = DB_USER;
    $dbpass = DB_PASS;
    $dbname = DB_NAME;

    $command = "mysqldump --host=$dbhost --user=$dbuser --password=$dbpass $dbname > $backup_file";
    system($command, $output);

    if (file_exists($backup_file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($backup_file) . '"');
        header('Content-Length: ' . filesize($backup_file));
        readfile($backup_file);
        unlink($backup_file);
        exit;
    } else {
        $backup_message = 'Backup failed or mysqldump not available on server.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>System Backup</title>
    <link rel='stylesheet' href='../assets/bootstrap.min.css'>
</head>
<body>
<div class='container mt-5'>
    <h2>System Backup</h2>
    <?php if ($backup_message): ?>
        <div class='alert alert-danger'><?= $backup_message ?></div>
    <?php endif; ?>
    <a href='?action=download' class='btn btn-success'>Download Database Backup</a>
    <p class='mt-3 text-muted'>Note: This requires <code>mysqldump</code> to be available on the server.</p>
</div>
</body>
</html>