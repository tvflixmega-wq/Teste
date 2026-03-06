<?php use Core\Csrf; ?>
<h2>Cadastrar</h2>
<form method="post" action="/register" class="form">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
    <label>Nome <input type="text" name="name" required></label>
    <label>E-mail <input type="email" name="email" required></label>
    <label>Senha <input type="password" name="password" minlength="8" required></label>
    <button type="submit">Criar conta</button>
</form>
