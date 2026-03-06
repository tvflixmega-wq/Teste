<?php
declare(strict_types=1);

require dirname(__DIR__) . '/core/bootstrap.php';

use Core\Database;

header('Content-Type: application/json; charset=utf-8');

$path = $_GET['path'] ?? '';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$input = json_decode((string) file_get_contents('php://input'), true) ?: [];

$respond = static fn(array $d, int $s = 200) => (http_response_code($s) || true) && print(json_encode($d));

if ($path === 'catalog/movies' && $method === 'GET') {
    $stmt = Database::connection()->prepare('SELECT id,title,year,poster FROM catalog_items WHERE content_type = :t LIMIT 100');
    $stmt->execute([':t' => 'movie']);
    $respond(['data' => $stmt->fetchAll()]);
    return;
}

if ($path === 'auth/login' && $method === 'POST') {
    $stmt = Database::connection()->prepare('SELECT id,password_hash FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => (string) ($input['email'] ?? '')]);
    $user = $stmt->fetch();
    if (!$user || !password_verify((string) ($input['password'] ?? ''), $user['password_hash'])) {
        $respond(['error' => 'Credenciais inválidas'], 401);
        return;
    }
    $respond(['token' => hash('sha256', (string) $user['id'] . '|' . microtime(true))]);
    return;
}

$respond(['error' => 'Rota não encontrada'], 404);
