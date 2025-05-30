<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("Invalid job ID");
}

$job_id = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ? AND status = 'approved'");
$stmt->execute([$job_id]);
$job = $stmt->fetch();

if (!$job) {
  die("Job not found or not approved.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($job['title']) ?> - JobVisaWorld</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="<?= htmlspecialchars(substr($job['description'], 0, 150)) ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<section class="py-5 bg-light">
  <div class="container">
    <h2><?= htmlspecialchars($job['title']) ?></h2>
    <p class="text-muted">Posted on <?= date('F j, Y', strtotime($job['created_at'])) ?></p>

    <div class="mb-3">
      <strong>Location:</strong> <?= htmlspecialchars($job['location']) ?>
    </div>
    <div class="mb-3">
      <strong>Job Type:</strong> <?= htmlspecialchars($job['job_type']) ?>
    </div>
    <div class="mb-3">
      <strong>Category:</strong> <?= htmlspecialchars($job['category']) ?>
    </div>

    <div class="mb-4">
      <strong>Description:</strong>
      <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
    </div>

    <?php if (isset($_SESSION['user']) && $_SESSION['user']['type'] === 'candidate'): ?>
      <a href="candidate/apply.php?job_id=<?= $job['id'] ?>" class="btn btn-success">Apply Now</a>
    <?php else: ?>
      <p><a href="auth/login.php" class="btn btn-outline-primary">Login to Apply</a></p>
    <?php endif; ?>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>