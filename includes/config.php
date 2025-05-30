<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting - Turn off in production
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'u818683586_jobportal');  // ← Replace with actual DB user
define('DB_PASS', 'Jobvisaworld@786###');  // ← Replace with actual DB password
define('DB_NAME', 'u818683586_jobportal');      // ← Replace with actual DB name

// Site configuration
define('SITE_NAME', 'JobVisaWorld');
define('SITE_URL', 'https://jobvisaworld.com');
define('CURRENCY', 'AUD');

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Stripe configuration
define('STRIPE_SECRET_KEY', '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'); // ← Replace with your live key
define('STRIPE_PUBLISHABLE_KEY', 'pk_live_51RD7uuFZRa6Rf08cg427bU9R5ixEHS9DvytetMoQ1w6MZzvC6J8nK18DsPI3gVLYRQ3HOBoHByrFNl0IRxc426lH006eGWJg2B'); // ← Replace with your publishable key

// Google OAuth2 Configuration
define('GOOGLE_CLIENT_ID', 'http://366294797432-nkhfae9ubm8o66pjr4qd5ns11ai4h8oa.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', SITE_URL . '/google-callback.php');

// Security Headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://js.stripe.com https://accounts.google.com; style-src 'self' 'unsafe-inline';");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer-when-downgrade");
header("X-XSS-Protection: 1; mode=block");

// Database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>