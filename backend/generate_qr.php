<?php
require_once 'db.php';
require_once 'config.php';

header('Content-Type: application/json');

// 1️⃣ إنشاء token فريد (عشوائي)
$token = bin2hex(random_bytes(16)); // مثل: 4e5a9f0ab...

// 2️⃣ تحديد رابط الـ feedback (اختار اللغة)
$feedback_url = "http://localhost/MyProject/FrontEnd/front-Ar/feedback-Ar.html?token=" . $token;

// 3️⃣ إدخال التوكن في قاعدة البيانات مع حالته (صالح)
$stmt = $conn->prepare("INSERT INTO feedback_tokens (token, is_used, created_at) VALUES (?, 0, NOW())");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->close();

// 4️⃣ توليد QR يحتوي على الرابط
require_once 'phpqrcode/qrlib.php'; // تأكد من وجود مكتبة QRcode (بتنزلها تحت)
$qr_path = "../qrcodes/" . $token . ".png";
QRcode::png($feedback_url, $qr_path, QR_ECLEVEL_L, 6);

// 5️⃣ إعادة الرابط كـ JSON
echo json_encode([
  "success" => true,
  "token" => $token,
  "feedback_url" => $feedback_url,
  "qr_image" => $qr_path
]);
