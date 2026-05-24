<?php

namespace App\Support\Auth;

final class PasswordGenerator
{
    public static function compliant(int $length = 12): string
    {
        $length = max($length, 8);

        $sets = [
            'ABCDEFGHJKLMNPQRSTUVWXYZ',
            'abcdefghijkmnopqrstuvwxyz',
            '23456789',
            '!@#$%^&*?',
        ];

        $characters = array_map(fn (string $set): string => self::pick($set), $sets);
        $pool = implode('', $sets);

        while (count($characters) < $length) {
            $characters[] = self::pick($pool);
        }

        return self::secureShuffle($characters);
    }

    private static function pick(string $characters): string
    {
        return $characters[random_int(0, strlen($characters) - 1)];
    }

    private static function secureShuffle(array $characters): string
    {
        for ($i = count($characters) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$characters[$i], $characters[$j]] = [$characters[$j], $characters[$i]];
        }

        return implode('', $characters);
    }
}
