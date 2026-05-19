<?php
include '../model/mydb.php';
session_set_cookie_params(30 * 24 * 60 * 60);
session_start();
$errorMsg = "";
if (isset($_POST["login"])) {
    $mydb   = new MyDB();
    $conn   = $mydb->createConn();
    $result = $mydb->getUser(trim($_POST["uname"] ?? ""), $conn);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($_POST["pass"] ?? "", $row["password"])) {
            $_SESSION["username"] = $row["username"];
            $_SESSION["role"]     = $row["role"] ?? "customer";
            $mydb->closeConn($conn);
            $redirect = (!empty($_POST["redirect"])) ? $_POST["redirect"] : "../view/product_list.php";
            header("Location: " . $redirect);
            exit;
        } else { $errorMsg = "Invalid username or password."; }
    } else { $errorMsg = "Invalid username or password."; }
    $mydb->closeConn($conn);
}
?>
