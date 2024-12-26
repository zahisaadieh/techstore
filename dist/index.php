<?php

session_start();

include('./DataBase/connection.php');


if (!isset($_SESSION['user_id'])) {
    header("Location: ./dist/Login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];



$sql = "SELECT role_id FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $role_id = $user['role_id'];

    if ($role_id == 1) {
        header("Location: Customer/index.php");
        exit();
    } elseif ($role_id == 2) {
        header("Location: Manager/manager_dashboard.php");
        exit();
    } else {
        echo "Invalid role ID.";
    }
} else {
    echo "User not found.";
}

$conn->close();
?>
