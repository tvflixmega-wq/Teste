<?php
declare(strict_types=1);

namespace Core;

final class Csrf
{
    public static function boot(): void
    {
        if (!Session::get('_csrf')) {
            Session::set('_csrf', bin2hex(random_bytes(32)));
        }
    }

    public static function token(): string
    {
        return (string) Session::get('_csrf', '');
    }

    public static function check(?string $token): bool
    {
        return hash_equals((string) Session::get('_csrf', ''), (string) $token);
    }
}
