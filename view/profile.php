<?php
include '../control/profile_process.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Profile | Online Computer Shop</title>
    <link rel="stylesheet" type="text/css" href="../css/mystyle.css">
</head>

<body>
<?php include '../view/navbar.php'; ?>

<main class="profile-layout">
    <section class="profile-card">
        <?php if(!empty($flash)) { ?>
            <div class="alert <?php echo h($flash["type"]); ?>"><?php echo h($flash["message"]); ?></div>
        <?php } ?>

        <div class="profile-top">
            <div class="avatar">
                <?php if(!empty($file)) { ?>
                    <img src="../uploads/<?php echo h($file); ?>" alt="Profile Image">
                <?php } else { ?>
                    <span><?php echo h(strtoupper(substr($name, 0, 1))); ?></span>
                <?php } ?>
            </div>

            <div>
                <h1>Profile</h1>
                <p>Welcome to your profile!</p>
                <p>Hello, <?php echo h($name); ?>!</p>
                <p>Email: <?php echo h($email); ?></p>
                <p>Role: <?php echo h($role); ?></p>
            </div>
        </div>

        <div class="profile-actions">
            <a class="primary-btn" href="../view/editprofile.php">Edit Profile</a>
            <a class="secondary-btn" href="../view/Home.php">Back to Shop</a>
            <a class="secondary-btn" href="../control/logout_process.php">Logout</a>
        </div>
    </section>

    <section class="profile-card">
        <h2>User Search</h2>
        <p>Search users with AJAX JSON endpoint</p>
        <input type="text" name="user_search" id="userSearch" placeholder="Search users by name">
        <p id="searchResult" class="search-result"></p>
    </section>
</main>

<script src="../js/myscript.js"></script>
</body>
</html>
