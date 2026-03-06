<?php use Core\Csrf; ?>
<h2>Entrar</h2>
<form method="post" action="/login" class="form">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
    <label>E-mail <input type="email" name="email" required></label>
    <label>Senha <input type="password" name="password" required></label>
    <button type="submit">Entrar</button>
</form>
