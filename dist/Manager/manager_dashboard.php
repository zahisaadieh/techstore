<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php"); 
    exit();
}

include('../DataBase/connection.php');

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-black">

    <?php include('../Manager/nav.php'); ?>

    <div class="container mx-auto px-6 py-12">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <div class="card bg-white text-black p-8 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 text-center">
                <h2 class="text-xl font-semibold mb-4">📦 Total Orders</h2>
                <p class="text-4xl font-semibold mb-4">
                    <?php
                    $sql = "SELECT COUNT(*) AS total_orders FROM orders";
                    $result = $conn->query($sql);
                    $total_orders = $result->fetch_assoc()['total_orders'];
                    echo $total_orders;
                    ?>
                </p>
                <p class="text-sm text-gray-600">Total number of orders placed 🛒.</p>
            </div>

            <div class="card bg-white text-black p-8 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 text-center">
                <h2 class="text-xl font-semibold mb-4">🛍️ Total Products</h2>
                <p class="text-4xl font-semibold mb-4">
                    <?php
                    $sql = "SELECT COUNT(*) AS total_products FROM products";
                    $result = $conn->query($sql);
                    $total_products = $result->fetch_assoc()['total_products'];
                    echo $total_products;
                    ?>
                </p>
                <p class="text-sm text-gray-600">Total number of products in the store 🏪.</p>
            </div>

            <div class="card bg-white text-black p-8 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 text-center">
                <h2 class="text-xl font-semibold mb-4">👥 Total Users</h2>
                <p class="text-4xl font-semibold mb-4">
                    <?php
                    $sql = "SELECT COUNT(*) AS total_users FROM users";
                    $result = $conn->query($sql);
                    $total_users = $result->fetch_assoc()['total_users'];
                    echo $total_users;
                    ?>
                </p>
                <p class="text-sm text-gray-600">Total number of users registered 🙋‍♂️🙋‍♀️.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="card bg-white text-black p-8 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                <h2 class="text-xl font-semibold mb-4">➕ Add Product</h2>
                <p class="text-gray-600 mb-4">Add new products to your store. Keep your inventory up to date with the latest items 🆕.</p>
                <a href="add_product.php" class="inline-block text-black bg-white border-2 border-black hover:bg-gray-100 py-3 px-6 rounded-md text-lg font-medium transition duration-200">Add Product</a>
            </div>

            <div class="card bg-white text-black p-8 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                <h2 class="text-xl font-semibold mb-4">📋 Manage Orders</h2>
                <p class="text-gray-600 mb-4">View and manage customer orders. Update their status and manage delivery options 📦.</p>
                <a href="manage_orders.php" class="inline-block text-black bg-white border-2 border-black hover:bg-gray-100 py-3 px-6 rounded-md text-lg font-medium transition duration-200">Manage Orders</a>
            </div>

            <div class="card bg-white text-black p-8 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                <h2 class="text-xl font-semibold mb-4">🛠️ Manage Products</h2>
                <p class="text-gray-600 mb-4">Add, update, or delete products from your store. Maintain an accurate inventory 📊.</p>
                <a href="./manage_products.php" class="inline-block text-black bg-white border-2 border-black hover:bg-gray-100 py-3 px-6 rounded-md text-lg font-medium transition duration-200">Manage Products</a>
            </div>

            <div class="card bg-white text-black p-8 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                <h2 class="text-xl font-semibold mb-4">👤 Manage Users</h2>
                <p class="text-gray-600 mb-4">View and manage all users. Modify user details or deactivate accounts as needed 👥.</p>
                <a href="manage_users.php" class="inline-block text-black bg-white border-2 border-black hover:bg-gray-100 py-3 px-6 rounded-md text-lg font-medium transition duration-200">Manage Users</a>
            </div>
        </div>
    </div>

    <?php include('./footer.php'); ?>

</body>
</html>

<?php
$conn->close();
?>
