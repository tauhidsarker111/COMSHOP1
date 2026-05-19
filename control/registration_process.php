<?php
include '../model/mydb.php';

$errors   = [];
$success  = false;

if (isset($_POST["register"])) {

    $username = trim($_POST["uname"]    ?? "");
    $email    = trim($_POST["myemail"]  ?? "");
    $pass     = $_POST["pass"]          ?? "";
    $passConf = $_POST["pass_confirm"]  ?? "";

    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (empty($pass)) {
        $errors[] = "Password is required.";
    } elseif (strlen($pass) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    } elseif ($pass !== $passConf) {
        $errors[] = "Passwords do not match.";
    }

    $fileName = "";
    if (!empty($_FILES["myfile"]["name"])) {
        $allowed = ["image/jpeg", "image/png", "image/gif"];
        $fileType = $_FILES["myfile"]["type"];
        if (!in_array($fileType, $allowed)) {
            $errors[] = "Profile picture must be JPG, PNG, or GIF.";
        } else {
            $fileName = basename($_FILES["myfile"]["name"]);
        }
    }

    if (empty($errors)) {
        $mydb = new MyDB();
        $conn = $mydb->createConn();

        if ($mydb->usernameExists($username, $conn)) {
            $errors[] = "Username is already taken. Please choose another.";
        } elseif ($mydb->emailExists($email, $conn)) {
            $errors[] = "An account with this email already exists.";
        } else {
            if (!empty($fileName)) {
                if (!move_uploaded_file($_FILES["myfile"]["tmp_name"], "../uploads/" . $fileName)) {
                    $errors[] = "File upload failed. Try again.";
                }
            }

            if (empty($errors)) {
                $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
                $result = $mydb->createUser($username, $email, $hashedPass, $fileName, $conn);
                if ($result === true) {
                    $success = true;
                } else {
                    $errors[] = "Registration failed. Please try again.";
                }
            }
        }
        $mydb->closeConn($conn);
    }
}
?>
