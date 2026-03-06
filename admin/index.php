<?php
declare(strict_types=1);

require dirname(__DIR__) . '/core/bootstrap.php';

use Core\Csrf;
use Core\Database;
use Core\Session;

if (!Session::get('user_id')) {
    header('Location: /login');
    exit;
}

$db = Database::connection();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_key']) && Csrf::check($_POST['_csrf'] ?? null)) {
    $key = (string) $_POST['toggle_key'];
    $value = isset($_POST['enabled']) ? '1' : '0';
    $stmt = $db->prepare('INSERT INTO settings (`key`,`value`) VALUES (:k,:v) ON DUPLICATE KEY UPDATE `value`=:v2');
    $stmt->execute([':k' => 'extra_' . $key, ':v' => $value, ':v2' => $value]);
}

$extras = ['watch_party','scheduled_release','affiliates','offline_download','home_editor','advanced_reports','segmented_notifications'];
$enabled = [];
$stmt = $db->prepare('SELECT `key`,`value` FROM settings WHERE `key` LIKE :prefix');
$stmt->execute([':prefix' => 'extra_%']);
foreach ($stmt->fetchAll() as $row) {
    $enabled[str_replace('extra_', '', $row['key'])] = $row['value'] === '1';
}
?>
<!doctype html><html><head><meta charset="utf-8"><link rel="stylesheet" href="/assets/css/app.css"></head><body>
<h1>Admin StreamSaaS</h1>
<nav>
<a href="/admin/index.php">Dashboard</a> |
<a href="/admin/tmdb.php">Importar TMDB</a>
</nav>
<section>
<h2>Recursos Extras</h2>
<?php foreach ($extras as $extra): ?>
<form method="post">
<input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
<input type="hidden" name="toggle_key" value="<?= htmlspecialchars($extra, ENT_QUOTES, 'UTF-8') ?>">
<label><?= htmlspecialchars($extra, ENT_QUOTES, 'UTF-8') ?>
<input type="checkbox" name="enabled" <?= !empty($enabled[$extra]) ? 'checked' : '' ?>>
</label>
<button type="submit">Salvar</button>
</form>
<?php endforeach; ?>
</section>
</body></html>
