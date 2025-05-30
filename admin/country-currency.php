<?php
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../auth/login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $country = trim($_POST['country']);
    $currency = trim($_POST['currency']);
    $rate = floatval($_POST['rate']);

    $stmt = $conn->prepare("INSERT INTO country_currency (country, currency, conversion_rate) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE currency = VALUES(currency), conversion_rate = VALUES(conversion_rate)");
    $stmt->bind_param("ssd", $country, $currency, $rate);
    $stmt->execute();
    $success = "Updated successfully.";
}

$result = $conn->query("SELECT * FROM country_currency ORDER BY country ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Country & Currency Settings</title>
    <link rel='stylesheet' href='../assets/bootstrap.min.css'>
</head>
<body>
<div class='container mt-5'>
    <h2>Manage Country & Currency</h2>
    <?php if (isset($success)): ?>
        <div class='alert alert-success'><?= $success ?></div>
    <?php endif; ?>
    <form method='post' class='mb-4'>
        <div class='row'>
            <div class='col'>
                <input type='text' name='country' class='form-control' placeholder='Country (e.g., Australia)' required>
            </div>
            <div class='col'>
                <input type='text' name='currency' class='form-control' placeholder='Currency (e.g., AUD)' required>
            </div>
            <div class='col'>
                <input type='number' step='0.01' name='rate' class='form-control' placeholder='Conversion Rate' required>
            </div>
            <div class='col'>
                <button type='submit' class='btn btn-primary'>Save</button>
            </div>
        </div>
    </form>
    <table class='table table-bordered'>
        <thead>
            <tr>
                <th>Country</th>
                <th>Currency</th>
                <th>Conversion Rate</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['country']) ?></td>
                <td><?= htmlspecialchars($row['currency']) ?></td>
                <td><?= number_format($row['conversion_rate'], 2) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>