<?php
session_start();
include('../DataBase/connection.php');

$error_message = "";
$success_message = "";

$upload_dir = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $product_name = trim($_POST['product_name']);
    $product_description = trim($_POST['product_description']);
    $product_price = filter_var($_POST['product_price'], FILTER_VALIDATE_FLOAT);
    $product_stock = filter_var($_POST['product_stock'], FILTER_VALIDATE_INT);

    if (!$product_price) {
        $error_message = "Invalid price or stock quantity.";
    } else {
        $image_path = $_POST['existing_image'];
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $image = $_FILES['product_image'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $image_mime = mime_content_type($image['tmp_name']);

            if (in_array($image_mime, $allowed_types)) {
                $image_name = time() . "_" . basename($image['name']);
                $image_tmp = $image['tmp_name'];
                $image_path = $upload_dir . $image_name;

                if (!move_uploaded_file($image_tmp, $image_path)) {
                    $error_message = "Image upload failed.";
                }
            } else {
                $error_message = "Invalid image format. Only JPEG, PNG, and GIF are allowed.";
            }
        }

        if (!$error_message) {
            $sql = "UPDATE products SET product_name = ?, description = ?, price = ?, image = ?, stock_quantity = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdsii", $product_name, $product_description, $product_price, $image_path, $product_stock, $product_id);

            if ($stmt->execute()) {
                $success_message = "Product updated successfully!";
            } else {
                $error_message = "Error updating product.";
            }
            $stmt->close();
        }
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "SELECT image FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($image_path);

    if ($stmt->fetch()) {
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        $delete_sql = "DELETE FROM products WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $delete_id);

        if ($delete_stmt->execute()) {
            $success_message = "Product deleted successfully!";
        } else {
            $error_message = "Error deleting product.";
        }
        $delete_stmt->close();
    } else {
        $error_message = "Product not found.";
    }
    $stmt->close();
}

// Fetch all products
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function openEditModal(productId, productName, productDescription, productPrice, productStock, productImage) {
            document.getElementById("editProductModal").style.display = "flex";
            document.getElementById("edit_product_id").value = productId;
            document.getElementById("edit_product_name").value = productName;
            document.getElementById("edit_product_description").value = productDescription;
            document.getElementById("edit_product_price").value = productPrice;
            document.getElementById("edit_product_stock").value = productStock;
            document.getElementById("existing_image").value = productImage;
        }

        function closeEditModal() {
            document.getElementById("editProductModal").style.display = "none";
        }

        function confirmDelete(productId) {
            if (confirm('Are you sure you want to delete this product?')) {
                window.location.href = 'manage_products.php?delete_id=' + productId;
            }
        }
    </script>
</head>

<body class="bg-gray-50 font-sans leading-normal tracking-normal">

    <?php include "./nav.php"; ?>

    <div class="mb-6 mt-8">
        <input type="text" id="searchQuery" class="w-full p-3 border border-gray-300 rounded-lg shadow-md" placeholder="Search products..." />
    </div>

    <div id="editProductModal" class="fixed inset-0 flex items-center justify-center bg-gray-600 bg-opacity-50" style="display:none;">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-lg w-full relative">
            <button onclick="closeEditModal()" class="absolute top-2 right-2 text-gray-500 text-xl font-semibold">&times;</button>

            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Edit Product</h2>

            <?php if ($error_message): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg">
                    <p><?php echo $error_message; ?></p>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-lg">
                    <p><?php echo $success_message; ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" action="manage_products.php" enctype="multipart/form-data">
                <input type="hidden" name="product_id" id="edit_product_id">
                <input type="hidden" name="existing_image" id="existing_image">

                <div class="mb-4">
                    <label for="edit_product_name" class="block text-sm font-medium text-gray-700">Product Name</label>
                    <input type="text" name="product_name" id="edit_product_name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label for="edit_product_description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="product_description" id="edit_product_description" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required></textarea>
                </div>

                <div class="mb-4">
                    <label for="edit_product_price" class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" name="product_price" id="edit_product_price" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label for="edit_product_stock" class="block text-sm font-medium text-gray-700">Stock Quantity</label>
                    <input type="number" name="product_stock" id="edit_product_stock" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label for="product_image" class="block text-sm font-medium text-gray-700">Product Image</label>
                    <input type="file" name="product_image" id="edit_product_image" class="mt-1 block w-full">
                </div>

                <button type="submit" name="edit_product" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg shadow-md hover:bg-indigo-700 transition duration-300">Update Product</button>
            </form>
        </div>
    </div>

    <div id="product-list" class="w-full max-w-7xl mx-auto mt-10 p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        if ($result->num_rows > 0) {
            while ($product = $result->fetch_assoc()) {
                $product_image_path = $upload_dir . htmlspecialchars($product['image']);
                echo '
                <div class="bg-white p-6 rounded-lg shadow-lg transition-all duration-300 hover:shadow-xl">
                    <img src="' . $product_image_path . '" alt="Product Image" class="w-full h-64 object-contain mb-4 rounded-lg">
                    <h3 class="text-xl font-semibold text-gray-800">' . htmlspecialchars($product['product_name']) . '</h3>
                    <p class="text-sm text-gray-500 mb-2">' . htmlspecialchars($product['description']) . '</p>
                    <p class="text-lg font-semibold text-indigo-600">Price: $' . number_format($product['price'], 2) . '</p>
                    <p class="text-sm text-gray-500">Stock: ' . htmlspecialchars($product['stock_quantity']) . '</p>
                    <div class="mt-4 flex gap-4">
                        <button onclick="openEditModal(' . $product['id'] . ', \'' . addslashes($product['product_name']) . '\', \'' . addslashes($product['description']) . '\', ' . $product['price'] . ', ' . $product['stock_quantity'] . ', \'' . addslashes($product['image']) . '\')" 
                            class="w-1/2 bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300">
                            Edit Product
                        </button>
                        <button onclick="confirmDelete(' . $product['id'] . ')" class="w-1/2 bg-red-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-red-700 transition duration-300">
                            Delete Product
                        </button>
                    </div>
                </div>';
            }
        } else {
            echo "<p class='text-center text-gray-500'>No products found.</p>";
        }
        ?>
    </div>

</body>

</html>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#searchQuery').on('input', function() {
            var query = $(this).val();

            $.ajax({
                url: 'search_products.php',
                method: 'GET',
                data: {
                    query: query
                },
                success: function(response) {

                    $('#product-list').html(response);
                }
            });
        });
    });
</script>