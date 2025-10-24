<?php
require_once 'config.php';

function db() {
    $host = "127.0.0.1";   // أو localhost
    $user = "root";        // المستخدم الافتراضي
    $pass = "";            // كلمة المرور فارغة في XAMPP
    $db   = "sentiment_feedback";  // اسم قاعدة البيانات عندك

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("❌ Database connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}
?>

