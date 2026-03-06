<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Core\Csrf;
use Core\Logger;
use Core\Session;
use Models\User;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('auth/login');
    }

    public function showRegister(): void
    {
        $this->view('auth/register');
    }

    public function register(): void
    {
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            echo 'CSRF inválido';
            return;
        }

        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $name = trim((string) ($_POST['name'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (!$email || strlen($password) < 8 || $name === '') {
            echo 'Dados inválidos';
            return;
        }

        $model = new User();
        if ($model->byEmail($email)) {
            echo 'E-mail já cadastrado';
            return;
        }

        $userId = $model->create($email, $password, htmlspecialchars($name, ENT_QUOTES, 'UTF-8'));
        Session::set('user_id', $userId);
        Logger::audit("Novo cadastro #{$userId}");
        $this->redirect('/profile');
    }

    public function login(): void
    {
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            echo 'CSRF inválido';
            return;
        }

        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = (string) ($_POST['password'] ?? '');
        if (!$email) {
            echo 'Credenciais inválidas';
            return;
        }

        $user = (new User())->byEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            echo 'Credenciais inválidas';
            return;
        }

        Session::set('user_id', (int) $user['id']);
        Logger::audit('Login usuário #' . (int) $user['id']);
        $this->redirect('/profile');
    }

    public function logout(): void
    {
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            echo 'CSRF inválido';
            return;
        }

        Session::destroy();
        $this->redirect('/');
    }
}
