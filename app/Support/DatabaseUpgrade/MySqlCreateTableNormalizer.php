<?php

namespace App\Support\DatabaseUpgrade;

final class MySqlCreateTableNormalizer
{
    public static function normalize(string $sql): string
    {
        $normalized = trim($sql);
        $normalized = preg_replace('/AUTO_INCREMENT=\d+/i', 'AUTO_INCREMENT=?', $normalized) ?? $normalized;
        $normalized = preg_replace('/ROW_FORMAT=\w+/i', 'ROW_FORMAT=?', $normalized) ?? $normalized;
        $normalized = preg_replace('/,\s+/', ', ', $normalized) ?? $normalized;
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        return trim($normalized);
    }
}
