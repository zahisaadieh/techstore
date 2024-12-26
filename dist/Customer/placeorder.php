<?php
include('../DataBase/connection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login'); 
    exit;
}

$user_id = $_SESSION['user_id'];  
$user_email = $_SESSION['email'];
$user_phone = $_SESSION['phone']; 

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: index.php?page=cart');
    exit;
}

$subtotal = 0.00;
$total_price = 0.00;
$delivery_charge = 0.00;

$products_in_cart = $_SESSION['cart'];
$placeholders = implode(',', array_fill(0, count($products_in_cart), '?'));
$types = str_repeat('i', count($products_in_cart));

$stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->bind_param($types, ...array_keys($products_in_cart));
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

foreach ($products as $product) {
    $subtotal += $product['price'] * $products_in_cart[$product['id']];
}

if (isset($_POST['place_order'])) {
    $address = $_POST['address'];
    $delivery_option = $_POST['delivery_option'];

    if ($delivery_option === 'Standard') {
        $delivery_charge = 3.00; 
    } elseif ($delivery_option === 'Express') {
        $delivery_charge = 7.00; 
    }

    $total_price = $subtotal + $delivery_charge;

    $stmt = $conn->prepare("INSERT INTO orders (user_id, email, phone, address, delivery_option, total_price) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issssd', $user_id, $user_email, $user_phone, $address, $delivery_option, $total_price);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    $stmt = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
    foreach ($products as $product) {
        $product_id = $product['id'];
        $quantity = $products_in_cart[$product_id];
        $price = $product['price'];
        $total = $price * $quantity;

        $stmt->bind_param('iiidd', $order_id, $product_id, $quantity, $price, $total);
        $stmt->execute();
    }

    // Clear cart and redirect
    unset($_SESSION['cart']);
    header('Location: index.php?page=order_success');
    exit;
} else {
    $total_price = $subtotal;
}

$conn->close();
?>

<?= template_header('Place Order') ?>

<div class="max-w-4xl mx-auto px-6 py-10 bg-white text-black rounded-lg shadow-md">
    <h1 class="text-3xl font-bold mb-6 text-center">📝 Place Your Order</h1>

    <form action="index.php?page=placeorder" method="post" class="space-y-6">
        <div>
            <label class="block font-semibold mb-2">📧 Email</label>
            <input type="email" value="<?= htmlspecialchars($user_email) ?>" disabled
                class="w-full px-4 py-2 border rounded-md bg-gray-100 cursor-not-allowed">
        </div>

        <div>
            <label class="block font-semibold mb-2">📱 Phone</label>
            <input type="text" value="<?= htmlspecialchars($user_phone) ?>" disabled
                class="w-full px-4 py-2 border rounded-md bg-gray-100 cursor-not-allowed">
        </div>

        <div>
            <label class="block font-semibold mb-2">📍 Delivery Address</label>
            <textarea name="address" rows="3" required placeholder="Enter your delivery address"
                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-gray-600"></textarea>
        </div>

        <div>
            <label class="block font-semibold mb-2">🚚 Delivery Option</label>
            <select name="delivery_option" required
                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-gray-600">
                <option value="Standard">Standard Delivery (+$3) 🚚</option>
                <option value="Express">Express Delivery (+$7) ⚡</option>
            </select>
        </div>

        <div class="flex justify-between text-xl font-bold border-t pt-4">
            <span>Total Price:</span>
            <span>&dollar;<?= number_format($total_price, 2) ?></span>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="index.php?page=cart"
                class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600">
                🛒 Back to Cart
            </a>
            <button type="submit" name="place_order"
                class="px-6 py-2 bg-black text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600">
                ✅ Confirm Order
            </button>
        </div>
    </form>
</div>

<?= template_footer() ?>
