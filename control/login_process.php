<?php
include '../control/auth_helper.php';

remember_login_if_possible();
if(!empty($_SESSION["user_id"])) {
    header("Location: ../view/profile.php");
    exit();
}

$hasError = false;
$emailError = "";
$passwordError = "";
$errorMsg = "";
$email = "";
$flash = get_flash();
$csrfToken = create_csrf_token();

if(isset($_POST["login"])) {

    $email = trim($_REQUEST["myemail"] ?? "");
    $password = $_REQUEST["pass"] ?? "";

    if(!check_csrf_token($_REQUEST["csrf_token"] ?? "")) {
        $hasError = true;
        $errorMsg = "Security check failed. Please submit the form again.";
    }

    if(empty($email)) {
        $hasError = true;
        $emailError = "Email is required";
    }
    else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $hasError = true;
        $emailError = "Enter a valid email";
    }

    if(empty($password)) {
        $hasError = true;
        $passwordError = "Password is required";
    }

    if($hasError == false) {
        $mydb = new MyDB();
        $conn = $mydb->createConn();
        $result = $mydb->getUserByEmail($email, $conn);

        if($result->num_rows > 0) {
            foreach($result as $row) {
                $user = $row;
            }

            if(password_verify($password, $user["password_hash"])) {
                login_user_session($user);

                if(!empty($_POST["remember"])) {
                    $token = bin2hex(random_bytes(32));
                    $mydb->saveRememberToken((int)$user["id"], hash("sha256", $token), $conn);
                    setcookie("remember_shop", $token, time() + (30 * 24 * 60 * 60), "/", "", false, true);
                }

                $mydb->closeConn($conn);
                header("Location: ../view/profile.php");
                exit();
            }
            else {
                $errorMsg = "Invalid email or password";
            }
        }
        else {
            $errorMsg = "Invalid email or password";
        }

        $mydb->closeConn($conn);
    }
}
?>
