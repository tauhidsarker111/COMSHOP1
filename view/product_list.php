<?php include '../control/product_list_process.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Products — PC Shop</title>
    <link rel="stylesheet" href="../css/customer.css">
</head>
<body>
<div class="topbar">
    <div class="topbar-brand">PC Shop</div>
    <div class="topbar-links">
        <a href="product_list.php">Products</a>
        <a href="cart.php" class="cart-link">
            🛒 Cart <span id="cart_count" class="cart-badge"><?php echo $cartCount; ?></span>
        </a>
        <?php if (isset($_SESSION["username"])): ?>
            <span class="greeting">Hi, <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
            <a href="../control/logout_process.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</div>

<div class="search-bar-wrap">
    <input type="text" class="search-bar-fake"
           placeholder="🔍  Search products, categories, brands..."
           onclick="window.location.href='search_filter.php'"
           readonly>
</div>

<div class="page-wrap">
    <h2 class="section-title">All Products</h2>
    <div class="product-grid">
        <?php if ($products->num_rows > 0): ?>
            <?php while ($p = $products->fetch_assoc()): ?>
            <a href="product_details.php?id=<?php echo $p['id']; ?>" class="pcard">
                <div class="pcard-img">
                    <?php if (!empty($p['image_path'])): ?>
                        <img src="../uploads/products/<?php echo htmlspecialchars($p['image_path']); ?>" alt="">
                    <?php else: ?>
                        <div class="no-img">No Image</div>
                    <?php endif; ?>
                </div>
                <div class="pcard-body">
                    <div class="pcard-name"><?php echo htmlspecialchars($p['name']); ?></div>
                    <div class="pcard-review">
                        <?php
                        $rev = htmlspecialchars($p['manufacturer_review'] ?? '');
                        echo strlen($rev) > 75 ? substr($rev,0,75).'...' : $rev;
                        ?>
                    </div>
                    <div class="pcard-price">$<?php echo number_format($p['price'],2); ?></div>
                </div>
            </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products available.</p>
        <?php endif; ?>
    </div>
</div>

<script src="../js/customer.js"></script>
</body>
</html>
