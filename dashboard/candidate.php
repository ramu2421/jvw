<?php include '../includes/auth.php'; ?>
<?php include '../templates/header.php'; ?>

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

<?php if ($user['profile_photo']): ?>
  <p><strong>Profile Photo:</strong><br><img src="../<?= $user['profile_photo'] ?>" width="100"></p>
<?php endif; ?>

<?php if ($user['resume_file']): ?>
  <p><strong>Resume:</strong> <a href="../<?= $user['resume_file'] ?>" target="_blank">View Resume</a></p>
<?php endif; ?>

<div class="container my-5">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?> (Candidate)</h2>
    <p>You are logged in as a <strong>Job Seeker</strong>.</p>
</div>

<div class="card mt-4">
  <div class="card-header">Update Profile Picture & Resume</div>
  <div class="card-body">
    <form action="upload-files.php" method="POST" enctype="multipart/form-data">
      <div class="form-group mb-3">
        <label>Profile Photo (JPG/PNG):</label>
        <input type="file" name="profile_photo" accept="image/*" class="form-control" required>
      </div>
      <div class="form-group mb-3">
        <label>Resume (PDF/DOC):</label>
        <input type="file" name="resume_file" accept=".pdf,.doc,.docx" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary">Upload</button>
    </form>
  </div>
</div>

<h3 class="mt-5 mb-3">My Job Applications</h3>

<?php
$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT a.*, j.title, j.company_name 
                       FROM applications a 
                       JOIN jobs j ON a.job_id = j.id 
                       WHERE a.user_id = ? 
                       ORDER BY a.applied_at DESC");
$stmt->execute([$user_id]);
$applications = $stmt->fetchAll();

<h5>Notifications</h5>
<?php
$note = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$note->execute([$_SESSION['user']['id']]);
$notes = $note->fetchAll();

foreach ($notes as $n):
?>
    <div class="alert alert-success p-2 mb-2"><?= htmlspecialchars($n['message']) ?></div>
<?php endforeach; ?>

<li class="nav-item">
  <a class="nav-link" href="saved-jobs.php">Saved Jobs</a>
</li>

if ($applications):
?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Company</th>
                <th>Status</th>
                <th>Applied On</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($applications as $app): ?>
            <tr>
                <td><?= htmlspecialchars($app['title']) ?></td>
                <td><?= htmlspecialchars($app['company_name']) ?></td>
                <td><span class="badge bg-info"><?= htmlspecialchars($app['status']) ?></span></td>
                <td><?= date('d M Y', strtotime($app['applied_at'])) ?></td>
                <td><a href="../jobs/view.php?id=<?= $app['job_id'] ?>" class="btn btn-sm btn-primary">View Job</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No applications submitted yet.</p>
<?php endif; ?>

<li class="nav-item">
  <a class="nav-link" href="job-alerts.php">Job Alerts</a>
</li>

<?php include '../templates/footer.php'; ?>