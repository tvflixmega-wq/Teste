<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Core\Csrf;
use Models\User;

final class ProfileController extends Controller
{
    public function index(): void
    {
        $id = $this->requireAuth();
        $user = (new User())->byId($id);
        $this->view('home/profile', ['user' => $user]);
    }

    public function updateTheme(): void
    {
        $id = $this->requireAuth();
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            echo 'CSRF inválido';
            return;
        }

        $theme = (string) ($_POST['theme'] ?? 'netflix');
        if (!in_array($theme, ['netflix', 'prime', 'color'], true)) {
            echo 'Tema inválido';
            return;
        }

        (new User())->updateTheme($id, $theme);
        $this->redirect('/profile');
    }
}
