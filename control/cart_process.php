<?php
include '../model/mydb.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION["username"])) {
    echo json_encode(["error" => "not_logged_in"]);
    exit;
}

$username = $_SESSION["username"];
$action   = $_GET["action"] ?? "";
$mydb     = new MyDB();
$conn     = $mydb->createConn();

if ($action === "add") {
    $product_id = isset($_GET["product_id"]) && is_numeric($_GET["product_id"])
                  ? (int)$_GET["product_id"] : 0;
    $quantity   = isset($_GET["quantity"]) && is_numeric($_GET["quantity"])
                  ? (int)$_GET["quantity"] : 1;

    if ($product_id < 1 || $quantity < 1) {
        echo json_encode(["error" => "Invalid product or quantity."]);
        $mydb->closeConn($conn); exit;
    }

    $pRes = $mydb->getProductById($product_id, $conn);
    if ($pRes->num_rows === 0) {
        echo json_encode(["error" => "Product not found."]);
        $mydb->closeConn($conn); exit;
    }
    $product = $pRes->fetch_assoc();

    $existing = $mydb->getCartItem($username, $product_id, $conn);
    if ($existing->num_rows > 0) {
        $cartRow = $existing->fetch_assoc();
        $newQty  = $cartRow["quantity"] + $quantity;
        if ($newQty > $product["stock"]) {
            echo json_encode(["error" => "Cannot exceed stock (" . $product["stock"] . ")."]);
            $mydb->closeConn($conn); exit;
        }
        $mydb->updateCartQty($cartRow["id"], $newQty, $conn);
    } else {
        if ($quantity > $product["stock"]) {
            echo json_encode(["error" => "Cannot exceed stock (" . $product["stock"] . ")."]);
            $mydb->closeConn($conn); exit;
        }
        $mydb->addToCart($username, $product_id, $quantity, $conn);
    }

    echo json_encode([
        "message"    => htmlspecialchars($product["name"]) . " added to cart!",
        "cart_count" => $mydb->getCartCount($username, $conn)
    ]);

} elseif ($action === "update") {
    $cart_id  = isset($_GET["cart_id"])  && is_numeric($_GET["cart_id"])  ? (int)$_GET["cart_id"]  : 0;
    $quantity = isset($_GET["quantity"]) && is_numeric($_GET["quantity"]) ? (int)$_GET["quantity"] : 0;

    if ($cart_id < 1 || $quantity < 1) {
        echo json_encode(["error" => "Quantity must be a positive number."]);
        $mydb->closeConn($conn); exit;
    }

    $stmt = $conn->prepare(
        "SELECT cart.*, p.stock, p.price FROM cart
         JOIN products p ON cart.product_id=p.id
         WHERE cart.id=? AND cart.username=?");
    $stmt->bind_param("is", $cart_id, $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) {
        echo json_encode(["error" => "Cart item not found."]);
        $mydb->closeConn($conn); exit;
    }
    $item = $res->fetch_assoc();
    if ($quantity > $item["stock"]) {
        echo json_encode(["error" => "Cannot exceed stock (" . $item["stock"] . ")."]);
        $mydb->closeConn($conn); exit;
    }
    $mydb->updateCartQty($cart_id, $quantity, $conn);
    $rowTotal   = number_format($quantity * $item["price"], 2);
    $grandTotal = number_format($mydb->getCartTotal($username, $conn), 2);

    echo json_encode([
        "success"     => true,
        "row_total"   => $rowTotal,
        "grand_total" => $grandTotal,
        "cart_count"  => $mydb->getCartCount($username, $conn)
    ]);

} elseif ($action === "remove") {
    $cart_id = isset($_GET["cart_id"]) && is_numeric($_GET["cart_id"]) ? (int)$_GET["cart_id"] : 0;
    if ($cart_id < 1) {
        echo json_encode(["error" => "Invalid cart item."]);
        $mydb->closeConn($conn); exit;
    }
    $mydb->removeCartItem($cart_id, $username, $conn);
    $grandTotal = number_format($mydb->getCartTotal($username, $conn), 2);
    echo json_encode([
        "message"    => "Item removed.",
        "cart_count" => $mydb->getCartCount($username, $conn),
        "grand_total"=> $grandTotal
    ]);

} else {
    echo json_encode(["error" => "Unknown action."]);
}
$mydb->closeConn($conn);
?>
