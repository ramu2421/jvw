<?php
require_once '../includes/db.php';
require_once '../includes/mailer.php'; // already set in earlier steps

$stmt = $pdo->query("
  SELECT ja.user_id, ja.keyword, ja.location, u.email
  FROM job_alerts ja
  JOIN users u ON ja.user_id = u.id
");

$alerts = $stmt->fetchAll();

foreach ($alerts as $alert) {
  $keyword = $alert['keyword'];
  $location = $alert['location'];
  $email = $alert['email'];

  $jobs = $pdo->prepare("SELECT * FROM jobs WHERE status = 'approved' AND (title LIKE ? OR description LIKE ?) AND location LIKE ?");
  $kw = "%$keyword%";
  $loc = "%$location%";
  $jobs->execute([$kw, $kw, $loc]);
  $matched = $jobs->fetchAll();

  if ($matched) {
    $message = "<h3>New Jobs Matching: $keyword - $location</h3><ul>";
    foreach ($matched as $job) {
      $message .= "<li><a href='https://jobvisaworld.com/jobportal/jobs/view-job.php?id={$job['id']}'>{$job['title']}</a> – {$job['company_name']}</li>";
    }
    $message .= "</ul>";

    sendMail($email, "Job Alerts - New Jobs Matching Your Criteria", $message);
  }
}