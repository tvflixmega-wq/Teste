<?php
use Core\Csrf;
use Core\Session;

$theme = 'netflix';
if (Session::get('user_id')) {
    try {
        $stmt = Core\Database::connection()->prepare('SELECT theme FROM users WHERE id = :id');
        $stmt->execute([':id' => (int) Session::get('user_id')]);
        $theme = $stmt->fetchColumn() ?: 'netflix';
    } catch (Throwable $e) {
        $theme = 'netflix';
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StreamSaaS</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="/assets/css/themes/<?= htmlspecialchars($theme, ENT_QUOTES, 'UTF-8') ?>.css">
</head>
<body>
<header class="topbar">
    <a href="/" class="logo">StreamSaaS</a>
    <nav>
        <?php if (Session::get('user_id')): ?>
            <a href="/profile">Perfil</a>
            <form method="post" action="/logout" style="display:inline">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit">Sair</button>
            </form>
        <?php else: ?>
            <a href="/login">Entrar</a>
            <a href="/register">Cadastrar</a>
        <?php endif; ?>
        <a href="/admin/index.php">Admin</a>
    </nav>
</header>
<main>
    <?php include $viewFile; ?>
</main>
<script src="/assets/js/app.js"></script>
</body>
</html>
