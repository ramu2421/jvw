<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
include 'includes/header.php';

$employer_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
  SELECT s.*, u.full_name, u.email, u.resume
  FROM shortlists s
  JOIN users u ON s.candidate_id = u.id
  WHERE s.employer_id = ?
  ORDER BY s.created_at DESC
");
$stmt->execute([$employer_id]);
$candidates = $stmt->fetchAll();
?>

<div class="container mt-4">
  <h4>Shortlisted Candidates</h4>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Candidate Name</th>
        <th>Email</th>
        <th>Resume</th>
        <th>Shortlisted On</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($candidates as $c): ?>
      <tr>
        <td><?= htmlspecialchars($c['full_name']) ?></td>
        <td><?= htmlspecialchars($c['email']) ?></td>
        <td>
          <?php if ($c['resume']): ?>
            <a href="../uploads/resumes/<?= $c['resume'] ?>" target="_blank">Download</a>
          <?php else: ?>
            N/A
          <?php endif; ?>
        </td>
        <td><?= date('d M Y, h:i A', strtotime($c['created_at'])) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include 'includes/footer.php'; ?>