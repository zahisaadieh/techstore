<?php
session_start();
include('../DataBase/connection.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] !== 2) { 
    header("Location: login.php");
    exit();
}

$sql = "SELECT o.id, o.email, o.phone, o.address, o.delivery_option, o.total_price, o.status, o.created_at
        FROM orders o ORDER BY o.created_at DESC";
$result = $conn->query($sql);

if (isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['update_order']; 

    if (!in_array($status, ['Done', 'Cancelled'])) {
        echo "<script>alert('Invalid status.'); window.location.href='manage_orders.php';</script>";
        exit();
    }

    if ($status === 'Cancelled') {
        $sql_order_details = "SELECT od.product_id, od.quantity FROM order_details od WHERE od.order_id = ?";
        $stmt = $conn->prepare($sql_order_details);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $order_details_result = $stmt->get_result();

        while ($row = $order_details_result->fetch_assoc()) {
            $product_id = $row['product_id'];
            $quantity = $row['quantity'];

            $sql_stock = "SELECT stock_quantity FROM products WHERE id = ?";
            $stmt = $conn->prepare($sql_stock);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $stock_result = $stmt->get_result();
            $product = $stock_result->fetch_assoc();

            $new_stock_quantity = $product['stock_quantity'] + $quantity;

            $sql_update_stock = "UPDATE products SET stock_quantity = ? WHERE id = ?";
            $stmt = $conn->prepare($sql_update_stock);
            $stmt->bind_param("ii", $new_stock_quantity, $product_id);
            $stmt->execute();
        }
    }
    if ($status === 'Done') {
        $conn->begin_transaction();

        try {
            $sql_order_details = "SELECT od.product_id, od.quantity 
                                  FROM order_details od 
                                  WHERE od.order_id = ?";
            $stmt_order_details = $conn->prepare($sql_order_details);
            $stmt_order_details->bind_param("i", $order_id);
            $stmt_order_details->execute();
            $order_details_result = $stmt_order_details->get_result();

            while ($row = $order_details_result->fetch_assoc()) {
                $product_id = $row['product_id'];
                $quantity = $row['quantity'];

                $sql_stock = "SELECT stock_quantity FROM products WHERE id = ?";
                $stmt_stock = $conn->prepare($sql_stock);
                $stmt_stock->bind_param("i", $product_id);
                $stmt_stock->execute();
                $stock_result = $stmt_stock->get_result();
                $product = $stock_result->fetch_assoc();

                if (!$product) {
                    throw new Exception("Product with ID $product_id not found.");
                }

                $new_stock_quantity = $product['stock_quantity'] - $quantity;

                if ($new_stock_quantity < 0) {
                    throw new Exception("Not enough stock for product ID $product_id.");
                }

                $sql_update_stock = "UPDATE products SET stock_quantity = ? WHERE id = ?";
                $stmt_update_stock = $conn->prepare($sql_update_stock);
                $stmt_update_stock->bind_param("ii", $new_stock_quantity, $product_id);
                $stmt_update_stock->execute();
            }

            $sql_update_order = "UPDATE orders SET status = ? WHERE id = ?";
            $stmt_update_order = $conn->prepare($sql_update_order);
            $stmt_update_order->bind_param("si", $status, $order_id);
            $stmt_update_order->execute();

            $conn->commit();

            echo "<script>alert('Order status updated successfully.'); window.location.href='manage_orders.php';</script>";
        } catch (Exception $e) {
            $conn->rollback(); 
            echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='manage_orders.php';</script>";
        }

        $stmt_order_details->close();
        $stmt_stock->close();
        $stmt_update_stock->close();
        $stmt_update_order->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-white text-black">

    <?php include('./nav.php'); ?>

    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-black mb-6">📦 Manage Orders</h1>

        <div class="mb-6">
            <div class="flex items-center mb-4">
                <label for="year" class="mr-4 text-lg font-semibold">📅 Select Year:</label>
                <select id="year" name="year" class="border rounded-lg px-4 py-2 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select Year</option>
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                </select>
            </div>

            <div class="flex items-center">
                <label for="month" class="mr-4 text-lg font-semibold">🌙 Select Month:</label>
                <select id="month" name="month" class="border rounded-lg px-4 py-2 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select Month</option>
                    <option value="01">January 🧳</option>
                    <option value="02">February ❤️</option>
                    <option value="03">March 🌼</option>
                    <option value="04">April 🌷</option>
                    <option value="05">May 🌸</option>
                    <option value="06">June ☀️</option>
                    <option value="07">July 🏖️</option>
                    <option value="08">August 🍉</option>
                    <option value="09">September 🍂</option>
                    <option value="10">October 🎃</option>
                    <option value="11">November 🍁</option>
                    <option value="12">December 🎄</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto bg-gray-800 shadow-lg rounded-lg">
            <table class="min-w-full table-auto border-collapse" id="orders_table">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left">📧 Email</th>
                        <th class="px-6 py-3 text-left">📱 Phone</th>
                        <th class="px-6 py-3 text-left">🏠 Address</th>
                        <th class="px-6 py-3 text-left">🛍️ Delivery Option</th>
                        <th class="px-6 py-3 text-left">💰 Total Price</th>
                        <th class="px-6 py-3 text-left">📅 Order Date</th>
                        <th class="px-6 py-3 text-left">🚨 Status</th>
                        <th class="px-6 py-3 text-left">⚙️ Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-700">
                            <td class="px-6 py-4 border-b text-gray-300"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="px-6 py-4 border-b text-gray-300"><?= htmlspecialchars($row['phone']) ?></td>
                            <td class="px-6 py-4 border-b text-gray-300"><?= htmlspecialchars($row['address']) ?></td>
                            <td class="px-6 py-4 border-b text-gray-300"><?= htmlspecialchars($row['delivery_option']) ?></td>
                            <td class="px-6 py-4 border-b text-gray-300"><?= htmlspecialchars($row['total_price']) ?></td>
                            <td class="px-6 py-4 border-b text-gray-300"><?= htmlspecialchars($row['created_at']) ?></td>
                            <td class="px-6 py-4 border-b text-gray-300"><?= htmlspecialchars($row['status']) ?></td>
                            <td class="px-6 py-4 border-b">
                                <?php if ($row['status'] == 'pending'): ?>
                                    <form method="POST" action="manage_orders.php" class="inline-block">
                                        <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                        <button type="submit" name="update_order" value="Done" class="text-green-400 hover:text-green-600 font-semibold">✔️ Done</button>
                                        <button type="submit" name="update_order" value="Cancelled" class="text-red-400 hover:text-red-600 font-semibold">❌ Cancel</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-gray-400">No actions available</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include('./footer.php'); ?>

    <script>
        $(document).ready(function() {
            $('#year, #month').change(function() {
                var year = $('#year').val();
                var month = $('#month').val();

                if (year && month) {
                    $.ajax({
                        url: 'fetch_orders.php', 
                        type: 'GET',
                        data: {
                            year: year,
                            month: month
                        },
                        success: function(response) {
                            $('#orders_table tbody').html(response);
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>