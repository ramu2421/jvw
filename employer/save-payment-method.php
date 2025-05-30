<?php
require_once '../includes/db.php';
require_once '../includes/session.php';

$data = json_decode(file_get_contents("php://input"), true);
$paymentMethod = $data['payment_method'] ?? null;

if ($paymentMethod && isset($_SESSION['job_id'])) {
    $stmt = $pdo->prepare("UPDATE jobs SET payment_method = ?, status = 'pending' WHERE id = ?");
    $stmt->execute([$paymentMethod, $_SESSION['job_id']]);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}