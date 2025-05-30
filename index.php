<?php
// Detect domain, load home template
include __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/auth.php';
include __DIR__ . '/templates/home.php';

<?php
require_once 'includes/functions.php';

$country_code = $_GET['country_code'] ?? 'au';
$stmt = $conn->prepare("SELECT * FROM jobs WHERE country = ? AND status = 'approved' ORDER BY created_at DESC");
$stmt->bind_param("s", $country_code);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JobVisaWorld - Find Your Next Opportunity</title>
    <link rel="stylesheet" href="assets/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Latest Jobs in <?= strtoupper($country_code) ?></h1>
    <div class="list-group">
        <?php while ($job = $result->fetch_assoc()): ?>
            <a href="job/view.php?id=<?= $job['id'] ?>" class="list-group-item list-group-item-action">
                <h5><?= htmlspecialchars($job['title']) ?></h5>
                <p><?= htmlspecialchars($job['location']) ?> | <?= date('d M Y', strtotime($job['created_at'])) ?></p>
            </a>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>