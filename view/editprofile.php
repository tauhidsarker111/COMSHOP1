<?php
include '../control/editprofile_process.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Profile | Online Computer Shop</title>
    <link rel="stylesheet" type="text/css" href="../css/mystyle.css">
</head>

<body>
<?php include '../view/navbar.php'; ?>

<main class="auth-shell wide">
    <section class="auth-panel">
        <h1>Edit Profile</h1>
        <p>Edit your profile information here.</p>

        <?php if(!empty($formError)) { ?>
            <div class="alert error"><?php echo h($formError); ?></div>
        <?php } ?>

        <form action="" method="post" enctype="multipart/form-data" id="profileForm">
            <input type="hidden" name="csrf_token" value="<?php echo h($csrfToken); ?>">

            <div class="current-picture">
                <?php if(!empty($file)) { ?>
                    <img src="../uploads/<?php echo h($file); ?>" alt="Profile Image">
                <?php } else { ?>
                    <span><?php echo h(strtoupper(substr($uname, 0, 1))); ?></span>
                <?php } ?>
            </div>

            <label for="username">Username:</label>
            <input type="text" id="username" name="uname" value="<?php echo h($uname); ?>">
            <small class="field-error"><?php echo h($nameError); ?></small><br>

            <label for="email">Email:</label>
            <input type="text" id="email" name="myemail" value="<?php echo h($myemail); ?>">
            <small class="field-error"><?php echo h($emailError); ?></small><br>

            <label for="file">Upload File:</label>
            <input type="file" id="file" name="myfile" accept="image/jpeg,image/png,image/webp">
            <small class="field-error"><?php echo h($fileError); ?></small><br>

            <hr>
            <h2>Change Password</h2>

            <label for="current_pass">Current Password:</label>
            <input type="password" id="current_pass" name="current_pass">
            <small class="field-error"><?php echo h($currentPasswordError); ?></small><br>

            <label for="new_pass">New Password:</label>
            <input type="password" id="new_pass" name="new_pass">
            <small class="field-error"><?php echo h($newPasswordError); ?></small><br>

            <label for="confirm_pass">Confirm New Password:</label>
            <input type="password" id="confirm_pass" name="confirm_pass">
            <small class="field-error"><?php echo h($confirmPasswordError); ?></small><br>

            <input type="submit" name="update" value="Update" class="primary-btn">
        </form>
    </section>
</main>

<script src="../js/myscript.js"></script>
</body>
</html>
