<?php
include_once '../control/auth_helper.php';

if (!empty($_SESSION['user_id'])) {
    $mydb = new MyDB();
    $conn = $mydb->createConn();
    $mydb->clearRememberToken((int)$_SESSION['user_id'], $conn);
    $mydb->closeConn($conn);
}

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}
session_destroy();
setcookie('remember_shop', '', time() - 3600, '/');

header("Location: ../view/login.php");
exit();
?>
