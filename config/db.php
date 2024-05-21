<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'online_store');
define('DB_USER', 'root'); // 替换为你的数据库用户名
define('DB_PASS', '1234'); // 替换为你的数据库密码

// 创建连接
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}
?>
