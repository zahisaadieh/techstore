<?php
session_start();
include('../DataBase/connection.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    die("Unauthorized access. Please log in as a Manager.");
}

$file = 'mi_data.csv';

if (!file_exists($file)) {
    die("CSV file not found.");
}

$handle = fopen($file, 'r');

if ($handle !== FALSE) {
    fgetcsv($handle);

    $stmt = $conn->prepare("INSERT INTO products (product_name, description, price, image, stock_quantity) VALUES (?, ?, ?, ?, ?)");

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $model_name = $data[0];      
        $ratings = $data[1];          
        $price = $data[2];             
        $imgURL = $data[3];           
        $storage_ram = $data[4];       
        $os_processor = $data[5];      
        $network = $data[6];         
        $battery = $data[7];         

        $price = preg_replace('/[^0-9.]/', '', $price); 
        $price = (float)$price;

        $description = "Rating: $ratings\nStorage & RAM: $storage_ram\nOS/Processor: $os_processor\nNetwork: $network\nBattery: $battery";

        $stock_quantity = 5;

        $stmt->bind_param("ssssi", $model_name, $description, $price, $imgURL, $stock_quantity);

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
