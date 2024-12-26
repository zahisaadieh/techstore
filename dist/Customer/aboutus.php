

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-black">

    <nav class="bg-black text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold">TechStore</a>
            <div class="space-x-6 text-lg">
                <a href="index.php" class="hover:text-gray-400">Home</a>
                <a href="aboutus.php" class="hover:text-gray-400">About Us</a>
                <a href="contact.php" class="hover:text-gray-400">Contact</a>
            </div>
        </div>
    </nav>

    <section class="container mx-auto px-6 py-12">
        <h1 class="text-4xl font-bold text-center mb-8">About Us 🖤</h1>
        
        <div class="bg-gray-100 shadow-md rounded-lg p-8">
            <p class="text-xl text-center mb-6">
                Welcome to <span class="font-semibold">TechStore</span> — Where Innovation Meets Excellence.
            </p>
            
            <div class="flex flex-col md:flex-row items-center md:space-x-8">
                <div class="md:w-1/2 mb-6 md:mb-0">
                    <img src="https://via.placeholder.com/400x300" alt="About Us Image" class="rounded-lg shadow-md">
                </div>
                <div class="md:w-1/2">
                    <p class="text-lg leading-relaxed text-gray-700">
                        At <span class="font-semibold">TechStore</span>, we believe in delivering state-of-the-art technology products with unmatched quality and reliability. Our mission is to empower individuals and businesses by providing the tools they need to stay ahead in a rapidly evolving digital world.  
                    </p>
                    <p class="mt-4 text-lg text-gray-700">
                        From the latest gadgets to essential tech accessories, our carefully curated collection ensures you get the best every time.  
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-12">
            <h2 class="text-3xl font-semibold text-center mb-6">Our Core Values ⚡</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-50 p-6 rounded-lg shadow-md text-center">
                    <h3 class="text-xl font-semibold mb-2">💼 Professionalism</h3>
                    <p class="text-gray-600">We maintain the highest standards in every aspect of our business.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg shadow-md text-center">
                    <h3 class="text-xl font-semibold mb-2">🤝 Customer Focus</h3>
                    <p class="text-gray-600">Our customers are at the heart of everything we do.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg shadow-md text-center">
                    <h3 class="text-xl font-semibold mb-2">🌟 Innovation</h3>
                    <p class="text-gray-600">We embrace change and drive innovation to stay ahead.</p>
                </div>
            </div>
        </div>

        <div class="mt-12 text-center">
            <h2 class="text-3xl font-semibold mb-6">Meet Our Team 🧑‍💼</h2>
            <p class="text-lg text-gray-700 max-w-2xl mx-auto">
                Our dedicated team of professionals works tirelessly to ensure our customers have the best experience possible. With expertise across diverse domains, we deliver excellence at every step.
            </p>
        </div>
    </section>

    <section class="bg-black text-white py-12 mt-12">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl font-bold mb-4">Stay Connected with Us 🤍</h2>
            <p class="text-lg mb-6">Follow us on social media for updates, exclusive deals, and more!</p>
            <div class="flex justify-center space-x-6 text-lg">
                <a href="https://facebook.com" target="_blank" class="hover:text-gray-400">📘 Facebook</a>
                <a href="https://twitter.com" target="_blank" class="hover:text-gray-400">🐦 Twitter</a>
                <a href="https://instagram.com" target="_blank" class="hover:text-gray-400">📸 Instagram</a>
                <a href="https://linkedin.com" target="_blank" class="hover:text-gray-400">🔗 LinkedIn</a>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-white py-6">
        <div class="container mx-auto text-center">
            <p class="text-sm">&copy; <?= date('Y') ?> TechStore. All rights reserved.</p>
            <p class="text-xs mt-1">Crafted with care 🖤</p>
        </div>
    </footer>

</body>
</html>
