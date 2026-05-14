<?php

namespace App\Helpers;

use Carbon\Carbon;

class NicHelper
{
    /* =====================================================
     | NORMALIZATION
     |===================================================== */

    public static function normalize(string $nic): string
    {
        $nic = trim($nic);

        if ($nic === '') {
            return '';
        }

        // Remove spaces & hidden characters (NBSP, zero-width, etc.)
        $nic = preg_replace('/[\s\x{00A0}\x{200B}-\x{200F}]+/u', '', $nic);

        // Uppercase first
        $nic = strtoupper($nic);

        // Keep only digits and V/X
        $nic = preg_replace('/[^0-9VX]/', '', $nic);

        return $nic;
    }

    /* =====================================================
     | TYPE DETECTION
     |===================================================== */

    public static function getNicType(string $nic): ?string
    {
        $n = self::normalize($nic);

        if (preg_match('/^[0-9]{9}[VX]$/', $n)) {
            return 'old';
        }

        if (preg_match('/^[0-9]{12}$/', $n)) {
            return 'new';
        }

        return null;
    }

    public static function isValid(string $nic): bool
    {
        return self::checkNicValid($nic);
    }

    /* =====================================================
     | CANONICAL FORMAT (12 DIGITS ONLY)
     |===================================================== */

    /**
     * Convert NIC to canonical NEW format (12 digits):
     * - New NIC: YYYYDDDSSSSS (12 digits) -> unchanged
     * - Old NIC: YYDDDSSSSV/X -> 19YY + DDD + "0" + SSSS
     */
    public static function toNewFormat(string $nic): string
    {
        $n = self::normalize($nic);

        // Already new NIC (12 digits)
        if (preg_match('/^[0-9]{12}$/', $n)) {
            return $n;
        }

        // Old NIC: YYDDDSSSSV/X
        if (preg_match('/^[0-9]{9}[VX]$/', $n)) {
            $yy   = substr($n, 0, 2); // YY
            $ddd  = substr($n, 2, 3); // DDD
            $ssss = substr($n, 5, 4); // SSSS

            // Canonical: 19YYDDD0SSSS  (12 digits)
            return '19' . $yy . $ddd . '0' . $ssss;
        }

        return '';
    }

    /* =====================================================
     | HASHING (DUPLICATE SAFE)
     |===================================================== */

    public static function hash(string $nic): string
    {
        $canonical = self::toNewFormat($nic);

        if ($canonical === '') {
            return '';
        }

        return hash('sha256', $canonical);
    }

    /* =====================================================
     | DOB & GENDER EXTRACTION
     |===================================================== */

    public static function extractDetails(string $nic): ?array
    {
        $type = self::getNicType($nic);

        if ($type === null) {
            return null;
        }

        $n = self::normalize($nic);

        // Read year and day-of-year depending on NIC type
        if ($type === 'new') {
            $year = (int) substr($n, 0, 4);
            $day  = (int) substr($n, 4, 3);
        } else {
            // old: YYDDDSSSSV/X
            $year = (int) ('19' . substr($n, 0, 2));
            $day  = (int) substr($n, 2, 3);
        }

        $isFemale  = $day > 500;
        $dayOfYear = $isFemale ? $day - 500 : $day;

        // Day-of-year must be 1..366
        if ($dayOfYear < 1 || $dayOfYear > 366) {
            return null;
        }

        try {
            // Jan 1 + (dayOfYear - 1)
            $date = Carbon::create($year, 1, 1)->addDays($dayOfYear - 1);

            // Safety: ensure it didn't overflow the year
            if ($date->year !== $year) {
                return null;
            }

            return [
                'birthday'  => $date->format('Y-m-d'),
                'gender'    => $isFemale ? 'female' : 'male',
                'gender_id' => $isFemale ? 2 : 1,
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    /* =====================================================
     | FULL VALIDATION
     |===================================================== */

    public static function checkNicValid(string $nic): bool
    {
        // Must match OLD or NEW pattern
        if (self::getNicType($nic) === null) {
            return false;
        }

        // Must produce a valid DOB/day-of-year
        return self::extractDetails($nic) !== null;
    }
}