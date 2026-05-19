<?php
include '../control/auth_helper.php';
require_login();

$hasError = false;
$formError = "";
$nameError = "";
$emailError = "";
$fileError = "";
$currentPasswordError = "";
$newPasswordError = "";
$confirmPasswordError = "";
$errors = [];

$mydb = new MyDB();
$conn = $mydb->createConn();
$result = $mydb->getUserById((int)$_SESSION["user_id"], $conn);

if($result->num_rows > 0) {
    foreach($result as $row) {
        $user = $row;
        $uname = $row["name"];
        $myemail = $row["email"];
        $file = $row["profile_picture"];
        $oldPasswordHash = $row["password_hash"];
    }
}
else {
    $mydb->closeConn($conn);
    header("Location: ../control/logout_process.php");
    exit();
}

$csrfToken = create_csrf_token();

if(isset($_POST["update"])) {

    $uname = trim($_REQUEST["uname"] ?? "");
    $myemail = trim($_REQUEST["myemail"] ?? "");
    $currentPassword = $_REQUEST["current_pass"] ?? "";
    $newPassword = $_REQUEST["new_pass"] ?? "";
    $confirmPassword = $_REQUEST["confirm_pass"] ?? "";

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
    else if($mydb->emailExistsForOtherUser($myemail, (int)$_SESSION["user_id"], $conn)) {
        $hasError = true;
        $emailError = "This email is already used by another account";
    }

    $changePassword = false;
    if(!empty($currentPassword) || !empty($newPassword) || !empty($confirmPassword)) {
        $changePassword = true;

        if(!password_verify($currentPassword, $oldPasswordHash)) {
            $hasError = true;
            $currentPasswordError = "Current password is incorrect";
        }

        if(strlen($newPassword) < 8) {
            $hasError = true;
            $newPasswordError = "New password must be at least 8 characters";
        }

        if($newPassword !== $confirmPassword) {
            $hasError = true;
            $confirmPasswordError = "Password and confirm password do not match";
        }
    }

    $newFile = $file;
    if($hasError == false && !empty($_FILES["myfile"]["name"])) {
        $uploadedFile = validate_image_upload($_FILES["myfile"], $errors, true);
        if($uploadedFile == "") {
            $hasError = true;
            $fileError = $errors["profile_picture"] ?? "Invalid image file";
        }
        else {
            $newFile = $uploadedFile;
        }
    }

    if($hasError == false) {
        $updateResult = $mydb->updateUserProfile((int)$_SESSION["user_id"], $uname, $myemail, $newFile, $conn);

        if($updateResult === true && $changePassword == true) {
            $updateResult = $mydb->updateUserPassword((int)$_SESSION["user_id"], password_hash($newPassword, PASSWORD_DEFAULT), $conn);
        }

        if($updateResult === true) {
            $_SESSION["name"] = $uname;
            set_flash("success", "Profile updated successfully.");
            $mydb->closeConn($conn);
            header("Location: ../view/profile.php");
            exit();
        }
        else {
            $formError = "Error: " . $conn->error;
        }
    }
}

$mydb->closeConn($conn);
?>
