<?php

include('./dist/DataBase/connection.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


include('./dist/index.php');

if (isset($conn)) {
    $conn->close();
}
?>
