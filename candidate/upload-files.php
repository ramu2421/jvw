<?php
require_once '../includes/session.php';
require_once '../includes/db.php';

$user_id = $_SESSION['user_id'];

$uploadDir = '../uploads/';
$photoPath = '';
$resumePath = '';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Handle profile photo
if (!empty($_FILES['profile_photo']['name'])) {
    $photoName = 'photo_' . $user_id . '_' . time() . '.' . pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
    $photoPath = $uploadDir . $photoName;
    move_uploaded_file($_FILES['profile_photo']['tmp_name'], $photoPath);
    $photoPath = 'uploads/' . $photoName; // for DB
}

// Handle resume
if (!empty($_FILES['resume_file']['name'])) {
    $resumeName = 'resume_' . $user_id . '_' . time() . '.' . pathinfo($_FILES['resume_file']['name'], PATHINFO_EXTENSION);
    $resumePath = $uploadDir . $resumeName;
    move_uploaded_file($_FILES['resume_file']['tmp_name'], $resumePath);
    $resumePath = 'uploads/' . $resumeName; // for DB
}

// Update database
$stmt = $pdo->prepare("UPDATE users SET profile_photo = ?, resume_file = ? WHERE id = ?");
$stmt->execute([$photoPath, $resumePath, $user_id]);

header("Location: dashboard.php?uploaded=1");
exit;
?>