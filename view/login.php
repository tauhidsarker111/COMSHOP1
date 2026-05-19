<?php
include '../control/login_process.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login | Online Computer Shop</title>
    <link rel="stylesheet" type="text/css" href="../css/mystyle.css">
</head>

<body>
<?php include '../view/navbar.php'; ?>

<main class="auth-shell">
    <section class="auth-panel">
        <h1>Login</h1>
        <p>Login here</p>

        <?php if(!empty($flash)) { ?>
            <div class="alert <?php echo h($flash["type"]); ?>"><?php echo h($flash["message"]); ?></div>
        <?php } ?>

        <?php if(!empty($errorMsg)) { ?>
            <div class="alert error"><?php echo h($errorMsg); ?></div>
        <?php } ?>

        <form action="" method="post" id="loginForm">
            <input type="hidden" name="csrf_token" value="<?php echo h($csrfToken); ?>">

            <label for="email">Email:</label>
            <input type="text" id="email" name="myemail" value="<?php echo h($email); ?>">
            <small class="field-error"><?php echo h($emailError); ?></small><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="pass">
            <small class="field-error"><?php echo h($passwordError); ?></small><br>

            <label class="check-row">
                <input type="checkbox" name="remember" value="1">
                <span>Remember me for 30 days</span>
            </label>

            <input type="submit" name="login" value="Login" class="primary-btn">
        </form>

        <p class="form-note">New customer? <a href="../view/Registration.php">Create an account</a>.</p>
    </section>
</main>

<script src="../js/myscript.js"></script>
</body>
</html>
