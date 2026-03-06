<?php
declare(strict_types=1);

require __DIR__ . '/core/bootstrap.php';

use Core\Router;
use Controllers\HomeController;
use Controllers\AuthController;
use Controllers\ProfileController;

$router = new Router();
$router->get('/', [HomeController::class, 'index']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/profile', [ProfileController::class, 'index']);
$router->post('/profile/theme', [ProfileController::class, 'updateTheme']);

$router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');
