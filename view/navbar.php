<?php
$currentRole = "guest";
$currentName = "";

if(isset($_SESSION["role"])) {
    $currentRole = $_SESSION["role"];
}

if(isset($_SESSION["name"])) {
    $currentName = $_SESSION["name"];
}
?>

<header class="site-header">
    <div class="top-strip">
        <span>Online Computer Shop</span>
        <span>Components, peripherals, and PC essentials</span>
    </div>

    <nav class="navbar">
        <a class="brand" href="../view/Home.php">TechGear Shop</a>

        <div class="nav-links">
            <a href="../view/Home.php">Home</a>

            <?php if($currentRole == "admin") { ?>
                <a href="../view/profile.php">Admin Profile</a>
                <a href="#">Customers</a>
                <a href="#">Reviews</a>
            <?php } else if($currentRole == "customer") { ?>
                <a href="../view/profile.php">Profile</a>
                <a href="#">Cart</a>
                <a href="#">Orders</a>
            <?php } else { ?>
                <a href="../view/login.php">Login</a>
                <a href="../view/Registration.php">Register</a>
            <?php } ?>

            <?php if($currentRole != "guest") { ?>
                <span class="nav-user"><?php echo h($currentName); ?></span>
                <a class="nav-action" href="../control/logout_process.php">Logout</a>
            <?php } ?>
        </div>
    </nav>
</header>
