<?php
require_once '../includes/db.php';
session_start();

$keyword = $_GET['keyword'] ?? '';
$location = $_GET['location'] ?? '';
$job_type = $_GET['type'] ?? '';
$category = $_GET['category'] ?? '';

// Build query
$sql = "SELECT * FROM jobs WHERE status = 'approved' AND is_paid = 1";
$params = [];

if ($keyword) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}
if ($location) {
    $sql .= " AND location LIKE ?";
    $params[] = "%$location%";
}
if ($job_type) {
    $sql .= " AND job_type = ?";
    $params[] = $job_type;
}
if ($category) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$jobs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Jobs</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
<div class="container my-4">
    <h2 class="mb-4">Search Jobs</h2>

    <form class="row mb-4" method="get">
        <div class="col-md-3">
            <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" class="form-control" placeholder="Keyword">
        </div>
        <div class="col-md-3">
            <input type="text" name="location" value="<?= htmlspecialchars($location) ?>" class="form-control" placeholder="Location">
        </div>
        <div class="col-md-2">
            <select name="type" class="form-control">
                <option value="">Job Type</option>
                <option value="Full-Time" <?= $job_type == 'Full-Time' ? 'selected' : '' ?>>Full-Time</option>
                <option value="Part-Time" <?= $job_type == 'Part-Time' ? 'selected' : '' ?>>Part-Time</option>
                <option value="Contract" <?= $job_type == 'Contract' ? 'selected' : '' ?>>Contract</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="category" class="form-control">
                <option value="">Category</option>
                <option value="IT" <?= $category == 'IT' ? 'selected' : '' ?>>IT</option>
                <option value="Marketing" <?= $category == 'Marketing' ? 'selected' : '' ?>>Marketing</option>
                <option value="Sales" <?= $category == 'Sales' ? 'selected' : '' ?>>Sales</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Search</button>
        </div>
    </form>

    <?php if (count($jobs)): ?>
        <div class="list-group">
            <?php foreach ($jobs as $job): ?>
                <a href="view.php?id=<?= $job['id'] ?>" class="list-group-item list-group-item-action">
                    <h5><?= htmlspecialchars($job['title']) ?> <span class="badge bg-info"><?= $job['job_type'] ?></span></h5>
                    <p class="mb-1 text-muted"><?= htmlspecialchars($job['location']) ?> | <?= htmlspecialchars($job['category']) ?></p>
                    <small>Posted on <?= date('M d, Y', strtotime($job['created_at'])) ?></small>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No jobs found. Try adjusting your filters.</p>
    <?php endif; ?>
</div>
</body>
</html>