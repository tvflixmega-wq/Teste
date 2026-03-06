<?php
declare(strict_types=1);

namespace Core;

use Throwable;

final class ErrorHandler
{
    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
    }

    public static function handleException(Throwable $e): void
    {
        Logger::error($e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
        http_response_code(500);
        echo 'Erro interno. Consulte logs.';
    }
}
