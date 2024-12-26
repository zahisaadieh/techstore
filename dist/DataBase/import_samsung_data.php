<?php
session_start();
include('../DataBase/connection.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    die("Unauthorized access. Please log in as a Manager.");
}

$file = 'samsungMobilesData.csv';

if (!file_exists($file)) {
    die("CSV file not found.");
}

$handle = fopen($file, 'r');

if ($handle !== FALSE) {
    fgetcsv($handle);

    $stmt = $conn->prepare("INSERT INTO products (product_name, description, price, image, stock_quantity) VALUES (?, ?, ?, ?, ?)");

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $name = $data[0];
        $ratings = $data[1];
        $price = $data[2];
        $imgURL = $data[3];
        $camera = $data[4];
        $display = $data[5];
        $battery = $data[6];
        $storage = $data[7];
        $ram = $data[8];
        $processor = $data[9];
        $android_version = $data[10];

        $description = "Ratings: $ratings, Camera: $camera, Display: $display, Battery: $battery, Storage: $storage, RAM: $ram, Processor: $processor, Android Version: $android_version";

        $stock_quantity = 6;

        $stmt->bind_param("ssdsi", $name, $description, $price, $imgURL, $stock_quantity);

        if (!$stmt->execute()) {
            echo "Error inserting row: " . $stmt->error . "<br>";
        }
    }

    fclose($handle);
    $stmt->close();
    echo "Data imported successfully!";
} else {
    echo "Failed to open the CSV file.";
}

$conn->close();
?>
