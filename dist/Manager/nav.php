<?php

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}
?>

<nav class="bg-black p-4">
    <div class="container mx-auto flex justify-between items-center">

        <a href="manager_dashboard.php" class="text-white text-2xl font-semibold">Teckstore 🛒</a>

        <ul class="flex space-x-6">
            <a href="../Login/logout.php" class="text-lg text-red-800 hover:text-black font-medium transition-colors duration-200" title="Logout">
                🚪
            </a>
        </ul>
    </div>
</nav>
