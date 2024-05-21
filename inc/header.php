<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>在线商店</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 56px;
            background-color: #f8f9fa; /* 浅灰色背景 */
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #343a40; /* 深色背景 */
            color: white;
            text-align: center;
            padding: 10px 0;
        }
        .product-card {
            margin-bottom: 20px;
        }
        .product-card img {
            height: 200px;
            object-fit: cover;
        }
        .card-custom {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.4);
            transition: 0.3s;
            border: none; /* 去除边框 */
            border-radius: 10px; /* 圆角边框 */
        }
        .card-custom:hover {
            box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.5);
        }
        .modal-header-custom {
            background-color: #007bff;
            color: white;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
            border: none; /* 去除边框 */
            border-radius: 20px; /* 圆角按钮 */
        }
        .btn-custom:hover {
            background-color: #0056b3;
            color: white;
        }
        .btn-danger-custom {
            background-color: #dc3545;
            color: white;
            border: none; /* 去除边框 */
            border-radius: 20px; /* 圆角按钮 */
        }
        .btn-danger-custom:hover {
            background-color: #c82333;
            color: white;
        }
        .navbar-brand {
            font-size: 1.5em;
            font-weight: bold;
        }
        .nav-link {
            font-size: 1.1em;
        }
        .container {
            max-width: 1200px;
        }
        .card-title {
            font-size: 1.25em;
            font-weight: bold;
        }
        .card-text {
            font-size: 1em;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">在线商城</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">首页</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="user_profile.php">个人中心</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">购物车</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="checkout.php">结算</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">退出</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">登录</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">注册</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="footer">
        <span class="text-muted">© 2024 在线商店</span>
    </div>

    <!-- Bootstrap 和 jQuery 脚本 -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
