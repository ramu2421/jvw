<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

$host = 'localhost';
$user = 'u818683586_jobportal';
$pass = 'Jobvisaworld@786###';
$db = 'u818683586_jobportal';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Clean input
function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Currency conversion via exchangerate.host
function convertCurrency($from, $to, $amount) {
    $endpoint = "https://api.exchangerate.host/convert?from=$from&to=$to&amount=$amount";
    $data = json_decode(file_get_contents($endpoint), true);
    return round($data['result'] ?? $amount, 2);
}
?>