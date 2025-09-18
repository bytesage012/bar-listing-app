<?php
// api/get_cars.php

// Allow requests from any origin. For production, restrict this to your actual domain.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require "db.php";

try {
    $stmt = $pdo->query("SELECT * FROM cars ORDER BY created_at DESC");
    $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($cars);
} catch (PDOException $e) {
    // Basic error handling for the API
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
