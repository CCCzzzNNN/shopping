<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];

// 检查当前密码是否正确
$sql = "SELECT password FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if (password_verify($current_password, $row['password'])) {
    // 更新新密码
    $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);
    $sql = "UPDATE users SET password='$new_password_hashed' WHERE id=$user_id";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('密码更新成功'); window.location.href='user_profile.php';</script>";
    } else {
        echo "更新失败: " . $conn->error;
    }
} else {
    echo "<script>alert('原密码错误'); window.location.href='user_profile.php';</script>";
}

$conn->close();
?>
