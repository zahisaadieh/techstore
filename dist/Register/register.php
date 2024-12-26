<?php

include('../DataBase/connection.php');

$error_message = "";
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = $_POST['phone']; // New phone field
    $role_name = $_POST['role'] ?? 'Customer';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !strpos($email, '@gmail.com')) {
        $error_message = "Please enter a valid Gmail address.";
    } 
    // Validate password length
    elseif (strlen($password) <= 8) {
        $error_message = "Password must be greater than 8 characters.";
    } 

    elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (!preg_match('/^\d{8}$/', $phone)) { 
        $error_message = "Please enter a valid 8-digit phone number.";
    } else {
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "SELECT id FROM roles WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $role_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $role = $result->fetch_assoc();
            $role_id = $role['id'];

            $sql = "INSERT INTO users (email, password, phone, role_id) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $email, $hashed_password, $phone, $role_id);

            if ($stmt->execute()) {
                $success_message = "Registration successful! Please login.";
            } else {
                $error_message = "Error: " . $stmt->error;
            }
        } else {
            $error_message = "Role does not exist.";
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
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white-900 h-screen flex justify-center items-center">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-xl">
        <h2 class="text-3xl font-semibold text-center text-black mb-6">📝 Create an Account</h2>

        <?php if ($error_message): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                <p class="font-medium"><?php echo $error_message; ?></p>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                <p class="font-medium"><?php echo $success_message; ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-800">📧 Email</label> 
                <input type="email" name="email" id="email" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black sm:text-sm" required>
            </div>

            <div class="mb-6">
                <label for="phone" class="block text-sm font-medium text-gray-800">📱 Phone Number</label> 
                <input type="text" name="phone" id="phone" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black sm:text-sm" required pattern="^\d{8}$" placeholder="e.g. 12345678">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-800">🔒 Password</label> 
                <input type="password" name="password" id="password" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black sm:text-sm" required>
            </div>

            <div class="mb-6">
                <label for="confirm_password" class="block text-sm font-medium text-gray-800">🔑 Confirm Password</label> 
                <input type="password" name="confirm_password" id="confirm_password" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black sm:text-sm" required>
            </div>

            <input type="hidden" name="role" value="Customer">

            <button type="submit" class="w-full bg-black text-white font-semibold py-3 px-4 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-black focus:ring-opacity-50">✅ Register</button> 
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">Already have an account? <a href="../Login/login.php" class="text-black hover:text-gray-700">🔓 Login here</a></p> 
    </div>

</body>
</html>
