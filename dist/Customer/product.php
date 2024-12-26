<?php
include('../DataBase/connection.php'); 

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and sanitize the 'id' parameter from URL
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $product_id = (int)$_GET['id'];

    // Prepare and execute SQL statement to fetch product details
    $stmt = $conn->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // Check if the product exists
    if (!$product) {
        exit('<div class="text-red-500 text-center py-4">🚨 Product does not exist!</div>');
    }
} else {
    exit('<div class="text-red-500 text-center py-4">⚠️ Product ID is not specified or invalid!</div>');
}

// Close the database connection
$conn->close();
?>

<?= template_header('Product') ?>

<div class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-1 md:grid-cols-2 gap-6 items-center bg-white shadow-lg rounded-lg overflow-hidden">
    <!-- Product Image -->
    <div class="overflow-hidden rounded-lg shadow-lg">
    <img src="<?= (strpos($product['image'], 'uploads/') === 0) ? substr($product['image'], 7) : $product['image'] ?>" 
         alt="<?= htmlspecialchars($product['product_name']) ?>" 
         class="w-full h-64 md:h-96 object-contain rounded-lg transition-transform duration-300 transform hover:scale-105">
    </div>

    <!-- Product Details -->
    <div class="space-y-4">
        <h1 class="text-3xl font-semibold text-black"><?= htmlspecialchars($product['product_name']) ?> 🛍️</h1> 
        <p class="text-xl text-black font-semibold">
            💵 &dollar;<?= number_format($product['price'], 2) ?> 
            <?php if (!empty($product['rrp']) && $product['rrp'] > 0): ?>
                <span class="text-gray-500 text-lg line-through ml-2">
                    🏷️ &dollar;<?= number_format($product['rrp'], 2) ?> 
                </span>
            <?php endif; ?>
        </p>

        <form action="index.php?page=cart" method="post">
            <div class="flex items-center gap-4 mt-4">
                <label for="quantity" class="text-black">🔢 Quantity</label> 
                <input type="number" 
                       name="quantity" 
                       id="quantity" 
                       value="1" 
                       min="1" 
                       max="<?= htmlspecialchars($product['stock_quantity']) ?>" 
                       required 
                       class="w-20 p-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-black">
            </div>

            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">

            <button type="submit" 
                    class="mt-6 w-full bg-black text-white py-3 rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-black transition-all duration-300">
                🛒 Add To Cart 
            </button>
        </form>

        <div class="mt-6 text-black leading-relaxed">
            <h3 class="text-lg font-semibold text-black">📜 Description:</h3> 
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        </div>
    </div>
</div>

<?= template_footer() ?>
