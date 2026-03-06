<?php
declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $prefixes = [
        'Core\\' => __DIR__ . '/',
        'Controllers\\' => dirname(__DIR__) . '/controllers/',
        'Models\\' => dirname(__DIR__) . '/models/',
        'Admin\\Controllers\\' => dirname(__DIR__) . '/admin/controllers/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

$configPath = dirname(__DIR__) . '/config/config.php';
if (!file_exists($configPath) && !str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/install')) {
    header('Location: /install/install.php');
    exit;
}

if (file_exists($configPath)) {
    $config = require $configPath;
    Core\Config::load($config);
}

Core\Session::start();
Core\Csrf::boot();
Core\ErrorHandler::register();
