<?php include '../inc/header.php'; ?>
<?php include '../config/db.php'; ?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_POST['email'];

    // 默认余额
    $default_balance = 10000000.00;

    // 获取支付方式数据
    $alipay_account = $_POST['alipay_account'];
    $bank_account = $_POST['bank_account'];
    $credit_card_account = $_POST['credit_card_account'];
    $wechat_account = $_POST['wechat_account'];

    // 检查用户名是否已存在
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "<script>alert('用户名已存在，请选择其他用户名');</script>";
    } else {
        // 检查电子邮件是否已存在
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo "<script>alert('电子邮件已存在，请选择其他电子邮件');</script>";
        } else {
            // 插入新用户
            $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";
            if ($conn->query($sql) === TRUE) {
                $user_id = $conn->insert_id;

                // 插入支付方式
                $sql_payments = "INSERT INTO user_payments (user_id, payment_type, account, balance) VALUES 
                                ($user_id, '支付宝', '$alipay_account', $default_balance),
                                ($user_id, '银行卡', '$bank_account', $default_balance),
                                ($user_id, '信用卡', '$credit_card_account', $default_balance),
                                ($user_id, '微信支付', '$wechat_account', $default_balance)";
                
                if ($conn->query($sql_payments) === TRUE) {
                    echo "<script>alert('注册成功！'); window.location.href='login.php';</script>";
                } else {
                    echo "支付方式添加失败: " . $conn->error;
                }
            } else {
                echo "注册失败: " . $conn->error;
            }
        }
    }
}
$conn->close();
?>

<div class="container mt-5">
    <h2>用户注册</h2>
    <form method="post">
        <div class="form-group">
            <label for="username">用户名</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="输入用户名" required>
        </div>
        <div class="form-group">
            <label for="password">密码</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="输入密码" required>
        </div>
        <div class="form-group">
            <label for="email">邮箱</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="输入邮箱" required>
        </div>
        <h4>添加支付方式</h4>
        <div class="form-group">
            <label for="alipay_account">支付宝账号</label>
            <input type="text" class="form-control" id="alipay_account" name="alipay_account" placeholder="输入支付宝账号" required>
        </div>
        <div class="form-group">
            <label for="bank_account">银行卡账号</label>
            <input type="text" class="form-control" id="bank_account" name="bank_account" placeholder="输入银行卡账号" required>
        </div>
        <div class="form-group">
            <label for="credit_card_account">信用卡账号</label>
            <input type="text" class="form-control" id="credit_card_account" name="credit_card_account" placeholder="输入信用卡账号" required>
        </div>
        <div class="form-group">
            <label for="wechat_account">微信账号</label>
            <input type="text" class="form-control" id="wechat_account" name="wechat_account" placeholder="输入微信账号" required>
        </div>
        <button type="submit" class="btn btn-primary">注册</button>
    </form>
</div>

<?php include '../inc/footer.php'; ?>
