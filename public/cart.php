<?php include '../inc/header.php'; ?>
<?php include '../config/db.php'; ?>
<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 获取购物车数据，并按产品进行分组和累加数量
$sql = "SELECT p.id, p.name, p.price, p.availability, SUM(c.quantity) AS total_quantity, (p.price * SUM(c.quantity)) AS total_price, p.image
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = $user_id
        GROUP BY p.id";
$result = $conn->query($sql);

$total_items = 0;
$total_price = 0;
?>

<!-- 内容 -->
<div class="container mt-5">
    <h2>购物车</h2>
    <div class="row">
        <div class="col-md-8">
            <form id="cart-form" method="post" action="checkout.php">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                        $total_items += $row['total_quantity'];
                        $total_price += $row['total_price'];
                        ?>
                        <div class="product-item d-flex align-items-center mb-4">
                            <input type="checkbox" class="product-checkbox mr-3" name="product_ids[]" value="<?php echo htmlspecialchars($row['id']); ?>" data-price="<?php echo htmlspecialchars($row['total_price']); ?>" data-quantity="<?php echo htmlspecialchars($row['total_quantity']); ?>" data-availability="<?php echo htmlspecialchars($row['availability']); ?>" onchange="updateSummary()">
                            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="商品图片" class="product-image mr-3" style="max-width: 150px; max-height: 150px; object-fit: cover;">
                            <div>
                                <h5><?php echo htmlspecialchars($row['name']); ?></h5>
                                <p>价格：$<?php echo htmlspecialchars($row['price']); ?></p>
                                <p>数量：<?php echo htmlspecialchars($row['total_quantity']); ?></p>
                                <p>总价：$<?php echo htmlspecialchars($row['total_price']); ?></p>
                                <button type="button" class="btn btn-danger ml-auto" onclick="showRemoveModal(<?php echo $row['id']; ?>, <?php echo htmlspecialchars($row['total_quantity']); ?>)">移除</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>购物车为空。</p>
                <?php endif; ?>
            </form>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">购物车汇总</h5>
                    <p>总计商品数量：<span id="total-items"><?php echo $total_items; ?></span></p>
                    <p>总价：<span id="total-price">$<?php echo $total_price; ?></span></p>
                    <button class="btn btn-primary btn-block" onclick="checkout()">结算</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 移除商品模态框 -->
<div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="removeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeModalLabel">移除商品</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="number" class="form-control mb-2" id="removeQuantity" min="1" value="1">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-danger" id="confirmRemove">确认移除</button>
            </div>
        </div>
    </div>
</div>

<?php include '../inc/footer.php'; ?>

<script>
    let currentProductId = null;
    let currentMaxQuantity = 1;

    // 显示移除商品模态框
    function showRemoveModal(productId, maxQuantity) {
        currentProductId = productId;
        currentMaxQuantity = maxQuantity;
        document.getElementById('removeQuantity').max = maxQuantity;
        $('#removeModal').modal('show');
    }
    
    // 移除购物车商品
    document.getElementById('confirmRemove').addEventListener('click', function() {
        const quantity = document.getElementById('removeQuantity').value;
        removeFromCart(currentProductId, quantity);
        $('#removeModal').modal('hide');
    });

    function removeFromCart(product_id, quantity) {
        fetch('remove_from_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ product_id: product_id, user_id: <?php echo $user_id; ?>, quantity: quantity })
        })
        .then(response => response.text())
        .then(data => {
            alert('商品已移除');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // 更新购物车汇总信息
    function updateSummary() {
        var checkboxes = document.querySelectorAll('.product-checkbox:checked');
        var totalItems = 0;
        var totalPrice = 0;

        checkboxes.forEach(function(checkbox) {
            totalItems += parseInt(checkbox.dataset.quantity);
            totalPrice += parseFloat(checkbox.dataset.price);
        });

        document.getElementById('total-items').innerText = totalItems;
        document.getElementById('total-price').innerText = '$' + totalPrice.toFixed(2);
    }

    // 结算选中的商品
    function checkout() {
        var checkboxes = document.querySelectorAll('.product-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('请选择要结算的商品');
            return;
        }

        var cartItems = [];
        var validCheckout = true;
        var stockWarning = '';

        checkboxes.forEach(function(checkbox) {
            var quantity = parseInt(checkbox.dataset.quantity);
            var availability = parseInt(checkbox.dataset.availability);

            if (quantity > availability) {
                validCheckout = false;
                stockWarning += `商品库存不足： ${checkbox.closest('.product-item').querySelector('h5').innerText}, 库存数量：${availability}, 当前选择数量：${quantity}\n`;
            }

            cartItems.push({
                id: checkbox.closest('.product-item').querySelector('.btn-danger').getAttribute('onclick').match(/\d+/)[0],
                quantity: quantity,
                price: checkbox.dataset.price
            });
        });

        if (!validCheckout) {
            alert(stockWarning);
            return;
        }

        // 将选择的商品信息传递到 checkout.php 页面
        document.getElementById('cart-form').submit();
    }
</script>
