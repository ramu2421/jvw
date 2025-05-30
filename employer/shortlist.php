<?php
require_once '../includes/session.php';
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $employer_id = $_SESSION['user_id'];
  $candidate_id = $_POST['candidate_id'];
  $application_id = $_POST['application_id'];

  // Check if already shortlisted
  $check = $pdo->prepare("SELECT id FROM shortlists WHERE employer_id = ? AND candidate_id = ?");
  $check->execute([$employer_id, $candidate_id]);
  if ($check->rowCount() === 0) {
    $stmt = $pdo->prepare("INSERT INTO shortlists (employer_id, candidate_id, application_id) VALUES (?, ?, ?)");
    $stmt->execute([$employer_id, $candidate_id, $application_id]);
  }

  header("Location: applications.php?success=1");
  exit();
}
?>