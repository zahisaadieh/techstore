<?php
session_start();
include('../DataBase/connection.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    die("Unauthorized access. Please log in as a Manager.");
}

$file = 'phones.csv';

if (!file_exists($file)) {
    die("CSV file not found.");
}

$handle = fopen($file, 'r');

if ($handle !== FALSE) {
    $header = fgetcsv($handle);

    $stmt = $conn->prepare("INSERT INTO products (product_name, description, price, image, stock_quantity) VALUES (?, ?, ?, ?, ?)");

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $name = $data[0]; 
        $price = (float)preg_replace('/[^0-9.]/', '', $data[1]); // price (clean and convert to float)
        $img = $data[4]; 

        $description = "Rating: {$data[2]}\n";
        $description .= "Specs Score: {$data[3]}\n";
        $description .= "Sim: {$data[5]}\n";
        $description .= "Processor: {$data[6]}\n";
        $description .= "Company: {$data[7]}\n";
        $description .= "4G: {$data[8]}\n";
        $description .= "5G: {$data[9]}\n";
        $description .= "NFC: {$data[10]}\n";
        $description .= "VoLTE: {$data[11]}\n";
        $description .= "Core: {$data[12]}\n";
        $description .= "Frequency: {$data[13]}\n";
        $description .= "RAM (Inbuilt): {$data[14]}\n";
        $description .= "RAM: {$data[15]}\n";
        $description .= "Fast Charging: {$data[16]}\n";
        $description .= "Battery: {$data[17]} mAh\n";
        $description .= "Display Size: {$data[18]}\n";
        $description .= "Display Pixels: {$data[19]}\n";
        $description .= "Display Frequency: {$data[20]} Hz\n";
        $description .= "Punch Hole: {$data[21]}\n";
        $description .= "Front Camera: {$data[22]}\n";
        $description .= "Rear Camera: {$data[23]}\n";
        $description .= "Extended Upto: {$data[24]}\n";
        $description .= "Memory Card: {$data[25]}\n";
        $description .= "OS Version: {$data[26]}\n";
        $description .= "OS Brand: {$data[27]}";

        $stock_quantity = 3;

        $stmt->bind_param("ssdsi", $name, $description, $price, $img, $stock_quantity);

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
