<?php
include '../model/mydb.php';
session_set_cookie_params(30 * 24 * 60 * 60);
session_start();
$mydb       = new MyDB();
$conn       = $mydb->createConn();
$products   = $mydb->getAllProducts($conn);
$categories = $mydb->getTopCategories($conn);
$cartCount  = 0;
if (isset($_SESSION["username"]))
    $cartCount = $mydb->getCartCount($_SESSION["username"], $conn);
$mydb->closeConn($conn);
?>
