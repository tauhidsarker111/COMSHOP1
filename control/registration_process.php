<?php
include '../control/auth_helper.php';

$hasError = false;
$nameError = "";
$emailError = "";
$passwordError = "";
$confirmPasswordError = "";
$roleError = "";
$fileError = "";
$successMsg = "";
$formError = "";
$errors = [];

$uname = "";
$myemail = "";
$role = "customer";
$csrfToken = create_csrf_token();

if(isset($_POST["register"])) {

    $uname = trim($_REQUEST["uname"] ?? "");
    $myemail = trim($_REQUEST["myemail"] ?? "");
    $pass = $_REQUEST["pass"] ?? "";
    $confirmPass = $_REQUEST["confirm_pass"] ?? "";
    $role = $_REQUEST["role"] ?? "customer";

    if(!check_csrf_token($_REQUEST["csrf_token"] ?? "")) {
        $hasError = true;
        $formError = "Security check failed. Please submit the form again.";
    }

    if(empty($uname)) {
        $hasError = true;
        $nameError = "Name is required";
    }
    else if(strlen($uname) < 3) {
        $hasError = true;
        $nameError = "Name must be at least 3 characters";
    }

    if(empty($myemail)) {
        $hasError = true;
        $emailError = "Email is required";
    }
    else if(!filter_var($myemail, FILTER_VALIDATE_EMAIL)) {
        $hasError = true;
        $emailError = "Enter a valid email";
    }

    if(empty($pass)) {
        $hasError = true;
        $passwordError = "Password is required";
    }
    else if(strlen($pass) < 8) {
        $hasError = true;
        $passwordError = "Password must be at least 8 characters";
    }

    if($pass !== $confirmPass) {
        $hasError = true;
        $confirmPasswordError = "Password and confirm password do not match";
    }

    if($role !== "admin" && $role !== "customer") {
        $hasError = true;
        $roleError = "Select a valid role";
    }

    $mydb = new MyDB();
    $conn = $mydb->createConn();

    if($hasError == false) {
        $emailResult = $mydb->getUserByEmail($myemail, $conn);
        if($emailResult->num_rows > 0) {
            $hasError = true;
            $emailError = "This email is already registered";
        }
    }

    $fileName = "";
    if($hasError == false) {
        if(!empty($_FILES["myfile"]["name"])) {
            $fileName = validate_image_upload($_FILES["myfile"], $errors, true);
            if($fileName == "") {
                $hasError = true;
                $fileError = $errors["profile_picture"] ?? "Invalid image file";
            }
        }
    }

    if($hasError == false) {
        $passwordHash = password_hash($pass, PASSWORD_DEFAULT);
        $result = $mydb->createUser($uname, $myemail, $passwordHash, $role, $fileName, $conn);

        if($result === true) {
            set_flash("success", "Registration complete. Please login.");
            $mydb->closeConn($conn);
            header("Location: ../view/login.php");
            exit();
        }
        else {
            $formError = "Error: " . $conn->error;
        }
    }

    $mydb->closeConn($conn);
}
?>
