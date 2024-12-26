<?php

include('../DataBase/connection.php');

function template_header($title)
{
    $num_items_in_cart = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
    echo <<<EOT
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>$title</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
    </head>
    <body class="bg-gray-50 text-gray-900">
        <!-- Header Section -->
        <header class="bg-white border-b border-gray-300 shadow-md py-4">
            <div class="container mx-auto px-6 flex justify-between items-center">
                <!-- Logo or Website Name with Emoji -->
                <h1 class="text-3xl font-semibold text-black flex items-center">
                    <span class="mr-2">🚀</span> 
                    <a href="index.php" class="hover:text-gray-900">TechStore</a>
                </h1>
                
                <!-- Navigation Links (Visible on all screen sizes) -->
                <nav class="flex space-x-8">
                    <a href="index.php" class="text-lg text-gray-800 hover:text-black font-medium transition-colors duration-200">🏠</a> <!-- Home Emoji -->
                    <a href="index.php?page=products" class="text-lg text-gray-800 hover:text-black font-medium transition-colors duration-200">🛍️</a> <!-- Products Emoji -->
                    <a href="../Login/logout.php" class="text-lg text-red-800 hover:text-black font-medium transition-colors duration-200">🚪</a> <!-- Logout Emoji -->
                </nav>
                
                <!-- Cart Icon -->
                <div class="relative">
                    <a href="index.php?page=cart" class="text-gray-800 hover:text-black text-xl">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="absolute -top-2 -right-2 bg-black text-white text-xs rounded-full px-2 py-0.5">$num_items_in_cart</span>
                    </a>
                </div>
            </div>
        </header>
        
        <!-- Main Content Section -->
        <main class="container mx-auto px-6 py-8">
EOT;
}



function template_footer()
{
    $year = date('Y');
    echo <<<EOT
        </main>
        <footer class="bg-black text-white py-8 mt-8 border-t-4 border-white">
            <div class="container mx-auto px-4 text-center">
                <div class="flex justify-center space-x-8 mb-6">
                    <!-- Footer Navigation Links -->
                    <a href="./privacy.php" class="text-white hover:text-gray-400">Privacy Policy</a>
                    <a href="./service.php" class="text-white hover:text-gray-400">Terms of Service</a>
                    <a href="./aboutus.php" class="text-white hover:text-gray-400">About Us</a>
                </div>
                <div class="mb-6">
                    <!-- Social Media Links -->
                    <p class="text-lg">Follow Us on Social Media 📱</p>
                    <div class="flex justify-center space-x-6 mt-4">
                        <a href="https://facebook.com" target="_blank" class="hover:text-gray-400">📘 Facebook</a>
                <a href="https://twitter.com" target="_blank" class="hover:text-gray-400">🐦 Twitter</a>
                <a href="https://instagram.com" target="_blank" class="hover:text-gray-400">📸 Instagram</a>
                <a href="https://linkedin.com" target="_blank" class="hover:text-gray-400">🔗 LinkedIn</a>
                    </div>
                </div>
                <p class="text-lg">&copy; $year TechStore. All rights reserved.</p>
                <p class="mt-2 text-sm">Designed with ❤️ for tech enthusiasts 🚀</p>
            </div>
        </footer>
    </body>
    </html>
EOT;
}
