<?php
declare(strict_types=1);

$lockFile = __DIR__ . '/installed.lock';
if (file_exists($lockFile)) {
    exit('Instalador bloqueado. Remova install/installed.lock para reinstalar.');
}

$errors = [];
$success = '';
$requirements = [
    'PHP >= 8.0' => version_compare(PHP_VERSION, '8.0.0', '>='),
    'PDO' => extension_loaded('pdo'),
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'openssl' => extension_loaded('openssl'),
    'mbstring' => extension_loaded('mbstring'),
    'json' => extension_loaded('json'),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = trim((string)($_POST['db_host'] ?? 'localhost'));
    $port = (int)($_POST['db_port'] ?? 3306);
    $name = trim((string)($_POST['db_name'] ?? 'stream_saas'));
    $user = trim((string)($_POST['db_user'] ?? 'root'));
    $pass = (string)($_POST['db_pass'] ?? '');
    $adminEmail = filter_var($_POST['admin_email'] ?? '', FILTER_VALIDATE_EMAIL);
    $adminPass = (string)($_POST['admin_pass'] ?? '');

    if (!$adminEmail || strlen($adminPass) < 8) {
        $errors[] = 'Admin inválido.';
    }

    foreach ($requirements as $label => $ok) {
        if (!$ok) {
            $errors[] = "Requisito não atendido: {$label}";
        }
    }

    if (!$errors) {
        try {
            $pdo = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            $schema = file_get_contents(dirname(__DIR__) . '/sql/schema.sql');
            $pdo->exec($schema);

            $stmt = $pdo->prepare('INSERT INTO users (email,password_hash,name,email_verified,role_id,created_at) VALUES (:email,:pass,:name,1,1,NOW())');
            $stmt->execute([
                ':email' => $adminEmail,
                ':pass' => password_hash($adminPass, PASSWORD_DEFAULT),
                ':name' => 'Administrador',
            ]);

            $csrfSecret = bin2hex(random_bytes(32));
            $salt = bin2hex(random_bytes(32));

            $config = "<?php\nreturn " . var_export([
                'app' => ['name' => 'StreamSaaS', 'env' => 'production', 'csrf_secret' => $csrfSecret, 'salt' => $salt],
                'db' => ['host' => $host, 'port' => $port, 'name' => $name, 'user' => $user, 'pass' => $pass],
                'tmdb' => ['api_key' => trim((string)($_POST['tmdb_key'] ?? ''))],
            ], true) . ";\n";

            file_put_contents(dirname(__DIR__) . '/config/config.php', $config);
            file_put_contents($lockFile, 'installed_at=' . date('c'));
            $success = 'Instalação concluída com sucesso.';
        } catch (Throwable $e) {
            $errors[] = 'Falha na instalação: ' . $e->getMessage();
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Instalação</title></head><body>
<h1>Instalador StreamSaaS</h1>
<ul><?php foreach ($requirements as $req => $ok): ?><li><?= htmlspecialchars($req, ENT_QUOTES, 'UTF-8') ?>: <?= $ok ? 'OK' : 'FALHOU' ?></li><?php endforeach; ?></ul>
<?php foreach ($errors as $error): ?><p style="color:red"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endforeach; ?>
<?php if ($success): ?><p style="color:green"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
<form method="post">
<input name="db_host" placeholder="DB Host" value="localhost" required>
<input name="db_port" placeholder="DB Port" value="3306" required>
<input name="db_name" placeholder="DB Name" value="stream_saas" required>
<input name="db_user" placeholder="DB User" value="root" required>
<input name="db_pass" placeholder="DB Pass" type="password">
<input name="admin_email" placeholder="Admin Email" type="email" required>
<input name="admin_pass" placeholder="Admin Senha" type="password" minlength="8" required>
<input name="tmdb_key" placeholder="TMDB API Key">
<button type="submit">Instalar</button>
</form>
</body></html>
