<?php
require_once("../includes/headers.php");
require_once("../includes/database.php");

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}

function getUserInfo($email) {
    global $conn;

    $query = "SELECT Password FROM user WHERE Email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);

    if (!$stmt->execute()) {
        echo 'Error occured while parsing login: ' . $conn->error;
    }

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    $stmt->close();
    $conn->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);

    $email = $data['email'];
    $password = $data['password'];
    $userInfo = getUserInfo($email);

    if (password_verify($password, $userInfo["Password"])) {
        $response = true;
    }
    else {
        $response = false;
    }
    
    echo json_encode($response);

    header("http://localhost:8081");
    die();
}

?>