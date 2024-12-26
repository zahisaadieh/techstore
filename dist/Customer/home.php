<style>
    @keyframes slider {
        0% {
            transform: translateX(0);
        }

        16.66% {
            transform: translateX(-100%);
        }

        33.33% {
            transform: translateX(-200%);
        }

        50% {
            transform: translateX(-300%);
        }

        66.66% {
            transform: translateX(-400%);
        }

        83.33% {
            transform: translateX(-500%);
        }

        100% {
            transform: translateX(0);
        }
    }

    .animate-slider {
        display: flex;
        width: 600%;
        animation: slider 50s infinite ease-in-out; 
        animation-play-state: running;
    }

    .relative:hover .animate-slider {
        animation-play-state: paused;
    }

    .slider-item {
        flex: 0 0 16.666%; 
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .bg-black {
        background-color: #000;
    }

    .bg-white {
        background-color: #fff;
    }

    .text-black {
        color: #000;
    }

    .text-white {
        color: #fff;
    }

    .text-gray-300 {
        color: #D1D5DB;
    }

    .text-gray-500 {
        color: #6B7280;
    }

    .text-gray-700 {
        color: #374151;
    }

    .text-gray-900 {
        color: #111827;
    }

    .shadow-md {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .hover\:shadow-lg:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .services {
        background-color: #000;
        color: white;
        padding: 40px 20px;
        margin-top: 40px;
    }

    .service-item {
        background-color: #333;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        transition: background-color 0.3s ease-in-out;
    }

    .service-item:hover {
        background-color: #444;
    }
</style>

<?php
include('../DataBase/connection.php'); 
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM products 
        WHERE stock_quantity > 0 
        ORDER BY created_at DESC 
        LIMIT 4";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$recently_added_products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recently_added_products[] = $row;
    }
}

$conn->close();
?>

<?= template_header('Home') ?>
<div class="text-center py-8">
    <h2 class="text-3xl font-extrabold text-gray-900">
        <span role="img" aria-label="world">🌍</span> Welcome to Our World,
        <a href="profile.php" class="text-blue-500 hover:underline"><?= htmlspecialchars($email); ?></a>!
    </h2>
</div>

<div class="relative overflow-hidden w-full h-64 bg-gray-100 mt-4">
    <div class="flex animate-slider">
        <div class="slider-item min-w-full h-64 bg-black text-white flex flex-col items-center justify-center text-center p-4">
            <h3 class="text-2xl font-bold">🚀 Fast & Reliable Delivery!</h3>
            <p class="text-gray-300 mt-2">Get your favorite tech products delivered to your doorstep in no time.</p>
        </div>
        <div class="slider-item min-w-full h-64 bg-black text-white flex flex-col items-center justify-center text-center p-4">
            <h3 class="text-2xl font-bold">🔥 Hot Deals Every Day!</h3>
            <p class="text-gray-300 mt-2">Unbeatable discounts on the latest gadgets and accessories.</p>
        </div>
        <div class="slider-item min-w-full h-64 bg-black text-white flex flex-col items-center justify-center text-center p-4">
            <h3 class="text-2xl font-bold">💻 Wide Range of Products!</h3>
            <p class="text-gray-300 mt-2">From laptops to smartphones, find everything tech in one place.</p>
        </div>
        <div class="slider-item min-w-full h-64 bg-black text-white flex flex-col items-center justify-center text-center p-4">
            <h3 class="text-2xl font-bold">📦 Free Shipping On All Orders!</h3>
            <p class="text-gray-300 mt-2">Enjoy free shipping on all your tech purchases, no minimum required!</p>
        </div>
        <div class="slider-item min-w-full h-64 bg-black text-white flex flex-col items-center justify-center text-center p-4">
            <h3 class="text-2xl font-bold">🌟 Exclusive Member Benefits!</h3>
            <p class="text-gray-300 mt-2">Become a member and enjoy exclusive discounts and offers.</p>
        </div>
        <div class="slider-item min-w-full h-64 bg-black text-white flex flex-col items-center justify-center text-center p-4">
            <h3 class="text-2xl font-bold">🛒 Shop with Confidence!</h3>
            <p class="text-gray-300 mt-2">Enjoy secure payments and reliable customer service with every order.</p>
        </div>
    </div>
</div>

<div class="services">
    <h2 class="text-3xl font-bold text-center mb-6">Our Professional Services</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <div class="service-item">
            <h3 class="text-xl font-bold flex items-center justify-center">
                <span class="mr-2">🛠️</span> Tech Support
            </h3>
            <p class="mt-2 text-gray-300">Expert help for troubleshooting, installation, and tech-related problems.</p>
        </div>
        <div class="service-item">
            <h3 class="text-xl font-bold flex items-center justify-center">
                <span class="mr-2">🔄</span> Warranty & Returns
            </h3>
            <p class="mt-2 text-gray-300">Hassle-free returns and warranty services to ensure your peace of mind.</p>
        </div>
        <div class="service-item">
            <h3 class="text-xl font-bold flex items-center justify-center">
                <span class="mr-2">💼</span> Consulting Services
            </h3>
            <p class="mt-2 text-gray-300">Consult with professionals to choose the best tech products for your needs.</p>
        </div>
    </div>
</div>

<div class="recentlyadded container mx-auto mt-8 px-4">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">Recently Added Products</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <?php if (!empty($recently_added_products)): ?>
            <?php foreach ($recently_added_products as $product): ?>
                <a href="index.php?page=product&id=<?= htmlspecialchars($product['id']) ?>"
                    class="product bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <img src="<?= htmlspecialchars($product['image']) ?>"
                        alt="<?= htmlspecialchars($product['product_name']) ?>"
                        class="w-full h-48 object-contain"
                        onerror="this.onerror=null; this.src='../Manager/uploads/default.jpg';">

                    <div class="p-4">
                        <span class="name block text-lg font-bold text-gray-900"><?= htmlspecialchars($product['product_name']) ?></span>
                        <span class="price text-gray-700">
                            &dollar;<?= number_format($product['price'], 2) ?>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-gray-500 col-span-4">No recently added products available at the moment.</p>
        <?php endif; ?>
    </div>
</div>

<?= template_footer() ?>
