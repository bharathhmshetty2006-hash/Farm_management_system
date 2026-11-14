<?php
// Farm Management System - Database & Common Config
// VTU 5th Sem Mini Project

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'farm_db');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("<div style='font-family: Arial; padding: 30px; background-color: #ffebee; color: #c62828; border-radius: 8px; margin: 40px auto; max-width: 600px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);'>" .
        "<h2 style='margin-top:0;'>⚠️ Database Connection Failed</h2>" .
        "<p>Could not connect to MySQL database <strong>" . DB_NAME . "</strong>.</p>" .
        "<hr style='border-top: 1px solid #ef9a9a;'>" .
        "<p><strong>Please check:</strong></p>" .
        "<ul>" .
        "<li>Is XAMPP Apache & MySQL server started?</li>" .
        "<li>Have you imported <code>database/farm_db.sql</code> into phpMyAdmin / MySQL?</li>" .
        "</ul>" .
        "<small>Error Details: " . htmlspecialchars($e->getMessage()) . "</small>" .
        "</div>");
}

/**
 * Sanitize User Input
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect Helper
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}

/**
 * Flash Message Handler
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

function display_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        echo '<div class="alert alert-' . $flash['type'] . ' alert-dismissible fade show shadow-sm" role="alert">';
        echo '<strong>' . ($flash['type'] === 'success' ? '✓ Success:' : '⚠️ Notice:') . '</strong> ' . htmlspecialchars($flash['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION['flash']);
    }
}

/**
 * Authentication Check
 */
function check_login() {
    if (!isset($_SESSION['user_id'])) {
        set_flash('warning', 'Please login to access the system.');
        // Calculate relative path to login.php depending on directory depth
        $current_script = $_SERVER['SCRIPT_NAME'];
        if (strpos($current_script, '/modules/') !== false || strpos($current_script, '/reports/') !== false) {
            redirect('../../login.php');
        } else {
            redirect('login.php');
        }
    }
}

/**
 * Currency Formatter
 */
function format_currency($amount) {
    return '₹' . number_format((float)$amount, 2);
}

/**
 * Date Formatter
 */
function format_date($date_str) {
    if (empty($date_str) || $date_str === '0000-00-00') return 'N/A';
    return date('d M Y', strtotime($date_str));
}
