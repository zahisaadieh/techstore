<?php
include('../DataBase/connection.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['product_id'], $_POST['quantity']) && is_numeric($_POST['product_id']) && is_numeric($_POST['quantity'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    $stmt = $conn->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product && $quantity > 0) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        if (array_key_exists($product_id, $_SESSION['cart'])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }

    header('Location: index.php?page=cart');
    exit;
}

if (isset($_GET['remove']) && is_numeric($_GET['remove']) && isset($_SESSION['cart'][$_GET['remove']])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    header('Location: index.php?page=cart');
    exit;
}

if (isset($_POST['update']) && isset($_SESSION['cart'])) {
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'quantity-') === 0 && is_numeric($value)) {
            $id = str_replace('quantity-', '', $key);
            $quantity = (int)$value;

            if (is_numeric($id) && isset($_SESSION['cart'][$id]) && $quantity > 0) {
                $_SESSION['cart'][$id] = $quantity;
            } elseif ($quantity <= 0) {
                unset($_SESSION['cart'][$id]);
            }
        }
    }

    header('Location: index.php?page=cart');
    exit;
}

if (isset($_POST['placeorder']) && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit;
    }

    header('Location: index.php?page=placeorder');
    exit;
}

$products_in_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$products = [];
$subtotal = 0.00;

if ($products_in_cart) {
    $placeholders = implode(',', array_fill(0, count($products_in_cart), '?'));
    $types = str_repeat('i', count($products_in_cart));

    $stmt = $conn->prepare('SELECT * FROM products WHERE id IN (' . $placeholders . ')');
    $stmt->bind_param($types, ...array_keys($products_in_cart));
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($products as $product) {
        if (isset($products_in_cart[$product['id']])) {
            $quantity = (int)$products_in_cart[$product['id']];
            $subtotal += (float)$product['price'] * $quantity;
        }
    }
}

$conn->close();
?>

<?= template_header('Cart') ?>

<div class="max-w-7xl mx-auto px-6 py-10">
    <h1 class="text-3xl font-semibold text-black mb-6">🛒 Shopping Cart</h1>

    <form action="index.php?page=cart" method="post">
        <table class="min-w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-800 text-white">
                    <th class="py-3 px-6 text-left">Product</th>
                    <th class="py-3 px-6 text-left">Price</th>
                    <th class="py-3 px-6 text-left">Quantity</th>
                    <th class="py-3 px-6 text-left">Total</th>
                    <th class="py-3 px-6 text-left">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">🚫 You have no products in your Shopping Cart</td> 
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <tr class="border-t border-gray-200">
                            <td class="py-4 px-6 flex items-center space-x-4">
                                <a href="index.php?page=product&id=<?= $product['id'] ?>" class="text-black">
                                    <img src="<?= (strpos($product['image'], 'uploads/') === 0) ? substr($product['image'], 7) : $product['image'] ?>"
                                        alt="<?= htmlspecialchars($product['product_name']) ?>"
                                        class="w-32 h-32 object-contain rounded-lg transition-transform duration-300 transform hover:scale-105">
                                </a>
                                <p><?= htmlspecialchars($product['product_name']) ?></p>
                            </td>
                            <td class="py-4 px-6 text-black font-semibold">
                                💲 &dollar;<?= number_format($product['price'], 2) ?>
                            </td>
                            <td class="py-4 px-6">
                                <input type="number"
                                    name="quantity-<?= $product['id'] ?>"
                                    value="<?= $products_in_cart[$product['id']] ?>"
                                    min="1"
                                    max="<?= $product['stock_quantity'] ?>"
                                    required
                                    class="w-20 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                            </td>
                            <td class="py-4 px-6 text-black font-semibold">
                                💰 &dollar;<?= number_format($product['price'] * $products_in_cart[$product['id']], 2) ?> 
                            </td>
                            <td class="py-4 px-6">
                                <a href="index.php?page=cart&remove=<?= $product['id'] ?>"
                                    class="text-red-600 hover:text-red-800 font-semibold">
                                    🗑️ Remove 
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="flex justify-between items-center mt-6">
            <div class="text-xl font-semibold text-gray-800">
                Subtotal: 💵 &dollar;<?= number_format($subtotal, 2) ?> 
            </div>
            <div class="space-x-4">
                <input type="submit" value="🔄 Update" name="update" 
                    class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600">
                <input type="submit" value="🚚 Place Order" name="placeorder" 
                    class="px-6 py-2 bg-black text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600">
            </div>
        </div>
    </form>

    <div class="flex justify-between text-xl font-bold border-t pt-4">
        <span>Total Price:</span>
        <span>💸 &dollar;<?= number_format($subtotal ?? 0, 2) ?> 
        </span>
    </div>
</div>

<?= template_footer() ?>
