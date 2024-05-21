<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include '../inc/header.php';
include '../config/db.php';

// 从数据库中获取商品列表
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!-- 内容 -->
<div class="container mt-5">
    <h2>欢迎来到我的在线商城</h2>
    <div class="row mt-4" id="product-list">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card product-card card-custom">
                        <img src="<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="Product Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                            <a href="product_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-custom">详情</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>没有商品。</p>
        <?php endif; ?>
    </div>
</div>

<?php
$conn->close();
include '../inc/footer.php'; 
?>
