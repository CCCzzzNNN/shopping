<?php include '../config/db.php'; ?>

<?php
$data = json_decode(file_get_contents('php://input'), true);

$product_id = intval($data['product_id']);
$user_id = intval($data['user_id']);
$quantity_to_remove = intval($data['quantity']);

// 获取当前购物车中的商品数量
$sql = "SELECT quantity FROM cart WHERE user_id = $user_id AND product_id = $product_id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($row) {
    $current_quantity = $row['quantity'];

    if ($current_quantity <= $quantity_to_remove) {
        // 如果移除数量大于或等于当前数量，则删除该条目
        $sql = "DELETE FROM cart WHERE user_id = $user_id AND product_id = $product_id";
    } else {
        // 否则，更新数量
        $new_quantity = $current_quantity - $quantity_to_remove;
        $sql = "UPDATE cart SET quantity = $new_quantity WHERE user_id = $user_id AND product_id = $product_id";
    }

    if ($conn->query($sql) === TRUE) {
        echo "商品已移除";
    } else {
        echo "移除商品失败: " . $conn->error;
    }
} else {
    echo "购物车中没有找到该商品";
}

$conn->close();
?>
