<?php
require_once 'config.php';
require_once 'db.php';

// التحقق من تسجيل دخول الأدمن
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$conn = db();

// فلترة حسب النوع
$filter = $_GET['label'] ?? '';
$query = "SELECT * FROM feedbacks";
if ($filter && in_array($filter, ['positive', 'neutral', 'negative'])) {
    $query .= " WHERE label='$filter'";
}
$query .= " ORDER BY id DESC";

$res = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Feedback Dashboard</title>
<style>
body { font-family: Arial, sans-serif; margin: 30px; background: #f5f5f5; }
h2 { text-align: center; color: #333; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; border-radius: 10px; overflow: hidden; }
th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
th { background-color: #333; color: white; }
tr:nth-child(even) { background-color: #f2f2f2; }
.filter-bar { text-align: center; margin-bottom: 15px; }
.filter-bar a { padding: 8px 15px; margin: 3px; border-radius: 5px; background: #333; color: white; text-decoration: none; }
.filter-bar a.active { background: #00b894; }
.logout { float: right; background: #d63031; color: white; padding: 6px 10px; border-radius: 5px; text-decoration: none; }
.export { background: #0984e3; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; margin-left: 10px; }
</style>
</head>
<body>

<h2>📊 Feedback Dashboard</h2>

<div class="filter-bar">
  <a href="admin_dashboard.php" class="<?= $filter==''?'active':'' ?>">All</a>
  <a href="?label=positive" class="<?= $filter=='positive'?'active':'' ?>">Positive</a>
  <a href="?label=neutral" class="<?= $filter=='neutral'?'active':'' ?>">Neutral</a>
  <a href="?label=negative" class="<?= $filter=='negative'?'active':'' ?>">Negative</a>
  <a href="export_csv.php" class="export">⬇️ Export CSV</a>
  <a href="logout.php" class="logout">Logout</a>
</div>

<table>
<tr>
  <th>ID</th>
  <th>Token</th>
  <th>Feedback</th>
  <th>Label</th>
  <th>Score</th>
  <th>Date</th>
</tr>
<?php while($r = $res->fetch_assoc()): ?>
<tr>
  <td><?= $r['id'] ?></td>
  <td><?= htmlspecialchars($r['token']) ?></td>
  <td><?= htmlspecialchars($r['text']) ?></td>
  <td><?= $r['label'] ?></td>
  <td><?= $r['score'] ?></td>
  <td><?= $r['created_at'] ?></td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>
