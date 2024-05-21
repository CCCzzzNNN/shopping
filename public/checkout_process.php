<?php include '../inc/header.php'; ?>
<?php include '../config/db.php'; ?>
<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$payment_method_id = intval($_POST['payment_method']);

// 获取支付方式的余额
$sql = "SELECT * FROM user_payments WHERE id = $payment_method_id AND user_id = $user_id";
$result = $conn->query($sql);
$payment_method = $result->fetch_assoc();

// 获取购物车数据
$sql = "SELECT p.id, p.name, p.price, p.availability, c.quantity, (p.price * c.quantity) AS total_price
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = $user_id";
$result = $conn->query($sql);

$total_price = 0;
$cart_items = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $total_price += $row['total_price'];
    }
}

// 检查余额是否足够
if ($payment_method['balance'] >= $total_price) {
    // 更新支付方式余额
    $new_balance = $payment_method['balance'] - $total_price;
    $sql = "UPDATE user_payments SET balance = $new_balance WHERE id = $payment_method_id";
    $conn->query($sql);

    // 更新产品库存
    foreach ($cart_items as $item) {
        $new_availability = $item['availability'] - $item['quantity'];
        $sql = "UPDATE products SET availability = $new_availability WHERE id = " . $item['id'];
        $conn->query($sql);
    }

    // 插入订单数据
    foreach ($cart_items as $item) {
        $order_sql = "INSERT INTO orders (user_id, product_id, quantity, price, payment_method, created_at) VALUES 
                      ($user_id, " . $item['id'] . ", " . $item['quantity'] . ", " . $item['total_price'] . ", '" . $payment_method['payment_type'] . "', NOW())";
        $conn->query($order_sql);
    }

    // 清空购物车
    $sql = "DELETE FROM cart WHERE user_id = $user_id";
    $conn->query($sql);

    // 跳转到成功页面
    header("Location: success.php");
    exit();
} else {
    // 余额不足，显示错误信息
    echo "<script>alert('余额不足，请选择其它支付方式'); window.history.back();</script>";
}

$conn->close();
?>
