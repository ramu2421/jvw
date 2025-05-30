<?php
header("Content-Type: application/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://jobvisaworld.com</loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
<?php
require_once __DIR__ . '/includes/db.php';
$stmt = $pdo->query("SELECT id, created_at FROM jobs WHERE status = 'approved'");
while ($job = $stmt->fetch()) {
    $id = $job['id'];
    $created = date('Y-m-d', strtotime($job['created_at']));
    echo "
  <url>
    <loc>https://jobvisaworld.com/jobportal/jobs/view-job.php?id=$id</loc>
    <lastmod>$created</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>";
}
?>
</urlset>