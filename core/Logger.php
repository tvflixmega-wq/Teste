<?php
declare(strict_types=1);

namespace Core;

final class Logger
{
    public static function error(string $message): void
    {
        $line = sprintf("[%s] ERROR %s\n", date('c'), $message);
        file_put_contents(dirname(__DIR__) . '/logs/error.log', $line, FILE_APPEND);
    }

    public static function audit(string $message): void
    {
        $line = sprintf("[%s] AUDIT %s\n", date('c'), $message);
        file_put_contents(dirname(__DIR__) . '/logs/audit.log', $line, FILE_APPEND);
    }
}
