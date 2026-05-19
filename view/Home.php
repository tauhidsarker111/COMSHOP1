<?php
session_start();
include '../control/home_process.php';

if (!function_exists('h')) {
    function h($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <title>Online Computer Shop</title>
    <link rel="stylesheet" type="text/css" href="../css/mystyle.css">
</head>

<body>

<?php include '../view/navbar.php'; ?>

<main>

    <!-- CATEGORY BAR -->
    <section class="category-bar">
        <div class="category-scroll">
            <a class="<?php echo empty($selectedCategoryId) ? 'active-category' : ''; ?>" href="../view/Home.php">All Products</a>
            <?php
            if ($categories && $categories->num_rows > 0) {
                while ($category = $categories->fetch_assoc()) {
                    $activeClass = ((int)$category['id'] === (int)$selectedCategoryId) ? ' class="active-category"' : '';
                    echo '<a'.$activeClass.' href="../view/Home.php?category_id='.(int)$category['id'].'">'
                        . h($category['name']) .
                    '</a>';
                }
            } else {
                echo '<span>Add categories in the database to show the category bar.</span>';
            }
            ?>
        </div>
    </section>


    <!-- HERO SECTION -->
    <section class="hero">

        <div class="hero-copy">
            <span class="eyebrow">Desktop builds, upgrades, and accessories</span>

            <h1>Build a faster PC with trusted components.</h1>

            <p>
                Browse processors, graphics cards, motherboards, storage, monitors, keyboards, and other computer shop essentials with live pricing.
            </p>

            <div class="hero-actions">
                <a class="primary-btn" href="#featured">Shop Featured</a>

                <?php if (empty($_SESSION['user_id'])) { ?>
                    <a class="secondary-btn" href="../view/Registration.php">Create Account</a>
                <?php } ?>
            </div>
        </div>

        <div class="hero-panel">
            <div class="build-card main-build">
                <span>Gaming Build</span>
                <strong>RTX Ready</strong>
                <small>High airflow case, DDR5 memory, NVMe storage</small>
            </div>

            <div class="build-grid">
                <div class="build-card"><span>CPU</span><strong>16 Core</strong></div>
                <div class="build-card"><span>SSD</span><strong>2TB NVMe</strong></div>
                <div class="build-card"><span>PSU</span><strong>Gold Rated</strong></div>
                <div class="build-card"><span>Display</span><strong>165Hz</strong></div>
            </div>
        </div>

    </section>


    <!-- FEATURE TITLE -->
    <section class="section-head" id="featured">
        <div>
            <span class="eyebrow"><?php echo $selectedCategoryName ? 'Category Products' : 'Featured Components'; ?></span>
            <h2><?php echo $selectedCategoryName ? h($selectedCategoryName) : 'Latest products from the shop'; ?></h2>
        </div>
    </section>


    <!-- PRODUCT GRID -->
    <section class="product-grid">

        <?php
        if ($featuredProducts && $featuredProducts->num_rows > 0) {

            while ($product = $featuredProducts->fetch_assoc()) {

                $imagePath = trim($product['image_path'] ?? '');
                $imageSrc = ($imagePath !== '') ? '../uploads/' . h($imagePath) : '';
                ?>

                <article class="product-card">

                    <div class="product-image">
                        <?php if ($imageSrc) { ?>
                            <img src="<?php echo $imageSrc; ?>" alt="<?php echo h($product['name']); ?>">
                        <?php } else { ?>
                            <span><?php echo h(substr($product['name'], 0, 2)); ?></span>
                        <?php } ?>
                    </div>

                    <div class="product-body">
                        <span class="product-meta">
                            <?php echo h($product['brand_name'] ?? 'Computer Brand'); ?>
                            &middot;
                            <?php echo h($product['category_name'] ?? 'Component'); ?>
                        </span>

                        <h3><?php echo h($product['name']); ?></h3>

                        <p>
                            <?php echo h(substr(
                                $product['manufacturer_review'] ?? 'Reliable component for daily computing and gaming builds.',
                                0,
                                120
                            )); ?>
                        </p>

                        <div class="price-row">
                            <strong>Tk <?php echo number_format((float)$product['price'], 2); ?></strong>
                            <span><?php echo (int)$product['stock']; ?> in stock</span>
                        </div>
                    </div>

                </article>

                <?php
            }

        } else {
            echo '<div class="empty-state">Add products in the database to display featured components here.</div>';
        }
        ?>

    </section>

</main>

<script src="../js/myscript.js"></script>

</body>
</html>
