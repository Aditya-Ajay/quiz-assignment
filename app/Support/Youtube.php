<?php

namespace App\Support;

class Youtube
{
    public static function embed(?string $url, array $attrs = []): string
    {
        $id = self::idFromUrl($url);
        if (! $id) {
            return '';
        }

        $class = $attrs['class'] ?? 'w-full h-full rounded border border-slate-200';

        return sprintf(
            '<iframe src="https://www.youtube.com/embed/%s" class="%s" frameborder="0" allowfullscreen></iframe>',
            e($id),
            e($class)
        );
    }

    private static function idFromUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (preg_match('~youtu\.be/([A-Za-z0-9_-]{6,})~', $url, $m)) {
            return $m[1];
        }
        if (preg_match('~[?&]v=([A-Za-z0-9_-]{6,})~', $url, $m)) {
            return $m[1];
        }
        if (preg_match('~youtube\.com/embed/([A-Za-z0-9_-]{6,})~', $url, $m)) {
            return $m[1];
        }

        return null;
    }
}
