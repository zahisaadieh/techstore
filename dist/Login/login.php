<?php
include('../DataBase/connection.php');

$error_message = "";
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        $sql = "SELECT id, password, role_id, phone FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['email'] = $email;
                $_SESSION['phone'] = $user['phone'];

                header("Location: ../index.php");
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "No user found with that email address.";
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white-900 h-screen flex justify-center items-center">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-xl">
        <h2 class="text-3xl font-semibold text-center text-black mb-6">Login to Your Account</h2>

        <?php if ($error_message): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                <p class="font-medium"><?php echo $error_message; ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-800">📧 Email</label> 
                <input type="email" name="email" id="email" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black sm:text-sm" required>
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-800">🔑 Password</label> 
                <input type="password" name="password" id="password" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black sm:text-sm" required>
            </div>

            <button type="submit" class="w-full bg-black text-white font-semibold py-2 px-4 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-black focus:ring-opacity-50">🚪 Login</button> 
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">Don't have an account? <a href="../Register/register.php" class="text-black hover:text-gray-700">📝 Register here</a></p> 
    </div>

</body>
</html>
