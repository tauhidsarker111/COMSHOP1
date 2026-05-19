<?php
include '../control/registration_process.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Registration | Online Computer Shop</title>
    <link rel="stylesheet" type="text/css" href="../css/mystyle.css">
</head>

<body>
<?php include '../view/navbar.php'; ?>

<main class="auth-shell">
    <section class="auth-panel">
        <h1>Registration</h1>
        <p>Register here</p>

        <?php if(!empty($formError)) { ?>
            <div class="alert error"><?php echo h($formError); ?></div>
        <?php } ?>

        <form action="" method="post" enctype="multipart/form-data" id="registrationForm">
            <input type="hidden" name="csrf_token" value="<?php echo h($csrfToken); ?>">

            <label for="username">Username:</label>
            <input type="text" id="username" name="uname" value="<?php echo h($uname); ?>">
            <small class="field-error"><?php echo h($nameError); ?></small><br>

            <label for="email">Email:</label>
            <input type="text" id="email" name="myemail" value="<?php echo h($myemail); ?>">
            <small class="field-error"><?php echo h($emailError); ?></small><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="pass">
            <small class="field-error"><?php echo h($passwordError); ?></small><br>

            <label for="confirm_pass">Confirm Password:</label>
            <input type="password" id="confirm_pass" name="confirm_pass">
            <small class="field-error"><?php echo h($confirmPasswordError); ?></small><br>

            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="customer" <?php if($role == "customer") { echo "selected"; } ?>>Customer</option>
                <option value="admin" <?php if($role == "admin") { echo "selected"; } ?>>Admin</option>
            </select>
            <small class="field-error"><?php echo h($roleError); ?></small><br>

            <label for="file">Upload File:</label>
            <input type="file" id="file" name="myfile" accept="image/jpeg,image/png,image/webp">
            <small class="field-error"><?php echo h($fileError); ?></small><br>

            <input type="submit" name="register" value="Register" class="primary-btn">
        </form>

        <p class="form-note">Already registered? <a href="../view/login.php">Login here</a>.</p>
    </section>
</main>

<script src="../js/myscript.js"></script>
</body>
</html>
