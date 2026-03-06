<?php
declare(strict_types=1);

namespace Models;

use Core\Database;
use PDO;

final class User
{
    public function create(string $email, string $password, string $name): int
    {
        $sql = 'INSERT INTO users (email, password_hash, name, email_verified, created_at) VALUES (:email, :password_hash, :name, 0, NOW())';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ':name' => $name,
        ]);

        return (int) Database::connection()->lastInsertId();
    }

    public function byEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function byId(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT id, email, name, avatar, bio, theme, twofa_enabled FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function updateTheme(int $id, string $theme): void
    {
        $stmt = Database::connection()->prepare('UPDATE users SET theme = :theme WHERE id = :id');
        $stmt->execute([':theme' => $theme, ':id' => $id]);
    }
}
