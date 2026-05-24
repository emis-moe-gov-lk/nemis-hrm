<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\UserMfaRecoveryCode;
use App\Models\UserMfaMethod;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TotpService
{
    public function generateSecret(): string
    {
        return $this->base32Encode(random_bytes(config('mfa.totp.secret_length', 32)));
    }

    public function getOtpAuthUrl(User $user, string $secret): string
    {
        $issuer = trim((string) config('mfa.totp.issuer', config('app.name', 'EMIS')));
        $account = trim((string) $user->email);
        $label = rawurlencode($issuer).':'.rawurlencode($account);

        return sprintf(
            'otpauth://totp/%s?secret=%s&issuer=%s&algorithm=SHA1&digits=%d&period=%d',
            $label,
            $secret,
            rawurlencode($issuer),
            (int) config('mfa.totp.digits', 6),
            (int) config('mfa.totp.period', 30),
        );
    }

    public function qrSvg(string $otpAuthUrl): string
    {
        return QrCode::format('svg')
            ->size(260)
            ->margin(4)
            ->errorCorrection('M')
            ->color(0, 0, 0)
            ->backgroundColor(255, 255, 255)
            ->generate($otpAuthUrl);
    }

    public function verifyCode(string $secret, string $code): bool
    {
        $normalizedCode = preg_replace('/\D+/', '', $code ?? '');
        $digits = (int) config('mfa.totp.digits', 6);

        if (strlen($normalizedCode) !== $digits) {
            return false;
        }

        $window = (int) config('mfa.totp.window', 1);
        $period = (int) config('mfa.totp.period', 30);
        $timeSlice = (int) floor(time() / $period);

        for ($offset = -$window; $offset <= $window; $offset++) {
            if (hash_equals($this->generateCode($secret, $timeSlice + $offset), $normalizedCode)) {
                return true;
            }
        }

        return false;
    }

    public function generateRecoveryCodes(User $user): array
    {
        $count = (int) config('mfa.totp.recovery_codes', 8);
        $codes = collect();

        $user->recoveryCodes()->delete();

        for ($i = 0; $i < $count; $i++) {
            $code = Str::upper(Str::random(10));
            $codes->push($code);

            $user->recoveryCodes()->create([
                'code_hash' => Hash::make($code),
            ]);
        }

        return $codes->all();
    }

    public function consumeRecoveryCode(User $user, string $code): bool
    {
        $normalized = Str::upper(trim($code));

        /** @var UserMfaRecoveryCode|null $recoveryCode */
        $recoveryCode = $user->recoveryCodes()
            ->whereNull('used_at')
            ->get()
            ->first(fn(UserMfaRecoveryCode $candidate) => Hash::check($normalized, $candidate->code_hash));

        if (! $recoveryCode) {
            return false;
        }

        $recoveryCode->forceFill([
            'used_at' => now(),
        ])->save();

        return true;
    }

    protected function generateCode(string $secret, int $timeSlice): string
    {
        $binarySecret = $this->base32Decode($secret);
        $time = pack('N*', 0, $timeSlice);
        $hash = hash_hmac('sha1', $time, $binarySecret, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $value = (
            ((ord($hash[$offset + 0]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        );

        $modulo = 10 ** (int) config('mfa.totp.digits', 6);

        return str_pad((string) ($value % $modulo), (int) config('mfa.totp.digits', 6), '0', STR_PAD_LEFT);
    }

    protected function base32Encode(string $binary): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';

        foreach (str_split($binary) as $char) {
            $bits .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        $encoded = '';

        foreach (str_split($bits, 5) as $chunk) {
            if (strlen($chunk) < 5) {
                $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            }

            $encoded .= $alphabet[bindec($chunk)];
        }

        return $encoded;
    }

    protected function base32Decode(string $encoded): string
    {
        $alphabet = array_flip(str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'));
        $clean = strtoupper(preg_replace('/[^A-Z2-7]/', '', $encoded));
        $bits = '';

        foreach (str_split($clean) as $char) {
            if (! array_key_exists($char, $alphabet)) {
                continue;
            }

            $bits .= str_pad(decbin($alphabet[$char]), 5, '0', STR_PAD_LEFT);
        }

        $binary = '';

        foreach (str_split($bits, 8) as $chunk) {
            if (strlen($chunk) !== 8) {
                continue;
            }

            $binary .= chr(bindec($chunk));
        }

        return $binary;
    }
}
