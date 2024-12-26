<?php
session_start();

include 'functions.php';
include('../DataBase/connection.php');

$allowed_pages = ['home', 'cart', 'products', 'product','profile', 'placeorder', 'login', 'register'];

$page = isset($_GET['page']) && in_array($_GET['page'], $allowed_pages) ? $_GET['page'] : 'home';

include $page . '.php';
?>
