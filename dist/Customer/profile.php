<?php
session_start();

include('../DataBase/connection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id']; 

$sql = "SELECT email, phone FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($email, $phone);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];
    $new_phone = $_POST['phone'];

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET email = ?, password = ?, phone = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssi", $new_email, $hashed_password, $new_phone, $user_id);
    } else {
        $update_sql = "UPDATE users SET email = ?, phone = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssi", $new_email, $new_phone, $user_id);
    }

    if ($stmt->execute()) {
        header("Location: ../login/logout.php"); 
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error updating profile. Please try again later.</div>";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white font-sans text-black">

    <div class="container mx-auto p-4">
        <h2 class="text-4xl font-extrabold text-center mb-8">Edit Your Profile</h2>

        <div class="max-w-md mx-auto bg-white text-black shadow-xl rounded-lg p-6">
            <form action="profile.php" method="POST">
                
                <div class="mb-6">
                    <label for="email" class="block text-lg font-medium mb-2">Email Address</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <span class="absolute left-3 top-3 text-blue-600">📧</span> 
                    </div>
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-lg font-medium mb-2">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Leave empty to keep current password" class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span class="absolute left-3 top-3 text-blue-600">🔒</span> 
                    </div>
                </div>

                <div class="mb-6">
                    <label for="phone" class="block text-lg font-medium mb-2">Phone Number</label>
                    <div class="relative">
                        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <span class="absolute left-3 top-3 text-blue-600">📱</span>
                    </div>
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="px-6 py-3 bg-blue-500 text-white text-lg font-semibold rounded-lg hover:bg-blue-700 transition duration-300 ease-in-out">Update Profile</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
