<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../model/mydb.php';

function h($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function create_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function check_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
}

function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash() {
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function login_user_session($user) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];
}

function remember_login_if_possible() {
    if (!empty($_SESSION['user_id']) || empty($_COOKIE['remember_shop'])) {
        return;
    }

    $tokenHash = hash('sha256', $_COOKIE['remember_shop']);
    $mydb = new MyDB();
    $conn = $mydb->createConn();
    $result = $mydb->getUserByRememberToken($tokenHash, $conn);

    if ($result && $result->num_rows === 1) {
        login_user_session($result->fetch_assoc());
    } else {
        setcookie('remember_shop', '', time() - 3600, '/');
    }

    $mydb->closeConn($conn);
}

function require_login() {
    remember_login_if_possible();
    if (empty($_SESSION['user_id'])) {
        header("Location: ../view/login.php");
        exit();
    }
}

function validate_image_upload($file, &$errors, $optional = true) {
    if (empty($file['name'])) {
        return '';
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors['profile_picture'] = "Image upload failed.";
        return '';
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        $errors['profile_picture'] = "Image must be 2MB or smaller.";
        return '';
    }

    $mime = mime_content_type($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) {
        $errors['profile_picture'] = "Only JPG, PNG, and WEBP images are allowed.";
        return '';
    }

    $fileName = 'profile_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $allowed[$mime];
    $destination = '../uploads/' . $fileName;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $errors['profile_picture'] = "Could not save uploaded image.";
        return '';
    }

    return $fileName;
}
?>
