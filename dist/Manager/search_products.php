<?php
include('../DataBase/connection.php');

if (isset($_GET['query'])) {
    $query = '%' . $_GET['query'] . '%';
    $sql = "SELECT * FROM products WHERE product_name LIKE ? OR description LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $query, $query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($product = $result->fetch_assoc()) {
            $product_image_path = '' . htmlspecialchars($product['image']);
            echo '
            <div class="bg-white p-4 rounded-lg shadow-md">
                <img src="' . $product_image_path . '" alt="Product Image" class="w-full h-64 object-contain mb-4 rounded-lg">
                <h3 class="text-xl font-semibold">' . htmlspecialchars($product['product_name']) . '</h3>
                <p class="text-sm text-gray-500 mb-2">' . htmlspecialchars($product['description']) . '</p>
                <p class="text-lg font-semibold text-indigo-600">Price: $' . number_format($product['price'], 2) . '</p>
                <p class="text-sm text-gray-500">Stock: ' . htmlspecialchars($product['stock_quantity']) . '</p>
                <button onclick="openEditModal(' . $product['id'] . ', \'' . addslashes($product['product_name']) . '\', \'' . addslashes($product['description']) . '\', ' . $product['price'] . ', ' . $product['stock_quantity'] . ', \'' . addslashes($product['image']) . '\')" 
                        class="mt-4 w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700">
                    Edit Product
                </button>
                <button onclick="confirmDelete(' . $product['id'] . ')" class="mt-4 w-full bg-red-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-red-700">
                    Delete Product
                </button>
            </div>';
        }
    } else {
        echo "<p>No products found.</p>";
    }
}
?>
