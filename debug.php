<?php
// Enable full error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>🔍 Server Debug Report</h2>";

// Check PHP version
echo "<strong>PHP Version:</strong> " . phpversion() . "<br>";

// Check required extensions
$extensions = ['mysqli', 'curl', 'mbstring', 'openssl', 'json'];
foreach ($extensions as $ext) {
    echo "<strong>Extension $ext:</strong> " . (extension_loaded($ext) ? 'Loaded ✅' : 'Missing ❌') . "<br>";
}

// Check critical files
$critical_files = [
    'includes/functions.php',
    'includes/db.php',
    'vendor/autoload.php',
    'config.php'
];
foreach ($critical_files as $file) {
    echo "<strong>$file:</strong> " . (file_exists($file) ? 'Exists ✅' : 'Missing ❌') . "<br>";
}

// Check folder permissions
$folders = ['includes', 'admin', 'candidate', 'employer'];
foreach ($folders as $folder) {
    echo "<strong>$folder permissions:</strong> " . substr(sprintf('%o', fileperms($folder)), -4) . "<br>";
}

// Check DB connection
require_once 'config.php';
$conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    echo "<strong>Database Connection:</strong> Failed ❌ - " . $conn->connect_error . "<br>";
} else {
    echo "<strong>Database Connection:</strong> Successful ✅<br>";
    $conn->close();
}

echo "<br><em>Remember to delete this debug.php file after use for security reasons!</em>";
?>