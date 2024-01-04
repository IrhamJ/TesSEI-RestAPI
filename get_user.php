<?php
# get_user.php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    exit;
}
# Include connection
require_once "config.php";

header("Content-Type: application/json");

# Fetch users from the database
$query = "SELECT id, username, name, email FROM users";
$result = $link->query($query);

$users = [];

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode(['users' => $users]); // Menggunakan kunci 'users' untuk membungkus array
