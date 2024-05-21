<?php
include '../inc/header.php';
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = $_GET['id'];

// 获取订单详细信息
$order_sql = "SELECT * FROM orders WHERE id = $order_id AND user_id = " . $_SESSION['user_id'];
$order_result = $conn->query($order_sql);

if ($order_result->num_rows > 0) {
    $order = $order_result->fetch_assoc();
} else {
    echo "<script>alert('没有找到订单'); window.location.href='user_profile.php';</script>";
    exit();
}

$conn->close();
?>

<div class="container mt-5">
    <h2>订单详情</h2>
    <p><strong>订单号:</strong> <?php echo htmlspecialchars($order['id']); ?></p>
    <p><strong>商品ID:</strong> <?php echo htmlspecialchars($order['product_id']); ?></p>
    <p><strong>数量:</strong> <?php echo htmlspecialchars($order['quantity']); ?></p>
    <p><strong>价格:</strong> $<?php echo htmlspecialchars($order['price']); ?></p>
    <p><strong>支付方式:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
    <p><strong>订单时间:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
    <a href="user_profile.php" class="btn btn-primary">返回</a>
</div>

<?php include '../inc/footer.php'; ?>
