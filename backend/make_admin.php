<?php
$email = "admin@example.com";
$password = "Admin@123";
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Email: $email<br>";
echo "Password Hash: $hash<br>";
?>
