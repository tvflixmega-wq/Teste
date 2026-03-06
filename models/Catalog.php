<?php
declare(strict_types=1);

namespace Models;

use Core\Database;

final class Catalog
{
    public function featured(int $limit = 12): array
    {
        $stmt = Database::connection()->prepare('SELECT id, title, synopsis, year, poster, content_type FROM catalog_items WHERE status = :status ORDER BY published_at DESC LIMIT :limit');
        $stmt->bindValue(':status', 'published');
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
