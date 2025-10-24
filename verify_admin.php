<?php
require_once 'config.php';
require_once 'db.php';

$email = 'admin@example.com';
$password_entered = 'Admin@123';

$conn = db();
$stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if (!$res) {
    echo "DB get_result failed\n";
    exit;
}

if ($res->num_rows === 0) {
    echo "No admin found with email: $email\n";
    exit;
}

$row = $res->fetch_assoc();

echo "<pre>";
echo "Row from DB:\n";
print_r($row);

// show hash with markers and length to detect hidden chars
$hash = $row['password_hash'];
echo "\n--- HASH DETAIL ---\n";
echo "Hash (raw): [" . $hash . "]\n";
echo "Hash length: " . strlen($hash) . "\n";
// print bytes in hex
echo "Hash bytes (hex): ";
foreach (str_split($hash) as $c) {
    printf("%02x ", ord($c));
}
echo "\n\n";

// compare using password_verify
$ok = password_verify($password_entered, $hash);
echo "password_verify result: " . ($ok ? "TRUE" : "FALSE") . "\n";

// manual compare using hash of entered? (just for info)
$gen = password_hash($password_entered, PASSWORD_DEFAULT);
echo "\nGenerated new hash for same password (for info only):\n$gen\n";
echo "New hash length: " . strlen($gen) . "\n";

echo "</pre>";
