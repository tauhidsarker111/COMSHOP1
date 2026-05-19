<?php
include_once '../control/auth_helper.php';
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Registration Submitted</title>
    <link rel="stylesheet" type="text/css" href="../css/mystyle.css">
</head>


<body>
<?php include '../view/navbar.php'; ?>
<main class="auth-shell">
    <section class="auth-panel">
        <span class="eyebrow">Success</span>
        <h1>Registration submitted</h1>
        <div class="alert success"><?php echo h($flash['message'] ?? 'Your account was created. Please login.'); ?></div>
        <a class="primary-btn" href="../view/login.php">Go to Login</a>
    </section>
    
</main>
</body>
</html>
