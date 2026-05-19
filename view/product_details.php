<?php include '../control/product_details_process.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $product ? htmlspecialchars($product['name']) : 'Product'; ?> — PC Shop</title>
    <link rel="stylesheet" href="../css/customer.css">
</head>
<body>
<div class="topbar">
    <div class="topbar-brand">PC Shop</div>
    <div class="topbar-links">
        <a href="product_list.php">Products</a>
        <a href="cart.php" class="cart-link">
            🛒 Cart <span id="cart_count" class="cart-badge"><?php echo $cartCount ?? 0; ?></span>
        </a>
        <?php if (isset($_SESSION["username"])): ?>
            <span class="greeting">Hi, <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
            <a href="../control/logout_process.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</div>

<div class="page-wrap">
    <a href="product_list.php" class="back-link">← Back to Products</a>

    <?php if ($errorMsg): ?>
        <p class="err-msg"><?php echo $errorMsg; ?></p>
    <?php elseif ($product): ?>

    <div class="details-grid">
        <!-- Left: Image -->
        <div class="details-img-col">
            <?php if (!empty($product['image_path'])): ?>
                <img src="../uploads/products/<?php echo htmlspecialchars($product['image_path']); ?>"
                     alt="" class="details-img">
            <?php else: ?>
                <div class="no-img large">No Image</div>
            <?php endif; ?>
        </div>

        <!-- Right: Info -->
        <div class="details-info-col">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <div class="detail-price">$<?php echo number_format($product['price'],2); ?></div>

            <?php if ($product['stock'] > 0): ?>
                <span class="badge-stock in">✔ In Stock (<?php echo $product['stock']; ?> left)</span>
            <?php else: ?>
                <span class="badge-stock out">✘ Out of Stock</span>
            <?php endif; ?>

            <div class="divider"></div>

            <div class="detail-label">Description</div>
            <p><?php echo nl2br(htmlspecialchars($product['description'] ?? 'N/A')); ?></p>

            <div class="detail-label">Manufacturer Review</div>
            <p><?php echo nl2br(htmlspecialchars($product['manufacturer_review'] ?? 'N/A')); ?></p>

            <p class="meta-info">
                Category: <strong><?php echo htmlspecialchars($product['category_name'] ?? '—'); ?></strong> &nbsp;|&nbsp;
                Brand: <strong><?php echo htmlspecialchars($product['brand_name'] ?? '—'); ?></strong>
            </p>

            <?php if ($product['stock'] > 0): ?>
            <div class="divider"></div>
            <div class="cart-action-row">
                <label>Qty:</label>
                <input type="number" id="qty_input" value="1" min="1"
                       max="<?php echo $product['stock']; ?>" class="qty-box">
                <button class="btn-main"
                    onclick="addToCart(
                        <?php echo $product['id']; ?>,
                        <?php echo $product['stock']; ?>,
                        <?php echo isset($_SESSION['username']) ? 'true' : 'false'; ?>,
                        '<?php echo urlencode('product_details.php?id='.$product['id']); ?>'
                    )">
                    Add to Cart
                </button>
            </div>
            <p id="cart_msg" class="cart-msg"></p>
            <?php endif; ?>
        </div>
    </div>

    <?php endif; ?>
</div>

<script src="../js/customer.js"></script>
</body>
</html>
