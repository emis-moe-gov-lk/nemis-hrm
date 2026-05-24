<?php

namespace App\Jobs\Auth;

use App\Services\Auth\SmsGatewayService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendLoginOtpSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $destination,
        public string $code,
    ) {
    }

    public function handle(SmsGatewayService $smsGatewayService): void
    {
        $success = $smsGatewayService->sendOtp($this->destination, $this->code);

        Log::info('mfa.sms.dispatched', [
            'destination' => $this->destination,
            'success' => $success,
        ]);
    }
}
