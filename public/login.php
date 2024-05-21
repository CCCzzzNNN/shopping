<?php
session_start();
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('密码错误');</script>";
        }
    } else {
        echo "<script>alert('用户名不存在');</script>";
    }
}
$conn->close();
?>

<?php include '../inc/header.php'; ?>

<div class="container mt-5">
    <h2>用户登录</h2>
    <form method="post">
        <div class="form-group">
            <label for="username">用户名</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="输入用户名" required>
        </div>
        <div class="form-group">
            <label for="password">密码</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="输入密码" required>
        </div>
        <button type="submit" class="btn btn-primary">登录</button>
    </form>
</div>

<?php include '../inc/footer.php'; ?>
