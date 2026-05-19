<?php include '../control/login_process.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login — PC Shop</title>
    <link rel="stylesheet" href="../css/customer.css">
</head>
<body>
<div class="topbar">
    <div class="topbar-brand">⚙ PC Shop</div>
    <div class="topbar-links"><a href="product_list.php">Browse Products</a></div>
</div>
<div class="center-box">
    <h2>Customer Login</h2>
    <?php $redirect = isset($_GET["redirect"]) ? htmlspecialchars($_GET["redirect"]) : ""; ?>
    <form method="post" action="">
        <input type="hidden" name="redirect" value="<?php echo $redirect; ?>">
        <label>Username</label>
        <input type="text" name="uname" required>
        <label>Password</label>
        <input type="password" name="pass" required>
        <input type="submit" name="login" value="Login" class="btn-main">
    </form>
    <?php if (!empty($errorMsg)): ?>
        <p class="err-msg"><?php echo $errorMsg; ?></p>
    <?php endif; ?>
    <p class="sub-note">No account? <a href="Registration.php">Register here</a></p>
</div>
</body>
</html>
