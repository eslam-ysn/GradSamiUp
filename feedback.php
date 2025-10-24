<?php
require_once 'config.php';
require_once 'db.php';
require_once 'utils.php';
json_header();

$data = json_decode(file_get_contents('php://input'), true);
$text = trim($data['message'] ?? '');
$token = trim($data['token'] ?? '');

if (!$text || !$token) {
    echo json_encode(['success' => false, 'error' => 'Missing token or message']);
    exit;
}

$conn = db();

// ✅ تحقق من التوكين
$stmt = $conn->prepare("SELECT status FROM tokens WHERE token=? LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row || $row['status'] !== 'issued') {
    echo json_encode(['success' => false, 'error' => 'Invalid or used token']);
    exit;
}

// ✅ إعداد قيم افتراضية
$label = "neutral";
$score = 0.0;
$response_time = 0.0;

// ✅ تشغيل الموديل إن وُجد
if (defined('PYTHON_PATH') && defined('MODEL_SCRIPT') && file_exists(MODEL_SCRIPT)) {
    $escaped = escapeshellarg($text);
    $cmd = PYTHON_PATH . " " . MODEL_SCRIPT . " " . $escaped;
    exec($cmd, $output, $status);
    if ($status === 0 && !empty($output)) {
        $result = json_decode(implode('', $output), true);
        if ($result && isset($result['label'])) {
            $label = $result['label'];
            $score = $result['score'] ?? 0.0;
            $response_time = $result['response_time'] ?? 0.0;
        }
    }
}

// ✅ إدخال في قاعدة البيانات مع معالجة أي خطأ
$stmt = $conn->prepare("INSERT INTO feedbacks (token, text, label, score, created_at) VALUES (?, ?, ?, ?, NOW())");
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'DB prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("sssd", $token, $text, $label, $score);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => 'DB insert failed: ' . $stmt->error]);
    exit;
}
$stmt->close();

// ✅ تحديث حالة التوكين
$stmt = $conn->prepare("UPDATE tokens SET status='used', used_at=NOW() WHERE token=?");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->close();

echo json_encode([
    'success' => true,
    'label' => $label,
    'score' => $score,
    'response_time' => $response_time
]);
?>
