<?php
class MyDB {
    private $DBHOST = "127.0.0.1";
    private $DBUSER = "comshop";
    private $DBPASS = "12345";
    private $DBNAME = "wti";
    private $DBPORT = 3307;

    function createConn() {
        mysqli_report(MYSQLI_REPORT_OFF);
        $conn = new mysqli($this->DBHOST, $this->DBUSER, $this->DBPASS, $this->DBNAME, $this->DBPORT);
        if ($conn->connect_errno) {
            die("Database connection failed. Check DB host/user/password/database in model/mydb.php. MySQL says: " . $conn->connect_error);
        }
        $conn->set_charset("utf8mb4");
        return $conn;
    }

    function createUser($name, $email, $passwordHash, $role, $profilePicture, $conn) {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role, profile_picture, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $name, $email, $passwordHash, $role, $profilePicture);
        return $stmt->execute();
    }

    function getUserByEmail($email, $conn) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getUserById($id, $conn) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    function updateUserProfile($id, $name, $email, $profilePicture, $conn) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, profile_picture = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $profilePicture, $id);
        return $stmt->execute();
    }

    function updateUserPassword($id, $passwordHash, $conn) {
        $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->bind_param("si", $passwordHash, $id);
        return $stmt->execute();
    }

    function emailExistsForOtherUser($email, $userId, $conn) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
        $stmt->bind_param("si", $email, $userId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    function searchUsersByName($name, $conn) {
        $search = "%" . $name . "%";
        $stmt = $conn->prepare("SELECT id, name, email, role, profile_picture FROM users WHERE name LIKE ? ORDER BY name LIMIT 5");
        $stmt->bind_param("s", $search);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getTopCategories($conn) {
        $sql = "SELECT id, name FROM categories WHERE parent_id IS NULL ORDER BY name";
        return $conn->query($sql);
    }

    function getFeaturedProducts($conn) {
        $sql = "SELECT p.id, p.name, p.manufacturer_review, p.price, p.image_path, p.stock, c.name AS category_name, b.name AS brand_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                ORDER BY p.created_at DESC, p.id DESC
                LIMIT 6";
        return $conn->query($sql);
    }

    function getCategoryById($categoryId, $conn) {
        $stmt = $conn->prepare("SELECT id, name FROM categories WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        return $stmt->get_result();
    }

    function getProductsByCategory($categoryId, $conn) {
        $stmt = $conn->prepare("SELECT p.id, p.name, p.manufacturer_review, p.price, p.image_path, p.stock, c.name AS category_name, b.name AS brand_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.category_id = ? OR c.parent_id = ?
                ORDER BY p.created_at DESC, p.id DESC");
        $stmt->bind_param("ii", $categoryId, $categoryId);
        $stmt->execute();
        return $stmt->get_result();
    }

    function hasColumn($table, $column, $conn) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ss", $table, $column);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return (int)$row['total'] > 0;
    }

    function saveRememberToken($userId, $tokenHash, $conn) {
        if (!$this->hasColumn("users", "remember_token", $conn)) {
            return false;
        }
        $stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
        $stmt->bind_param("si", $tokenHash, $userId);
        return $stmt->execute();
    }

    function getUserByRememberToken($tokenHash, $conn) {
        if (!$this->hasColumn("users", "remember_token", $conn)) {
            return false;
        }
        $stmt = $conn->prepare("SELECT * FROM users WHERE remember_token = ? LIMIT 1");
        $stmt->bind_param("s", $tokenHash);
        $stmt->execute();
        return $stmt->get_result();
    }

    function clearRememberToken($userId, $conn) {
        if (!$this->hasColumn("users", "remember_token", $conn)) {
            return false;
        }
        $empty = null;
        $stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
        $stmt->bind_param("si", $empty, $userId);
        return $stmt->execute();
    }

    function closeConn($conn) {
        $conn->close();
    }
}
?>
