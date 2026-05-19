<?php
include '../control/auth_helper.php';
require_login();

$mydb = new MyDB();
$conn = $mydb->createConn();

if(isset($_GET["search"])) {
    header("Content-Type: application/json");

    $search = trim($_GET["search"]);
    $users = [];

    if(!empty($search)) {
        $result2 = $mydb->searchUsersByName($search, $conn);
        if($result2->num_rows > 0) {
            foreach($result2 as $row) {
                $users[] = $row;
            }
        }
    }

    echo json_encode(["users" => $users]);
    $mydb->closeConn($conn);
    exit();
}

$result = $mydb->getUserById((int)$_SESSION["user_id"], $conn);
if($result->num_rows > 0) {
    foreach($result as $row) {
        $user = $row;
        $name = $row["name"];
        $email = $row["email"];
        $role = $row["role"];
        $file = $row["profile_picture"];
    }
}
else {
    $mydb->closeConn($conn);
    header("Location: ../control/logout_process.php");
    exit();
}

$flash = get_flash();
$mydb->closeConn($conn);
?>
