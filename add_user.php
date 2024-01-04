<?php

header("Access-Control-Allow-Origin: *");  // * berarti izinkan dari semua domain, Anda juga dapat spesifikasikan domain yang diizinkan
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    exit;
}
require_once "config.php";

header("Content-Type: application/json");

$requestData = json_decode(file_get_contents("php://input"), true);

if (!isset($requestData['username'], $requestData['password'], $requestData['name'], $requestData['email'])) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$username = $requestData['username'];
$password = password_hash($requestData['password'], PASSWORD_DEFAULT);
$name = $requestData['name'];
$email = $requestData['email'];

global $link;

$sql = "INSERT INTO users (username, password, name, email) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "ssss", $username, $password, $name, $email);

if (mysqli_stmt_execute($stmt)) {
    $userId = mysqli_insert_id($link);
    echo json_encode(['userid' => $userId]);
} else {
    echo json_encode(['error' => 'Failed to insert user']);
}

mysqli_stmt_close($stmt);
