<?php use Core\Csrf; ?>
<h2>Perfil</h2>
<p><strong>Nome:</strong> <?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
<p><strong>E-mail:</strong> <?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
<form method="post" action="/profile/theme" class="form">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
    <label>Tema
        <select name="theme">
            <option value="netflix">Netflix-like</option>
            <option value="prime">Prime-like</option>
            <option value="color">Moderno colorido</option>
        </select>
    </label>
    <button type="submit">Salvar tema</button>
</form>
