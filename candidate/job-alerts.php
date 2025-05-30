<?php
include '../includes/session.php';
include '../includes/db.php';
include 'includes/header.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $keyword = $_POST['keyword'];
  $location = $_POST['location'];

  $stmt = $pdo->prepare("INSERT INTO job_alerts (user_id, keyword, location) VALUES (?, ?, ?)");
  $stmt->execute([$user_id, $keyword, $location]);

  echo "<div class='alert alert-success'>Alert added successfully!</div>";
}

$stmt = $pdo->prepare("SELECT * FROM job_alerts WHERE user_id = ?");
$stmt->execute([$user_id]);
$alerts = $stmt->fetchAll();
?>

<div class="container mt-4">
  <h4>Manage Job Alerts</h4>
  <form method="POST" class="mb-4">
    <div class="row">
      <div class="col-md-5">
        <input type="text" name="keyword" class="form-control" placeholder="Keyword" required>
      </div>
      <div class="col-md-5">
        <input type="text" name="location" class="form-control" placeholder="Location" required>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary">Add Alert</button>
      </div>
    </div>
  </form>

  <ul class="list-group">
    <?php foreach ($alerts as $alert): ?>
      <li class="list-group-item">
        <strong><?= htmlspecialchars($alert['keyword']) ?></strong> – <?= htmlspecialchars($alert['location']) ?>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

<?php include 'includes/footer.php'; ?>