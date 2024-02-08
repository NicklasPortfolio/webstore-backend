<?php
require_once('../includes/headers.php');
require_once('../includes/database.php');

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

function getProducts($id, $category) {
    global $conn;

    if (!$category && !$id) {
        $query = "SELECT * FROM Product";
        $stmt = $conn->prepare($query);
    }

    else if ($category) {
        $query = "SELECT * FROM Product INNER JOIN ProductCategory ON Product.CategoryId = ProductCategory.CategoryId WHERE ProductCategory.CategoryName = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $category);
    }

    else if ($id) {
        $query = "SELECT * FROM Product WHERE ProductId = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
    }

    if (!$stmt->execute()) {
        echo 'Error occured while getting products ' . $conn->error;
        http_response_code(500);
    }

    return $stmt->get_result()->fetch_all();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postData = json_decode(file_get_contents('php://input'), true);

    $id = $postData['id'] ?? null;
    $category = $postData['category'] ?? null;

    $data = getProducts($id, $category);

    $formattedData = array_map(function($product) {
        return [
            'ProductId' => $product[0],
            'ProductName' => $product[1],
            'img' => $product[2],
            'CategoryId' => $product[3],
            'Price' => $product[4],
            'WarehouseQty' => $product[5]
        ];
    }, $data);

    header('Content-Type: application/json');

    echo json_encode($formattedData);
}


?>