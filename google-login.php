<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('http://366294797432-nkhfae9ubm8o66pjr4qd5ns11ai4h8oa.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://jobvisaworld.com/jobportal/google-callback.php');
$client->addScope("email");
$client->addScope("profile");

header('Location: ' . $client->createAuthUrl());
exit;