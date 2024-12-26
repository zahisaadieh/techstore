<?php
include('../DataBase/connection.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$num_products_on_each_page = 148;
$current_page = isset($_GET['p']) && filter_var($_GET['p'], FILTER_VALIDATE_INT) ? (int)$_GET['p'] : 1;
$current_page = max($current_page, 1);
$offset = ($current_page - 1) * $num_products_on_each_page;

$search_query = isset($_GET['query']) ? '%' . $_GET['query'] . '%' : '%';

$stmt = $conn->prepare("SELECT id, product_name, image, price, stock_quantity FROM products 
                        WHERE product_name LIKE ? 
                        ORDER BY created_at DESC 
                        LIMIT ?, ?");
$stmt->bind_param("sii", $search_query, $offset, $num_products_on_each_page);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

$total_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM products WHERE product_name LIKE ?");
$total_stmt->bind_param("s", $search_query);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_products = (int)$total_row['total'];
$total_pages = ceil($total_products / $num_products_on_each_page);

$conn->close();
?>

<?= template_header('Products') ?>

<div class="max-w-7xl mx-auto px-6 py-10">
    <h1 class="text-3xl font-semibold text-black mb-6">Products</h1>
    <p class="text-gray-600"><?= $total_products ?> Products Available</p>

    <div class="mb-6">
        <form method="GET" action="index.php">
            <input type="hidden" name="page" value="products">
            <input type="text" name="query" value="<?= htmlspecialchars($_GET['query'] ?? '') ?>"
                class="w-full p-3 border border-gray-300 rounded-md"
                placeholder="Search for products...">
            <button type="submit"
                class="mt-2 bg-black text-white px-4 py-2 rounded-md hover:bg-gray-800">Search</button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mt-8">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="relative bg-white border border-gray-300 rounded-lg overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                    <?php if ($product['stock_quantity'] == 0): ?>
                        <div class="absolute top-0 right-0 bg-red-600 text-white text-xs font-bold px-3 py-1 z-10">
                            🚫 Sold Out
                        </div>
                    <?php endif; ?>

                    <img src="<?= htmlspecialchars($product['image']) ?>"
                        alt="<?= htmlspecialchars($product['product_name']) ?>"
                        class="w-full h-48 object-contain"
                        onerror="this.onerror=null; this.src='../Manager/uploads/default.jpg';">

                    <div class="p-6 text-center">
                        <h2 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($product['product_name']) ?></h2>
                        <p class="text-lg font-medium text-gray-800 mt-2">
                            &dollar;<?= number_format($product['price'], 2) ?>
                        </p>
                    </div>

                    <?php if ($product['stock_quantity'] > 0): ?>
                        <a href="index.php?page=product&id=<?= htmlspecialchars($product['id']) ?>"
                            class="absolute inset-0"></a>
                    <?php else: ?>
                        <div class="absolute inset-0 cursor-not-allowed"></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="col-span-4 text-center text-gray-500">No products available at the moment.</p>
        <?php endif; ?>
    </div>

    <div class="flex justify-center mt-12 gap-4">
        <?php if ($current_page > 1): ?>
            <a href="index.php?page=products&p=<?= $current_page - 1 ?>&query=<?= urlencode($_GET['query'] ?? '') ?>"
                class="bg-gray-800 text-white px-6 py-3 rounded-md hover:bg-gray-700 transition-all duration-200">Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="index.php?page=products&p=<?= $i ?>&query=<?= urlencode($_GET['query'] ?? '') ?>"
                class="px-6 py-3 rounded-md text-gray-800 border border-gray-300 hover:bg-gray-100 transition-all duration-200 
               <?= $current_page == $i ? 'bg-black text-white' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($current_page < $total_pages): ?>
            <a href="index.php?page=products&p=<?= $current_page + 1 ?>&query=<?= urlencode($_GET['query'] ?? '') ?>"
                class="bg-gray-800 text-white px-6 py-3 rounded-md hover:bg-gray-700 transition-all duration-200">Next</a>
        <?php endif; ?>
    </div>
</div>

<script src="../../js/jquery-3.1.1.js"></script>
<script>
$(document).ready(function() {
    $('.cursor-not-allowed').on('click', function() {
        alert('🚫 This product is currently sold out.');
    });
});
</script>

<?= template_footer() ?>
