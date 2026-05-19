<?php include '../control/registration_process.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Register — PC Shop</title>
    <link rel="stylesheet" href="../css/customer.css">
</head>
<body>

<div class="topbar">
    <div class="topbar-brand">PC Shop</div>
    <div class="topbar-links">
        <a href="product_list.php">Browse Products</a>
        <a href="login.php">Login</a>
    </div>
</div>

<div class="center-box" style="max-width:440px;">
    <h2>Create Account</h2>

    <?php if ($success): ?>
        <div class="reg-success">
            <p>✔ Account created successfully!</p>
            <a href="login.php" class="btn-main" style="margin-top:12px;display:inline-block;">
                Go to Login
            </a>
        </div>

    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <?php foreach ($errors as $e): ?>
                    <p class="err-msg">✖ <?php echo htmlspecialchars($e); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="" enctype="multipart/form-data" onsubmit="return validateReg()">

            <label>Username <span class="required">*</span></label>
            <input type="text" name="uname" id="reg_uname"
                   value="<?php echo htmlspecialchars($_POST['uname'] ?? ''); ?>"
                   placeholder="At least 3 characters">

            <label>Email <span class="required">*</span></label>
            <input type="text" name="myemail" id="reg_email"
                   value="<?php echo htmlspecialchars($_POST['myemail'] ?? ''); ?>"
                   placeholder="you@example.com">

            <label>Password <span class="required">*</span></label>
            <input type="password" name="pass" id="reg_pass"
                   placeholder="At least 6 characters">

            <label>Confirm Password <span class="required">*</span></label>
            <input type="password" name="pass_confirm" id="reg_pass_confirm"
                   placeholder="Repeat password">

            <label>Profile Picture <span class="optional">(optional)</span></label>
            <input type="file" name="myfile" id="reg_file" accept="image/*"
                   style="border:none;padding:4px 0;width:100%;">

            <!-- JS validation error display -->
            <p id="js_error" class="err-msg" style="min-height:16px;"></p>

            <input type="submit" name="register" value="Create Account" class="btn-main"
                   style="width:100%;margin-top:6px;">
        </form>

        <p class="sub-note">Already have an account? <a href="login.php">Login here</a></p>

    <?php endif; ?>
</div>

<style>
.center-box label { display:block; font-size:13px; font-weight:bold; margin-bottom:3px; color:#555; margin-top:12px; }
.center-box input[type="text"],
.center-box input[type="password"] { width:100%; padding:9px 12px; border:2px solid #f5c400; border-radius:6px; font-size:14px; outline:none; margin-bottom:0; }
.center-box input:focus { border-color:#d4a900; }
.required { color:#e00; }
.optional { color:#aaa; font-weight:normal; font-size:11px; }
.error-box { background:#fff8f8; border:1px solid #fbb; border-radius:6px; padding:10px 14px; margin-bottom:14px; }
.error-box .err-msg { margin:3px 0; }
.reg-success { text-align:center; padding:20px 0; color:#7a6000; font-size:16px; font-weight:bold; }
</style>

<script>
function validateReg() {
    var errEl    = document.getElementById("js_error");
    var uname    = document.getElementById("reg_uname").value.trim();
    var email    = document.getElementById("reg_email").value.trim();
    var pass     = document.getElementById("reg_pass").value;
    var passConf = document.getElementById("reg_pass_confirm").value;

    errEl.innerHTML = "";

    if (uname.length < 3) {
        errEl.innerHTML = "Username must be at least 3 characters.";
        return false;
    }
    if (email.indexOf("@") < 1 || email.indexOf(".") < 3) {
        errEl.innerHTML = "Please enter a valid email address.";
        return false;
    }
    if (pass.length < 6) {
        errEl.innerHTML = "Password must be at least 6 characters.";
        return false;
    }
    if (pass !== passConf) {
        errEl.innerHTML = "Passwords do not match.";
        return false;
    }
    return true;
}
</script>

</body>
</html>
