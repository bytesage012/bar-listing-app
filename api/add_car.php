<?php
// api/add_car.php
header("Content-Type: application/json");
require "db.php"; // Make sure your db.php connects to your database

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Handle Text Inputs from $_POST
    $make = $_POST["make"] ?? "";
    $model = $_POST["model"] ?? "";
    $year = $_POST["year"] ?? 0;
    $price = $_POST["price"] ?? 0;
    $description = $_POST["description"] ?? "";

    // 2. Handle File Upload from $_FILES
    if (
        !isset($_FILES["image"]) ||
        $_FILES["image"]["error"] !== UPLOAD_ERR_OK
    ) {
        echo json_encode([
            "status" => "error",
            "message" =>
                "Image upload failed. Error code: " .
                ($_FILES["image"]["error"] ?? "N/A"),
        ]);
        exit();
    }

    // Define the directory to store uploads (e.g., a folder named 'uploads' in the root)
    $uploads_dir = __DIR__ . "/../uploads";
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true); // Create the directory if it doesn't exist
    }

    // Generate a unique filename to prevent overwriting files
    $tmp_name = $_FILES["image"]["tmp_name"];
    $filename = uniqid() . "-" . basename($_FILES["image"]["name"]);
    $target_file = $uploads_dir . "/" . $filename;

    // Move the uploaded file to the target directory
    if (!move_uploaded_file($tmp_name, $target_file)) {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to save the uploaded image.",
        ]);
        exit();
    }

    // This is the path you'll store in the database
    $image_path = "uploads/" . $filename;

    // 3. Insert into the database
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO cars (make, model, year, price, description, image_url) VALUES (?, ?, ?, ?, ?, ?)",
        );
        $stmt->execute([
            $make,
            $model,
            $year,
            $price,
            $description,
            $image_path,
        ]);

        echo json_encode([
            "status" => "success",
            "message" => "Car added successfully!",
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Database error: " . $e->getMessage(),
        ]);
    }
} else {
    // Handle cases where the request method is not POST
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method. Only POST is accepted.",
    ]);
}
?>
