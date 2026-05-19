<?php
include_once '../control/auth_helper.php';
remember_login_if_possible();

$mydb = new MyDB();
$conn = $mydb->createConn();
$categories = $mydb->getTopCategories($conn);

$selectedCategoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$selectedCategoryName = '';

if ($selectedCategoryId > 0) {
    $categoryResult = $mydb->getCategoryById($selectedCategoryId, $conn);
    if ($categoryResult && $categoryResult->num_rows > 0) {
        $category = $categoryResult->fetch_assoc();
        $selectedCategoryName = $category['name'];
        $featuredProducts = $mydb->getProductsByCategory($selectedCategoryId, $conn);
    } else {
        $selectedCategoryId = 0;
        $featuredProducts = $mydb->getFeaturedProducts($conn);
    }
} else {
    $featuredProducts = $mydb->getFeaturedProducts($conn);
}

$mydb->closeConn($conn);
?>
