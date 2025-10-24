<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = $_POST['password'] ?? '';
    if (!$pass) die('Enter password');
    echo "<pre>".password_hash($pass, PASSWORD_DEFAULT)."</pre>";
    exit;
}
?>
<form method="POST">
  <input type="text" name="password" placeholder="Password">
  <button type="submit">Generate Hash</button>
</form>
    