<?php
include '../model/mydb.php';
session_set_cookie_params(30 * 24 * 60 * 60);
session_start();
$mydb = new MyDB();
$conn = $mydb->createConn();

$topResult   = $mydb->getTopCategories($conn);
$categoryTree = [];
while ($cat = $topResult->fetch_assoc()) {
    $subs = $mydb->getSubCategories($cat['id'], $conn);
    $cat['subs'] = [];
    while ($s = $subs->fetch_assoc()) $cat['subs'][] = $s;
    $categoryTree[] = $cat;
}

$brandResult = $mydb->getAllBrands($conn);
$brands = [];
while ($b = $brandResult->fetch_assoc()) $brands[] = $b;

$cartCount = 0;
if (isset($_SESSION["username"]))
    $cartCount = $mydb->getCartCount($_SESSION["username"], $conn);

$mydb->closeConn($conn);
$initialQ = isset($_GET["q"]) ? htmlspecialchars($_GET["q"]) : "";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Search & Filter — PC Shop</title>
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
        <?php if (isset($_SESSION["username"])): ?>
            <span class="greeting">Hi, <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
            <a href="../control/logout_process.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</div>

<div class="page-wrap">
    <!-- Search bar -->
    <div class="search-bar-wrap">
        <input type="text" id="searchInput" class="search-bar-live"
               placeholder="🔍  Type to search products..."
               value="<?php echo $initialQ; ?>"
               oninput="doSearch()">
        <p id="searchError" class="err-msg" style="display:none;margin:4px 0 0 0;"></p>
    </div>

    <div class="filter-layout">
        <!-- SIDEBAR -->
        <div class="sidebar">
            <div class="sidebar-section">
                <div class="sidebar-title">Categories</div>
                <div class="filter-item active-filter" onclick="setFilter('cat_id','',this)">All</div>
                <?php foreach ($categoryTree as $cat): ?>
                    <div class="filter-item cat-top"
                         onclick="setFilter('cat_id','<?php echo $cat['id']; ?>',this)">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </div>
                    <?php foreach ($cat['subs'] as $sub): ?>
                        <div class="filter-item cat-sub"
                             onclick="setFilter('cat_id','<?php echo $sub['id']; ?>',this)">
                            ↳ <?php echo htmlspecialchars($sub['name']); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-title">Brands</div>
                <div class="filter-item active-filter" onclick="setFilter('brand_id','',this)">All</div>
                <?php foreach ($brands as $brand): ?>
                    <div class="filter-item"
                         onclick="setFilter('brand_id','<?php echo $brand['id']; ?>',this)">
                        <?php echo htmlspecialchars($brand['name']); ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-title">Price Range ($)</div>
                <div class="price-row">
                    <input type="number" id="minPrice" placeholder="Min" min="0" oninput="doSearch()">
                    <span>–</span>
                    <input type="number" id="maxPrice" placeholder="Max" min="0" oninput="doSearch()">
                </div>
            </div>

            <button onclick="clearFilters()" class="btn-secondary" style="width:100%;margin-top:8px;">
                Clear All Filters
            </button>
        </div>

        <div class="results-col">
            <p id="result-count" class="result-count"></p>
            <div id="product-grid" class="product-grid">
                <p>Loading...</p>
            </div>
        </div>
    </div>
</div>

<script>
var SEARCH_URL = "../control/search_filter_process.php";
var INITIAL_Q  = "<?php echo $initialQ; ?>";
</script>
<script src="../js/customer.js"></script>
<script>window.onload = function(){ doSearch(); };</script>
</body>
</html>
