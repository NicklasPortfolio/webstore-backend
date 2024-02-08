<?php
require_once('../includes/headers.php');
require_once('../includes/database.php');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

function getCategories() {
    global $conn;

    $query = "SELECT * FROM productcategory";
    $stmt = $conn->prepare($query);

    if (!$stmt->execute()) {
        echo 'Error occured while getting categories: ' . $conn->error;
        http_response_code(500);
    }

    $result = $stmt->get_result();
    return $result->fetch_all();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $data = getCategories();

    $formattedData = array_map(function($category) {
        return [
            'CategoryId' => $category[0],
            'CategoryName' => $category[1],
        ];
    }, $data);

    echo json_encode($formattedData);
}

?>