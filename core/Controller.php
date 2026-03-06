<?php
declare(strict_types=1);

namespace Core;

class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = dirname(__DIR__) . '/views/' . $view . '.php';
        include dirname(__DIR__) . '/views/layouts/app.php';
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    protected function requireAuth(): int
    {
        $userId = (int) Session::get('user_id', 0);
        if ($userId <= 0) {
            $this->redirect('/login');
        }
        return $userId;
    }
}
