<?php
require_once '../includes/db.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'employer') {
    die("Unauthorized access");
}

$app_id = $_POST['app_id'] ?? null;
$action = $_POST['action'] ?? '';

$allowed = ['Approved', 'Rejected'];
if ($app_id && in_array($action, $allowed)) {
    $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
    $stmt->execute([$action, $app_id]);
}

header('Location: dashboard.php');
exit;