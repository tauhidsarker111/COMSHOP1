<?php
include '../model/mydb.php';
session_start();
header('Content-Type: application/json');

$mydb = new MyDB();
$conn = $mydb->createConn();

$q         = isset($_GET["q"])         ? trim($_GET["q"])         : "";
$cat_id    = isset($_GET["cat_id"])    && is_numeric($_GET["cat_id"])    ? (int)$_GET["cat_id"]    : "";
$brand_id  = isset($_GET["brand_id"])  && is_numeric($_GET["brand_id"])  ? (int)$_GET["brand_id"]  : "";
$min_price = isset($_GET["min_price"]) && is_numeric($_GET["min_price"]) ? (float)$_GET["min_price"] : "";
$max_price = isset($_GET["max_price"]) && is_numeric($_GET["max_price"]) ? (float)$_GET["max_price"] : "";

if ($min_price !== "" && $max_price !== "" && $min_price > $max_price) {
    echo json_encode(["error" => "Min price cannot be greater than max price."]);
    $mydb->closeConn($conn); exit;
}

$result   = $mydb->searchProducts($q, $cat_id, $brand_id, $min_price, $max_price, $conn);
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = [
        "id"                  => $row["id"],
        "name"                => htmlspecialchars($row["name"]),
        "manufacturer_review" => htmlspecialchars($row["manufacturer_review"] ?? ""),
        "price"               => $row["price"],
        "stock"               => $row["stock"],
        "image_path"          => $row["image_path"],
        "category_name"       => htmlspecialchars($row["category_name"] ?? ""),
        "brand_name"          => htmlspecialchars($row["brand_name"] ?? "")
    ];
}
echo json_encode(["products" => $products]);
$mydb->closeConn($conn);
?>
