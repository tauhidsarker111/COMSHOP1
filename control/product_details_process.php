<?php
include '../model/mydb.php';
session_set_cookie_params(30 * 24 * 60 * 60);
session_start();
$product  = null;
$errorMsg = "";
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    $errorMsg = "Invalid product.";
} else {
    $mydb   = new MyDB();
    $conn   = $mydb->createConn();
    $result = $mydb->getProductById((int)$_GET["id"], $conn);
    if ($result->num_rows > 0) { $product = $result->fetch_assoc(); }
    else { $errorMsg = "Product not found."; }
    $cartCount = 0;
    if (isset($_SESSION["username"]))
        $cartCount = $mydb->getCartCount($_SESSION["username"], $conn);
    $mydb->closeConn($conn);
}
?>
