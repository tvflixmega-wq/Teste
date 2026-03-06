<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Models\Catalog;

final class HomeController extends Controller
{
    public function index(): void
    {
        $items = (new Catalog())->featured();
        $this->view('home/index', ['items' => $items]);
    }
}
