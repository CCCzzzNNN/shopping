<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$payment_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// 删除支付方式
$sql = "DELETE FROM user_payments WHERE id=$payment_id AND user_id=$user_id";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('支付方式删除成功'); window.location.href='user_profile.php';</script>";
} else {
    echo "删除失败: " . $conn->error;
}

$conn->close();
?>
