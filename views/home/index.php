<section class="hero">
    <h1>Plataforma de Streaming SaaS</h1>
    <p>Catálogo com monetização por tokens, assinaturas e gamificação.</p>
</section>
<section class="grid">
    <?php foreach (($items ?? []) as $item): ?>
        <article class="card">
            <img src="<?= htmlspecialchars($item['poster'] ?: '/assets/images/placeholder.jpg', ENT_QUOTES, 'UTF-8') ?>" alt="poster">
            <h3><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></h3>
            <p><?= htmlspecialchars(mb_substr((string) $item['synopsis'], 0, 120), ENT_QUOTES, 'UTF-8') ?></p>
        </article>
    <?php endforeach; ?>
</section>
