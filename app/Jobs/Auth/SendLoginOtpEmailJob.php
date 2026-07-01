<?php

namespace App\Jobs\Auth;

use App\Mail\Auth\LoginOtpMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLoginOtpEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $code,
    ) {
    }

    public function handle(): void
    {
        try {
            Mail::to($this->user->email)->send(new LoginOtpMail($this->user, $this->code));
        } catch (\Throwable $e) {
            Log::warning('Failed to send login OTP email: ' . $e->getMessage());
        }
        Log::info('mfa.email.dispatched', [
            'user_id' => $this->user->id,
        ]);
    }
}
