<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\MfaManager;
use App\Services\Auth\TrustedDeviceService;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Services\OIDCProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class OIDCLoginController extends Controller
{
    /**
     * Redirect the user to the OIDC provider.
     */
    public function redirectToProvider()
    {
        return Socialite::buildProvider(
            OIDCProvider::class,
            config('services.oidc')
        )
            ->scopes(['openid profile email'])
            ->redirect();
    }

    /**
     * Handle callback from OIDC provider.
     */
    public function handleProviderCallback(Request $request)
    {
        try {
            $provider = Socialite::buildProvider(
                OIDCProvider::class,
                config('services.oidc')
            );

            $oidcUser = $provider->user();
            $idToken = $oidcUser->token ?? $oidcUser->id_token ?? null;
            $user = User::where('email', $oidcUser->getEmail())->first();

            if (! $user) {
                Log::warning('OIDC login attempt for unknown email: '.$oidcUser->getEmail());

                return redirect()
                    ->route('login')
                    ->with('error', 'No account found for this email. Please contact the admin.');
            }

            $requiresMfa = config('mfa.enabled', true)
                && ! app(TrustedDeviceService::class)->validForUser($request, $user);

            if ($requiresMfa) {
                $mfaManager = app(MfaManager::class);
                $availableMethods = $mfaManager->enabledMethods($user)->pluck('method')->values()->all();
                $preferredMethod = $mfaManager->preferredMethod($user);

                Session::put('auth.mfa.pending', [
                    'challenge_id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'remember' => true,
                    'available_methods' => $availableMethods,
                    'current_method' => $preferredMethod->method,
                    'sent_methods' => [],
                    'initiated_at' => now()->toIso8601String(),
                ]);
            } else {
                Auth::login($user, remember: true);
                $request->session()->regenerate();
            }

            session()->put('oidc_user', [
                'id' => $oidcUser->getId(),
                'name' => $oidcUser->getName(),
                'email' => $oidcUser->getEmail(),
                'avatar' => $oidcUser->getAvatar(),
                'user_info' => $oidcUser->user,
            ]);

            if ($idToken) {
                session()->put('oidc_id_token', $idToken);
            }

            return $requiresMfa
                ? redirect()->route('mfa.challenge')
                : redirect()->intended(route('dashboard'));
        } catch (\Throwable $e) {
            Log::error('OIDC Authentication failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('login')
                ->with('error', 'Authentication failed: '.$e->getMessage());
        }
    }

    /**
     * Logout and redirect to OIDC endsession.
     */
    public function logout(Request $request)
    {
        // Log out from Laravel
        Auth::logout();

        // Clear all session data
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Get the OIDC ID token and post-logout redirect URI
        $idToken = session('oidc_id_token');
        $postLogoutRedirectUri = config('services.oidc.post_logout_redirect_uri', url('/'));

        // Build the OIDC end session URL
        $provider = new OIDCProvider(
            $request,
            config('services.oidc.client_id'),
            config('services.oidc.client_secret'),
            config('services.oidc.redirect')
        );

        $endSessionUrl = $provider->getLogoutUrl($idToken, $postLogoutRedirectUri);

        // Clear OIDC session data
        $request->session()->forget(['oidc_user', 'oidc_id_token']);

        // Redirect to OIDC logout endpoint
        return $endSessionUrl
            ? redirect($endSessionUrl)
            : redirect()->route('oidc.login');
    }
}
