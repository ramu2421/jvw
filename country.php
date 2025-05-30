<?php
$country_code = $_GET['cc'] ?? 'au'; // default to AU
$_GET['country_code'] = strtolower($country_code);

// Load main index (it will use this to filter content)
include 'index.php';