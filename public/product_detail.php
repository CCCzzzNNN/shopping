<?php include '../inc/header.php'; ?>
<?php include '../config/db.php'; ?>
<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT * FROM products WHERE id = $product_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $productName = $row['name'];
        $productDescription = $row['description'];
        $productPrice = $row['price'];
        $productAvailability = $row['availability'];
        $productImage = $row['image'];
    } else {
        echo "没有找到商品";
        exit();
    }
} else {
    echo "无效的商品ID";
    exit();
}
$conn->close();
?>

<div class="container mt-5">
    <h2>商品详情</h2>
    <div class="row">
        <div class="col-md-6">
            <img id="product-image" src="<?php echo htmlspecialchars($productImage); ?>" class="img-fluid" alt="Product Image">
        </div>
        <div class="col-md-6">
            <h3 id="product-name"><?php echo htmlspecialchars($productName); ?></h3>
            <p id="product-description"><?php echo htmlspecialchars($productDescription); ?></p>
            <p id="product-price">价格: $<?php echo htmlspecialchars($productPrice); ?></p>
            <p id="product-availability">库存: <?php echo htmlspecialchars($productAvailability); ?></p>
            <button class="btn btn-custom" onclick="showAddToCartForm()">添加到购物车</button>
        </div>
    </div>
</div>

<div id="add-to-cart-form" style="display: none;">
    <div class="container mt-5">
        <h3>添加到购物车</h3>
        <form id="cart-form">
            <div class="form-group">
                <label for="quantity">数量</label>
                <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="<?php echo htmlspecialchars($productAvailability); ?>" required>
            </div>
            <input type="hidden" id="product_id" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
            <button type="submit" class="btn btn-custom">确认</button>
            <button type="button" class="btn btn-secondary" onclick="hideAddToCartForm()">取消</button>
        </form>
    </div>
</div>

<?php include '../inc/footer.php'; ?>

<script>
function showAddToCartForm() {
    document.getElementById('add-to-cart-form').style.display = 'block';
}

function hideAddToCartForm() {
    document.getElementById('add-to-cart-form').style.display = 'none';
}

document.getElementById('cart-form').addEventListener('submit', function(e) {
    e.preventDefault();

    var formData = new FormData(this);

    fetch('add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert('商品已添加到购物车!');
        hideAddToCartForm();
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
</script>
