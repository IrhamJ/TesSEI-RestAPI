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

if (!isset($requestData['username'], $requestData['password'])) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$username = $requestData['username'];
$password = $requestData['password'];

global $link;

$sql = "SELECT id, username, name, email, password FROM users WHERE LOWER(username) = LOWER(?)";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    echo json_encode([
        'error' => 'Invalid username or password',
        'debug_info' => [
            'username' => $username,
            'password' => $password,
            'hashed_password' => password_hash($password, PASSWORD_DEFAULT),
            'stored_password' => null
        ]
    ]);
    exit;
}

$isValidPassword = password_verify($password, $user['password']);

if (!$isValidPassword) {
    echo json_encode([
        'error' => 'Invalid username or password',
        'debug_info' => [
            'username' => $username,
            'password' => $password,
            'hashed_password' => password_hash($password, PASSWORD_DEFAULT),
            'stored_password' => $user['password']
        ]
    ]);
    exit;
}

echo json_encode([
    'username' => $user['username'],
    'name' => $user['name'],
    'email' => $user['email'],
]);
