<?php
include '../inc/header.php';
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 获取用户信息
$user_sql = "SELECT username, email FROM users WHERE id = $user_id";
$user_result = $conn->query($user_sql);
$user_info = $user_result->fetch_assoc();

// 获取用户订单历史
$order_sql = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC";
$order_result = $conn->query($order_sql);

// 获取用户支付方式
$payments_sql = "SELECT * FROM user_payments WHERE user_id = $user_id";
$payments_result = $conn->query($payments_sql);
?>

<div class="container mt-5">
    <h2>用户个人中心</h2>
    <div class="row">
        <div class="col-md-6">
            <h3>个人信息</h3>
            <form id="profile-form" method="post" action="update_profile.php">
                <div class="form-group">
                    <label for="username">用户名</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user_info['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">邮箱</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_info['email']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">更新信息</button>
            </form>
            <button class="btn btn-warning mt-3" onclick="showChangePasswordForm()">更改密码</button>
        </div>
        <div class="col-md-6">
            <h3>订单历史</h3>
            <?php if ($order_result->num_rows > 0): ?>
                <ul>
                    <?php while ($order = $order_result->fetch_assoc()): ?>
                        <li>
                            <p>订单号: <a href="order_detail.php?id=<?php echo htmlspecialchars($order['id']); ?>"><?php echo htmlspecialchars($order['id']); ?></a></p>
                            <p>商品ID: <?php echo htmlspecialchars($order['product_id']); ?></p>
                            <p>数量: <?php echo htmlspecialchars($order['quantity']); ?></p>
                            <p>价格: $<?php echo htmlspecialchars($order['price']); ?></p>
                            <p>支付方式: <?php echo htmlspecialchars($order['payment_method']); ?></p>
                            <p>订单时间: <?php echo htmlspecialchars($order['created_at']); ?></p>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>没有订单记录。</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-6">
            <h3>支付方式管理</h3>
            <button class="btn btn-secondary" onclick="togglePayments()">查看支付方式</button>
            <div id="payments-container" style="display:none;">
                <ul>
                    <?php
                    if ($payments_result->num_rows > 0) {
                        while ($payment = $payments_result->fetch_assoc()) {
                            echo "<li>" . htmlspecialchars($payment['payment_type']) . " - " . htmlspecialchars($payment['account']) . " (余额: $" . htmlspecialchars($payment['balance']) . ") <a href='delete_payment.php?id=" . $payment['id'] . "' class='btn btn-danger btn-sm'>删除</a> <button class='btn btn-info btn-sm' onclick='showEditPaymentForm(" . $payment['id'] . ", \"" . htmlspecialchars($payment['account']) . "\")'>修改</button></li>";
                        }
                    } else {
                        echo "<li>没有支付方式。</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- 更改密码模态框 -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title" id="changePasswordModalLabel">更改密码</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="change-password-form" method="post" action="change_password.php">
                    <div class="form-group">
                        <label for="current_password">原密码</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">新密码</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">确认修改</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 修改支付方式模态框 -->
<div class="modal fade" id="editPaymentModal" tabindex="-1" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title" id="editPaymentModalLabel">修改支付方式</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-payment-form" method="post" action="update_payments.php">
                    <input type="hidden" id="edit_payment_id" name="payment_id">
                    <div class="form-group">
                        <label for="edit_account">账号</label>
                        <input type="text" class="form-control" id="edit_account" name="account" required>
                    </div>
                    <button type="submit" class="btn btn-primary">确认修改</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../inc/footer.php'; ?>

<script>
function showChangePasswordForm() {
    $('#changePasswordModal').modal('show');
}

function togglePayments() {
    var container = document.getElementById('payments-container');
    if (container.style.display === 'none') {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
}

function showEditPaymentForm(paymentId, account) {
    document.getElementById('edit_payment_id').value = paymentId;
    document.getElementById('edit_account').value = account;
    $('#editPaymentModal').modal('show');
}
</script>
