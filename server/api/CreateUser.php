<?php
require_once("../includes/headers.php");
require_once("../includes/database.php");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

function encrypt($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function createNewUser($email, $password, $tel) {
    global $conn;
    $encryptedPass = encrypt($password);

    $query = "INSERT INTO user (Email, Password, Phone) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $email, $encryptedPass, $tel);

    if (!$stmt->execute()) {
        echo 'Error occured: ' . $conn->error;
        http_response_code(500);
    }

    $stmt->close();
    $conn->close();
    header("http://localhost:8081");
    die();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents('php://input'), true);

    $email = $data['email'];
    $password = $data['password'];
    $tel = $data['tel'];
    
    createNewUser($email, $password, $tel);
}
?>