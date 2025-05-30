<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireLogin('employer');

$employer = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Employer Dashboard - JobVisaWorld</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <li><a href="notifications.php">🔔 Notifications</a></li>
</head>
<body>

<li class="nav-item">
  <a class="nav-link" href="shortlisted.php">Shortlisted</a>
</li>

<div class="container py-5">
  <h2>Welcome, <?= htmlspecialchars($employer['name']) ?></h2>

  <!-- Subscription Status -->
  <div class="card my-4">
    <div class="card-header bg-primary text-white">
      Your Subscription
    </div>
    <div class="card-body">
      <?php
      $stmt = $pdo->prepare("SELECT * FROM subscriptions WHERE employer_id = ? ORDER BY created_at DESC LIMIT 1");
      $stmt->execute([$employer['id']]);
      $subscription = $stmt->fetch();

      if ($subscription):
        $now = new DateTime();
        $ends_at = new DateTime($subscription['ends_at']);
        $is_active = $now < $ends_at;
      ?>
        <p><strong>Plan:</strong> <?= htmlspecialchars($subscription['plan']) ?></p>
        <p><strong>Start Date:</strong> <?= date('M d, Y', strtotime($subscription['created_at'])) ?></p>
        <p><strong>End Date:</strong> <?= date('M d, Y', strtotime($subscription['ends_at'])) ?></p>
        <p><strong>Status:</strong>
          <?php if ($is_active): ?>
            <span class="badge bg-success">Active</span>
          <?php else: ?>
            <span class="badge bg-danger">Expired</span>
          <?php endif; ?>
        </p>
      <?php else: ?>
        <p class="text-muted">You don’t have any active subscription.</p>
      <?php endif; ?>
    </div>
  </div>
  
 <h5>Notifications</h5>
<?php
$note = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$note->execute([$_SESSION['user']['id']]);
$notes = $note->fetchAll();

foreach ($notes as $n):
?>
    <div class="alert alert-info p-2 mb-2"><?= htmlspecialchars($n['message']) ?></div>
<?php endforeach; ?>

  <!-- Job Listings -->
  <h4 class="mb-3">Your Job Listings</h4>

  <?php
  $stmt = $pdo->prepare("SELECT * FROM jobs WHERE employer_id = ? ORDER BY created_at DESC");
  $stmt->execute([$employer['id']]);
  $jobs = $stmt->fetchAll();

  if ($jobs):
  ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Title</th>
          <th>Location</th>
          <th>Status</th>
          <th>Posted On</th>
          <th>View</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($jobs as $job): ?>
          <tr>
            <td><?= htmlspecialchars($job['title']) ?></td>
            <td><?= htmlspecialchars($job['location']) ?></td>
            <td>
              <?php if ($job['status'] === 'approved'): ?>
                <span class="badge bg-success">Approved</span>
              <?php elseif ($job['status'] === 'pending'): ?>
                <span class="badge bg-warning text-dark">Pending</span>
              <?php else: ?>
                <span class="badge bg-danger">Rejected</span>
              <?php endif; ?>
            </td>
            <td><?= date('M d, Y', strtotime($job['created_at'])) ?></td>
            <td><a href="../job-details.php?id=<?= $job['id'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">View</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="alert alert-info">You haven't posted any jobs yet.</div>
  <?php endif; ?>
</div>

</body>
</html>

<h3 class="mt-5 mb-3">Applications Received</h3>

<?php
$employer_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("
    SELECT a.*, j.title AS job_title, u.name AS candidate_name, u.email AS candidate_email
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN users u ON a.user_id = u.id
    WHERE j.user_id = ?
    ORDER BY a.applied_at DESC
");
$stmt->execute([$employer_id]);
$apps = $stmt->fetchAll();

if ($apps):
?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Candidate</th>
                <th>Email</th>
                <th>Job</th>
                <th>Status</th>
                <th>Applied On</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($apps as $app): ?>
            <tr>
                <td><?= htmlspecialchars($app['candidate_name']) ?></td>
                <td><?= htmlspecialchars($app['candidate_email']) ?></td>
                <td><?= htmlspecialchars($app['job_title']) ?></td>
                <td><span class="badge bg-secondary"><?= $app['status'] ?></span></td>
                <td><?= date('d M Y', strtotime($app['applied_at'])) ?></td>
                <td>
                    <?php if ($app['status'] === 'Pending'): ?>
                        <form method="post" action="update-application.php" class="d-inline">
                            <input type="hidden" name="app_id" value="<?= $app['id'] ?>">
                            <input type="hidden" name="action" value="Approved">
                            <button class="btn btn-sm btn-success">Approve</button>
                        </form>
                        <form method="post" action="update-application.php" class="d-inline">
                            <input type="hidden" name="app_id" value="<?= $app['id'] ?>">
                            <input type="hidden" name="action" value="Rejected">
                            <button class="btn btn-sm btn-danger">Reject</button>
                        </form>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No applications received yet.</p>
<?php endif; ?>

<form method="POST" action="shortlist.php">
  <input type="hidden" name="candidate_id" value="<?= $application['candidate_id'] ?>">
  <input type="hidden" name="application_id" value="<?= $application['id'] ?>">
  <button type="submit" class="btn btn-sm btn-outline-primary">Shortlist</button>
</form>

</body>
</html>