<?php
include('../DataBase/connection.php');

$year = isset($_GET['year']) ? $_GET['year'] : '';
$month = isset($_GET['month']) ? $_GET['month'] : '';

if ($year && $month) {
    $sql = "SELECT o.id, o.email, o.phone, o.address, o.delivery_option, o.total_price, o.status, o.created_at
            FROM orders o 
            WHERE YEAR(o.created_at) = ? AND MONTH(o.created_at) = ?
            ORDER BY o.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $year, $month);
} else {
    $sql = "SELECT o.id, o.email, o.phone, o.address, o.delivery_option, o.total_price, o.status, o.created_at
            FROM orders o
            ORDER BY o.created_at DESC";
    
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()):
?>
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
                <button type="submit" name="update_order" value="done" class="text-green-400 hover:text-green-600 font-semibold">✔️ Done</button>
                <button type="submit" name="update_order" value="cancelled" class="text-red-400 hover:text-red-600 font-semibold">❌ Cancel</button>
            </form>
        <?php else: ?>
            <span class="text-gray-400">No actions available</span>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
