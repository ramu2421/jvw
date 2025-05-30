<?php
require_once 'vendor/autoload.php';
require_once 'includes/db.php';
require_once 'includes/session.php';

$client = new Google_Client();
$client->setClientId('http://366294797432-nkhfae9ubm8o66pjr4qd5ns11ai4h8oa.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://jobvisaworld.com/jobportal/google-callback.php');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    $oauth = new Google_Service_Oauth2($client);
    $userInfo = $oauth->userinfo->get();

    $email = $userInfo->email;
    $name = $userInfo->name;

    // Default user type (can be adjusted based on button clicked)
    $type = $_SESSION['google_user_type'] ?? 'candidate';
    $table = $type === 'employer' ? 'employers' : 'candidates';

    // Check if user already exists
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Login
        $_SESSION['user_type'] = $type;
        $_SESSION[$type . '_id'] = $user['id'];
    } else {
        // Register
        $stmt = $pdo->prepare("INSERT INTO $table (name, email, is_verified) VALUES (?, ?, 1)");
        $stmt->execute([$name, $email]);
        $id = $pdo->lastInsertId();

        $_SESSION['user_type'] = $type;
        $_SESSION[$type . '_id'] = $id;
    }

    header("Location: $type/dashboard.php");
    exit;
}