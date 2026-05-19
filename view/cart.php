<?php
include '../model/mydb.php';
session_set_cookie_params(30 * 24 * 60 * 60);
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php?redirect=" . urlencode("cart.php"));
    exit;
}
$mydb      = new MyDB();
$conn      = $mydb->createConn();
$cartItems = $mydb->getCartByUser($_SESSION["username"], $conn);
$cartCount = $mydb->getCartCount($_SESSION["username"], $conn);
$grandTotal = $mydb->getCartTotal($_SESSION["username"], $conn);
$mydb->closeConn($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Cart — PC Shop</title>
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
        <span class="greeting">Hi, <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
        <a href="../control/logout_process.php">Logout</a>
    </div>
</div>

<div class="page-wrap">
    <h2 class="section-title">Your Cart</h2>
    <p id="cart_msg" class="cart-msg"></p>

    <?php if ($cartItems->num_rows === 0): ?>
        <div class="empty-cart">
            <p>Your cart is empty.</p>
            <a href="product_list.php" class="btn-main">Browse Products</a>
        </div>
    <?php else: ?>

    <table class="cart-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($item = $cartItems->fetch_assoc()): ?>
            <tr id="cart_row_<?php echo $item['cart_id']; ?>">
                <td>
                    <a href="product_details.php?id=<?php echo $item['product_id']; ?>" class="cart-prod-link">
                        <?php echo htmlspecialchars($item['name']); ?>
                    </a>
                </td>
                <td>$<?php echo number_format($item['price'],2); ?></td>
                <td>
                    <div class="qty-ctrl">
                        <button onclick="changeQty(<?php echo $item['cart_id']; ?>, -1, <?php echo $item['stock']; ?>)">−</button>
                        <span id="qty_<?php echo $item['cart_id']; ?>"><?php echo $item['quantity']; ?></span>
                        <button onclick="changeQty(<?php echo $item['cart_id']; ?>, 1, <?php echo $item['stock']; ?>)">+</button>
                    </div>
                </td>
                <td id="row_total_<?php echo $item['cart_id']; ?>">
                    $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                </td>
                <td>
                    <button class="btn-remove"
                            onclick="removeItem(<?php echo $item['cart_id']; ?>)">
                        Remove
                    </button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="total-label">Grand Total</td>
                <td colspan="2" class="total-amount" id="grand_total">
                    $<?php echo number_format($grandTotal,2); ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="cart-actions">
        <a href="product_list.php" class="btn-secondary">← Continue Shopping</a>
    </div>

    <?php endif; ?>
</div>

<script src="../js/customer.js"></script>
</body>
</html>
