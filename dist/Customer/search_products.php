<?php
include('../DataBase/connection.php');

$query = isset($_GET['query']) ? '%' . $_GET['query'] . '%' : '';

$stmt = $conn->prepare("SELECT id, product_name, image, price FROM products WHERE product_name LIKE ? ORDER BY created_at DESC");
$stmt->bind_param("s", $query);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

if (!empty($products)) {
    foreach ($products as $product) {
        echo '<a href="index.php?page=product&id=' . htmlspecialchars($product['id']) . '"
                class="block bg-white border border-gray-300 rounded-lg overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                <img src="' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['product_name']) . '" class="w-full h-48 object-contain" onerror="this.onerror=null; this.src=\'../Manager/uploads/default.jpg\';">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">' . htmlspecialchars($product['product_name']) . '</h2>
                    <p class="text-lg font-medium text-gray-800 mt-2">
                        &dollar;' . number_format($product['price'], 2) . '
                    </p>
                </div>
            </a>';
    }
} else {
    echo '<p class="col-span-4 text-center text-gray-500">No products found.</p>';
}
?>
