<?php
include '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "用户未登录";
    exit();
}

$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);
$user_id = $_SESSION['user_id'];

// 检查产品库存是否足够
$sql = "SELECT availability FROM products WHERE id = $product_id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($row['availability'] >= $quantity) {
    // 插入到购物车表
    $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)";
    if ($conn->query($sql) === TRUE) {
        echo "商品已添加到购物车";
    } else {
        echo "添加到购物车失败: " . $conn->error;
    }
} else {
    echo "库存不足";
}

$conn->close();
?>
