<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

include('../DataBase/connection.php');

$upload_dir = '../Manager/uploads';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = trim($_POST['product_name']);
    $product_description = trim($_POST['product_description']);
    $product_price = filter_var($_POST['product_price'], FILTER_VALIDATE_FLOAT);
    $product_stock = filter_var($_POST['product_stock'], FILTER_VALIDATE_INT);

    if (!$product_price || !$product_stock) {
        $error_message = "❌ Invalid price or stock quantity.";
    } else {
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $image = $_FILES['product_image'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $image_mime = mime_content_type($image['tmp_name']);

            if (in_array($image_mime, $allowed_types)) {
                $image_name = time() . "_" . basename($image['name']);
                $image_tmp = $image['tmp_name'];
                $image_path = $upload_dir . '/' . $image_name;

                if (move_uploaded_file($image_tmp, $image_path)) {
                    $image_db_path = '../Manager/uploads/' . $image_name;

                    $sql = "INSERT INTO products (product_name, description, price, image, stock_quantity) 
            VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssdsi", $product_name, $product_description, $product_price, $image_db_path, $product_stock);

                    if ($stmt->execute()) {
                        $success_message = "✅ Product added successfully!";
                    } else {
                        $error_message = "❌ Error adding product to the database.";
                    }
                    $stmt->close();
                } else {
                    $error_message = "❌ Image upload failed.";
                }
            } else {
                $error_message = "❌ Invalid image format. Only JPEG, PNG, and GIF are allowed.";
            }
        } else {
            $error_message = "❌ Please choose an image.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white text-black">

    <?php include('../Manager/nav.php'); ?>

    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-4xl font-semibold text-center text-black mb-12">🛍️ Add New Product</h1>

        <?php if (isset($success_message)): ?>
            <div class="bg-green-500 text-white text-center py-2 mb-6 rounded-md shadow-lg">
                <?php echo $success_message; ?>
            </div>
        <?php elseif (isset($error_message)): ?>
            <div class="bg-red-500 text-white text-center py-2 mb-6 rounded-md shadow-lg">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="add_product.php" method="POST" enctype="multipart/form-data" class="bg-gray-800 p-8 rounded-lg shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label for="product_name" class="block text-lg font-medium text-gray-300 mb-2">📦 Product Name</label>
                    <input type="text" id="product_name" name="product_name" class="w-full p-4 border border-gray-600 bg-gray-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>

                <div>
                    <label for="product_price" class="block text-lg font-medium text-gray-300 mb-2">💲 Product Price ($)</label>
                    <input type="number" id="product_price" name="product_price" class="w-full p-4 border border-gray-600 bg-gray-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>

                <div class="md:col-span-2">
                    <label for="product_description" class="block text-lg font-medium text-gray-300 mb-2">📝 Product Description</label>
                    <textarea id="product_description" name="product_description" class="w-full p-4 border border-gray-600 bg-gray-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required></textarea>
                </div>

                <div>
                    <label for="product_quantity" class="block text-lg font-medium text-gray-300 mb-2">🔢 Product Quantity</label>
                    <input type="number" id="product_quantity" name="product_stock" class="w-full p-4 border border-gray-600 bg-gray-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>

                <div>
                    <label for="product_image" class="block text-lg font-medium text-gray-300 mb-2">🖼️ Product Image</label>
                    <input type="file" id="product_image" name="product_image" class="w-full p-4 border border-gray-600 bg-gray-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>
            </div>

            <button type="submit" class="mt-6 w-full bg-indigo-600 text-white py-4 rounded-md shadow-md hover:bg-indigo-700 transition ease-in-out duration-200">➕ Add Product</button>
        </form>
    </div>

    <?php include('./footer.php'); ?>

</body>

</html>

<?php
$conn->close();
?>