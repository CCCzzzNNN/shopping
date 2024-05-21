<?php
include '../inc/header.php';
include '../config/db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 获取购物车数据
$sql = "SELECT p.id, p.name, p.price, c.quantity, (p.price * c.quantity) AS total_price
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
} else {
    echo "<p>购物车为空。</p>";
    exit();
}
?>

<!-- 内容 -->
<div class="container mt-5">
    <h2>结算</h2>
    <form id="checkout-form" action="checkout_process.php" method="post">
        <div class="form-group">
            <label for="payment_method">选择支付方式</label>
            <select class="form-control" id="payment_method" name="payment_method" required>
                <?php
                $sql = "SELECT * FROM user_payments WHERE user_id = $user_id";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['payment_type']) . ' (余额: $' . htmlspecialchars($row['balance']) . ')</option>';
                    }
                } else {
                    echo '<option value="">没有可用的支付方式</option>';
                }
                ?>
            </select>
        </div>
        <h3>确认订单</h3>
        <?php foreach ($cart_items as $item): ?>
            <div>
                <p><?php echo htmlspecialchars($item['name']); ?> - 数量：<?php echo htmlspecialchars($item['quantity']); ?> - 总价：$<?php echo htmlspecialchars($item['total_price']); ?></p>
            </div>
        <?php endforeach; ?>
        <p>总价：$<span id="total-price"><?php echo $total_price; ?></span></p>
        <button type="submit" class="btn btn-primary">确认付款</button>
    </form>
</div>

<?php include '../inc/footer.php'; ?>

<script>
document.getElementById('payment_method').addEventListener('change', function() {
    var totalPrice = <?php echo $total_price; ?>;
    document.getElementById('total-price').innerText = '$' + totalPrice.toFixed(2);
});
</script>
