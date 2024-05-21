<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$payment_id = $_POST['payment_id'];
$account = $_POST['account'];

// 更新支付方式
$sql = "UPDATE user_payments SET account='$account' WHERE id=$payment_id AND user_id=$user_id";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('支付方式更新成功'); window.location.href='user_profile.php';</script>";
} else {
    echo "更新失败: " . $conn->error;
}

$conn->close();
?>
