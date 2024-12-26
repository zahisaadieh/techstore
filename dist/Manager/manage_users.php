<?php
session_start();
include('../DataBase/connection.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 2) { 
    header("Location: login.php");
    exit();
}

if (isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role_id = $_POST['role_id'];

    $sql = "UPDATE users SET email = ?, phone = ?, role_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $email, $phone, $role_id, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('✅ User updated successfully.'); window.location.href='manage_users.php';</script>";
    } else {
        echo "<script>alert('❌ Error updating user: " . $stmt->error . "');</script>";
    }
}

if (isset($_GET['delete_id'])) {
    $user_id_to_delete = $_GET['delete_id'];

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id_to_delete);

    if ($stmt->execute()) {
        echo "<script>alert('✅ User deleted successfully.'); window.location.href='manage_users.php';</script>";
    } else {
        echo "<script>alert('❌ Error deleting user: " . $stmt->error . "');</script>";
    }
}

// Fetch users from the database
$sql = "SELECT users.id, users.email, users.phone, users.role_id, roles.name AS role_name FROM users
        JOIN roles ON users.role_id = roles.id";
$result = $conn->query($sql);

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white text-black">

    <?php include('../Manager/nav.php'); ?>

    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-black mb-6">👥 Manage Users</h1>

        <div class="overflow-x-auto bg-gray-800 shadow-lg rounded-lg">
            <table class="min-w-full table-auto border-collapse">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left">📧 Email</th>
                        <th class="px-6 py-3 text-left">📱 Phone</th>
                        <th class="px-6 py-3 text-left">🔑 Role</th>
                        <th class="px-6 py-3 text-left">⚙️ Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-700">
                        <td class="px-6 py-4 border-b text-gray-300"><?= htmlspecialchars($row['email']) ?></td>
                        <td class="px-6 py-4 border-b text-gray-300"><?= htmlspecialchars($row['phone']) ?></td>
                        <td class="px-6 py-4 border-b text-gray-300"><?= htmlspecialchars($row['role_name']) ?></td>
                        <td class="px-6 py-4 border-b">
                            <button onclick="openEditModal(<?= $row['id'] ?>, '<?= addslashes($row['email']) ?>', '<?= addslashes($row['phone']) ?>', <?= $row['role_id'] ?>)"
                                    class="text-blue-400 hover:text-blue-600 font-semibold">
                               ✏️ Edit
                            </button> |
                            <a href="manage_users.php?delete_id=<?= $row['id'] ?>"
                               onclick="return confirm('⚠️ Are you sure you want to delete this user?')"
                               class="text-red-400 hover:text-red-600 font-semibold">
                               🗑️ Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div id="editUserModal" class="fixed inset-0 flex items-center justify-center bg-gray-600 bg-opacity-50" style="display:none;">
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg max-w-lg w-full relative">
                <button onclick="closeEditModal()" class="absolute top-2 right-2 text-gray-400 text-xl font-semibold">&times;</button>

                <h2 class="text-2xl font-semibold mb-6 text-white">✏️ Edit User</h2>

                <form method="POST" action="manage_users.php">
                    <input type="hidden" name="user_id" id="edit_user_id">

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-300">📧 Email</label>
                        <input type="email" name="email" id="edit_email" class="mt-1 block w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white rounded-md" required>
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-300">📱 Phone</label>
                        <input type="text" name="phone" id="edit_phone" class="mt-1 block w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white rounded-md" required>
                    </div>

                    <div class="mb-4">
                        <label for="role_id" class="block text-sm font-medium text-gray-300">🔑 Role</label>
                        <select name="role_id" id="edit_role_id" class="mt-1 block w-full px-3 py-2 border border-gray-600 bg-gray-700 text-white rounded-md" required>
                            <option value="1">Admin</option>
                            <option value="2">Manager</option>
                            <option value="3">User</option>
                        </select>
                    </div>

                    <button type="submit" name="edit_user" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700">
                        🔄 Update User
                    </button>
                </form>
            </div>
        </div>

    </div>
    <?php include('./footer.php'); ?>

    <script>
        function openEditModal(userId, email, phone, roleId) {
            document.getElementById("editUserModal").style.display = "flex";
            document.getElementById("edit_user_id").value = userId;
            document.getElementById("edit_email").value = email;
            document.getElementById("edit_phone").value = phone;
            document.getElementById("edit_role_id").value = roleId;
        }

        function closeEditModal() {
            document.getElementById("editUserModal").style.display = "none";
        }
    </script>
    

</body>

</html>
