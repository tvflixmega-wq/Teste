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

$config = Core\Config::get('tmdb', []);
$apiKey = $config['api_key'] ?? '';
$type = $_GET['type'] ?? 'movie';
$query = trim((string) ($_GET['q'] ?? ''));
$lang = $_GET['lang'] ?? 'pt-BR';
$results = [];
$message = '';

if ($query !== '' && $apiKey !== '') {
    $url = sprintf('https://api.themoviedb.org/3/search/%s?api_key=%s&query=%s&language=%s', rawurlencode($type), rawurlencode($apiKey), rawurlencode($query), rawurlencode($lang));
    $json = @file_get_contents($url);
    if ($json) {
        $data = json_decode($json, true);
        $results = $data['results'] ?? [];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Csrf::check($_POST['_csrf'] ?? null)) {
    $tmdbId = (int) ($_POST['tmdb_id'] ?? 0);
    $title = trim((string) ($_POST['title'] ?? ''));
    $year = (int) ($_POST['year'] ?? 0);
    $poster = trim((string) ($_POST['poster'] ?? ''));

    $stmt = Database::connection()->prepare('INSERT INTO catalog_items (tmdb_id, content_type, title, year, poster, status, published_at) VALUES (:tmdb_id,:content_type,:title,:year,:poster,:status,NOW()) ON DUPLICATE KEY UPDATE title = VALUES(title), year = VALUES(year), poster = VALUES(poster)');
    $stmt->execute([
        ':tmdb_id' => $tmdbId,
        ':content_type' => $type,
        ':title' => $title,
        ':year' => $year,
        ':poster' => $poster,
        ':status' => 'draft',
    ]);
    $message = 'Importação concluída.';
}
?>
<!doctype html><html><head><meta charset="utf-8"><link rel="stylesheet" href="/assets/css/app.css"></head><body>
<h1>Importar do TMDB</h1>
<a href="/admin/index.php">Voltar</a>
<?php if ($message): ?><p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
<form method="get">
<input name="q" value="<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>" placeholder="Buscar">
<select name="type"><option value="movie" <?= $type==='movie'?'selected':'' ?>>Filme</option><option value="tv" <?= $type==='tv'?'selected':'' ?>>Série</option></select>
<input name="lang" value="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<button>Pesquisar</button>
</form>
<div class="grid">
<?php foreach ($results as $item): $title = $item['title'] ?? $item['name'] ?? 'Sem título'; $year = (int) substr((string)($item['release_date'] ?? $item['first_air_date'] ?? '0'),0,4); $poster = !empty($item['poster_path']) ? 'https://image.tmdb.org/t/p/w500'.$item['poster_path'] : '/assets/images/placeholder.jpg'; ?>
<article class="card">
<img src="<?= htmlspecialchars($poster, ENT_QUOTES, 'UTF-8') ?>" alt="poster">
<h3><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h3>
<form method="post">
<input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
<input type="hidden" name="tmdb_id" value="<?= (int) $item['id'] ?>">
<input type="hidden" name="title" value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
<input type="hidden" name="year" value="<?= $year ?>">
<input type="hidden" name="poster" value="<?= htmlspecialchars($poster, ENT_QUOTES, 'UTF-8') ?>">
<button type="submit">Importar</button>
</form>
</article>
<?php endforeach; ?>
</div>
</body></html>
