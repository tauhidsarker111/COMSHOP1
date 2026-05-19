<?php
class MyDB {

    function createConn() {
        $conn = new mysqli("localhost", "root", "", "wti");
        if ($conn->connect_error) die("DB error: " . $conn->connect_error);
        return $conn;
    }

    function closeConn($conn) { $conn->close(); }

    function getUser($username, $conn) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result();
    }

    function createUser($username, $email, $password, $file, $conn) {
        $stmt = $conn->prepare(
            "INSERT INTO users (username, email, password, file, role)
             VALUES (?, ?, ?, ?, 'customer')"
        );
        $stmt->bind_param("ssss", $username, $email, $password, $file);
        return $stmt->execute();
    }

    function usernameExists($username, $conn) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    function emailExists($email, $conn) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    function getTopCategories($conn) {
        $stmt = $conn->prepare("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name");
        $stmt->execute();
        return $stmt->get_result();
    }

    function getSubCategories($parent_id, $conn) {
        $stmt = $conn->prepare("SELECT * FROM categories WHERE parent_id=? ORDER BY name");
        $stmt->bind_param("i", $parent_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getCategoryById($cat_id, $conn) {
        $stmt = $conn->prepare("SELECT * FROM categories WHERE id=?");
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getAllBrands($conn) {
        $stmt = $conn->prepare("SELECT * FROM brands ORDER BY name");
        $stmt->execute();
        return $stmt->get_result();
    }

    function getBrandById($brand_id, $conn) {
        $stmt = $conn->prepare("SELECT * FROM brands WHERE id=?");
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getAllProducts($conn) {
        $stmt = $conn->prepare(
            "SELECT p.*, c.name AS category_name, b.name AS brand_name
             FROM products p
             LEFT JOIN categories c ON p.category_id=c.id
             LEFT JOIN brands b ON p.brand_id=b.id
             ORDER BY p.created_at DESC"
        );
        $stmt->execute();
        return $stmt->get_result();
    }

    function getProductById($product_id, $conn) {
        $stmt = $conn->prepare(
            "SELECT p.*, c.name AS category_name, b.name AS brand_name
             FROM products p
             LEFT JOIN categories c ON p.category_id=c.id
             LEFT JOIN brands b ON p.brand_id=b.id
             WHERE p.id=?"
        );
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    function searchProducts($q, $cat_id, $brand_id, $min_price, $max_price, $conn) {
        $sql    = "SELECT p.*, c.name AS category_name, b.name AS brand_name
                   FROM products p
                   LEFT JOIN categories c ON p.category_id=c.id
                   LEFT JOIN brands b ON p.brand_id=b.id WHERE 1=1";
        $params = [];
        $types  = "";

        if ($q !== "") {
            $like    = "%$q%";
            $sql    .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.manufacturer_review LIKE ?)";
            $params  = array_merge($params, [$like, $like, $like]);
            $types  .= "sss";
        }
        if ($cat_id !== "") {
            $ids  = $this->getCategoryTree((int)$cat_id, $conn);
            $ph   = implode(",", array_fill(0, count($ids), "?"));
            $sql .= " AND p.category_id IN ($ph)";
            foreach ($ids as $id) { $params[] = $id; $types .= "i"; }
        }
        if ($brand_id  !== "") { $sql .= " AND p.brand_id=?";  $params[] = (int)$brand_id;    $types .= "i"; }
        if ($min_price !== "") { $sql .= " AND p.price>=?";    $params[] = (float)$min_price;  $types .= "d"; }
        if ($max_price !== "") { $sql .= " AND p.price<=?";    $params[] = (float)$max_price;  $types .= "d"; }
        $sql .= " ORDER BY p.created_at DESC";

        $stmt = $conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getCartByUser($username, $conn) {
        $stmt = $conn->prepare(
            "SELECT cart.id AS cart_id, cart.quantity,
                    p.id AS product_id, p.name, p.price, p.stock, p.image_path
             FROM cart JOIN products p ON cart.product_id=p.id
             WHERE cart.username=? ORDER BY cart.added_at DESC"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getCartItem($username, $product_id, $conn) {
        $stmt = $conn->prepare("SELECT * FROM cart WHERE username=? AND product_id=?");
        $stmt->bind_param("si", $username, $product_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    function addToCart($username, $product_id, $quantity, $conn) {
        $stmt = $conn->prepare("INSERT INTO cart (username,product_id,quantity) VALUES(?,?,?)");
        $stmt->bind_param("sii", $username, $product_id, $quantity);
        return $stmt->execute();
    }

    function updateCartQty($cart_id, $quantity, $conn) {
        $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE id=?");
        $stmt->bind_param("ii", $quantity, $cart_id);
        return $stmt->execute();
    }

    function removeCartItem($cart_id, $username, $conn) {
        $stmt = $conn->prepare("DELETE FROM cart WHERE id=? AND username=?");
        $stmt->bind_param("is", $cart_id, $username);
        return $stmt->execute();
    }

    function getCartCount($username, $conn) {
        $stmt = $conn->prepare("SELECT SUM(quantity) AS total FROM cart WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row["total"] ?? 0);
    }

    function getCartTotal($username, $conn) {
        $stmt = $conn->prepare(
            "SELECT SUM(cart.quantity*p.price) AS grand_total
             FROM cart JOIN products p ON cart.product_id=p.id WHERE cart.username=?"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (float)($row["grand_total"] ?? 0);
    }

    function getCategoryTree($cat_id, $conn) {
        $ids  = [(int)$cat_id];
        $stmt = $conn->prepare("SELECT id FROM categories WHERE parent_id=?");
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $res  = $stmt->get_result();
        while ($row = $res->fetch_assoc())
            $ids = array_merge($ids, $this->getCategoryTree($row["id"], $conn));
        return $ids;
    }

}
?>