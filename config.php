<?php
// Include database configuration
require_once __DIR__ . '/config/database.php';

// Application settings
define('APP_NAME', 'Danmodi Students Care Portal');
define('APP_URL', 'http://localhost/danmodi-portal');
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function hasRole($roles) {
    if (!isLoggedIn()) return false;
    
    if (is_string($roles)) {
        $roles = [$roles];
    }
    
    return in_array($_SESSION['user']['role'], $roles);
}

function requireRole($roles) {
    if (!hasRole($roles)) {
        header('Location: unauthorized.php');
        exit;
    }
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function formatCurrency($amount) {
    return '₦' . number_format($amount, 2);
}

function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('M j, Y g:i A', strtotime($datetime));
}

function generateApplicationNumber() {
    return 'DSC' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

function uploadFile($file, $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png']) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }
    
    $fileSize = $file['size'];
    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (!in_array($fileExt, $allowedTypes)) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }
    
    if ($fileSize > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size too large'];
    }
    
    $newFileName = uniqid() . '.' . $fileExt;
    $uploadPath = UPLOAD_DIR . $newFileName;
    
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    
    if (move_uploaded_file($fileTmp, $uploadPath)) {
        return [
            'success' => true, 
            'filename' => $newFileName,
            'original_name' => $fileName,
            'path' => $uploadPath
        ];
    }
    
    return ['success' => false, 'message' => 'Failed to upload file'];
}

// Flash message functions
function setFlashMessage($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

function getFlashMessage($type) {
    if (isset($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}

function hasFlashMessage($type) {
    return isset($_SESSION['flash'][$type]);
}
?>
