<?php
require_once '../includes/db.php';
session_start();

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ? AND status = 'approved'");
$stmt->execute([$id]);
$job = $stmt->fetch();

if (!$job) {
    echo "Job not found.";
    exit;
}
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php elseif (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script type="application/ld+json">
    <?= json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
    </script>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($job['title']) ?> | Job Details</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>

$schemaData = [
  "@context" => "https://schema.org/",
  "@type" => "JobPosting",
  "title" => $job['title'],
  "description" => strip_tags($job['description']),
  "datePosted" => date('Y-m-d', strtotime($job['created_at'])),
  "validThrough" => date('Y-m-d', strtotime($job['created_at'] . ' +30 days')),
  "employmentType" => "FULL_TIME",
  "hiringOrganization" => [
    "@type" => "Organization",
    "name" => $job['company_name'],
    "sameAs" => "https://jobvisaworld.com"
  ],
  "jobLocation" => [
    "@type" => "Place",
    "address" => [
      "@type" => "PostalAddress",
      "addressLocality" => $job['location'],
      "addressCountry" => $job['country']
    ]
  ]
];
    
<div class="container my-5">
    <a href="index.php" class="btn btn-secondary mb-3">← Back to Job Listings</a>

    <h2><?= htmlspecialchars($job['title']) ?></h2>
    <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
    <p><strong>Job Type:</strong> <?= htmlspecialchars($job['job_type']) ?></p>
    <p><strong>Category:</strong> <?= htmlspecialchars($job['category']) ?></p>
    <hr>
    <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>

    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'candidate'): ?>
        <form method="post" action="apply.php">
            <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
            <button type="submit" class="btn btn-success mt-3">Apply Now</button>
        </form>
    <?php else: ?>
        <a href="../auth/login.php" class="btn btn-primary mt-3">Login to Apply</a>
    <?php endif; ?>
    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'candidate'): ?>
      <form method="POST" action="../candidate/save_job.php" style="display:inline;">
        <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
        <button type="submit" class="btn btn-outline-success btn-sm">💾 Save Job</button>
      </form>
    <?php endif; ?>
</div>
</body>
</html>