<?php
require_once 'config.php';
require_once 'db.php';

$email = 'admin@example.com';
$password = 'Admin@123';

// توليد هاش صحيح بطول 60
$hash = password_hash($password, PASSWORD_DEFAULT);

$conn = db();

// حذف أي حساب قديم
$stmt = $conn->prepare("DELETE FROM admins WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->close();

// إدراج الحساب الجديد
$stmt = $conn->prepare("INSERT INTO admins (email, password_hash) VALUES (?, ?)");
$stmt->bind_param("ss", $email, $hash);
$stmt->execute();
$stmt->close();

echo "✅ Admin created successfully.<br>";
echo "Email: $email<br>";
echo "Hash length: " . strlen($hash) . "<br>";
echo "Hash: $hash<br>";
