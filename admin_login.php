<?php
session_start(); // ✅ ضروري لتفعيل الـ sessions
require_once 'config.php';
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');

    if (!$email || !$pass) {
        $error = "⚠️ Please fill all fields.";
    } else {
        $conn = db();

        // ✅ عمود كلمة السر اسمه password_hash في جدولك
        $stmt = $conn->prepare("SELECT id, password_hash FROM admins WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // ✅ التحقق من كلمة السر المشفرة
            if (password_verify($pass, $row['password_hash'])) {
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['email'] = $email;

                // ✅ تحويل بعد تسجيل الدخول
                header("Location: admin_dashboard.php");
                exit;
            } else {
                $error = "❌ Incorrect password.";
            }
        } else {
            $error = "❌ Email not found.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<style>
body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; margin-top: 100px; }
form { background: white; display: inline-block; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
input { padding: 10px; margin: 8px; width: 250px; }
button { padding: 10px 20px; background-color: #333; color: white; border: none; border-radius: 5px; cursor: pointer; }
button:hover { background-color: #555; }
.error { color: red; margin-top: 10px; }
</style>
</head>
<body>
    <form method="POST">
        <h2>Admin Login</h2>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </form>
</body>
</html>
