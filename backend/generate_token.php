<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json');

require_once 'config.php';
require_once 'db.php';
require_once 'utils.php';
require_once 'phpqrcode/qrlib.php';

// ✅ تأكد من وجود مجلد qrcodes
$qrcodesDir = __DIR__ . "/qrcodes";
if (!is_dir($qrcodesDir)) {
    mkdir($qrcodesDir, 0777, true);
}

// ✅ توليد توكن عشوائي مكون من 6 أرقام (بين 000000 و 999999)
$token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

// ✅ إنشاء رقم الفاتورة
$invoice_id = "INV-" . date('YmdHis');

// ✅ تحديد اللغة المطلوبة (افتراضي: عربي)
$lang = isset($_GET['lang']) ? strtolower(trim($_GET['lang'])) : 'ar';

// ✅ تحديد رابط الصفحة حسب اللغة
if ($lang === 'en') {
    $feedback_url = "http://localhost/MyProject/FrontEndd/front-En/feedback.html?token=" . $token;
} else {
    $feedback_url = "http://localhost/MyProject/FrontEnd/front-Ar/feedback-Ar.html?token=" . $token;
}

// ✅ حفظ التوكن في قاعدة البيانات
$conn = db();
$stmt = $conn->prepare("INSERT INTO tokens (token, invoice_id, status, created_at) VALUES (?, ?, 'issued', NOW())");
$stmt->bind_param("ss", $token, $invoice_id);
$stmt->execute();
$stmt->close();

// ✅ توليد كود QR داخل backend/qrcodes/
$qrcode_file = $qrcodesDir . "/" . $token . ".png";
QRcode::png($feedback_url, $qrcode_file, QR_ECLEVEL_L, 6);

// ✅ إرجاع النتيجة كـ JSON
echo json_encode([
    'success' => true,
    'token' => $token,
    'invoice_id' => $invoice_id,
    'lang' => $lang,
    'feedback_url' => $feedback_url,
    'qr_image' => "http://localhost/MyProject/backend/qrcodes/" . $token . ".png"
]);
