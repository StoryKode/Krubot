<?php
namespace KrubiK\WebApps\Middlewares;
/*
| Krubot BotEngine: The Architect's Lexicon [×vRC.8×] 🚀📜
|--------------------------------------------------------------------------
| This is **a Playground For Mastery**, a laboratory of ***Software Dev Artistry***;
| not a weapon for production's final battles.
|
| Our Bond: ***"Rebuilding The Rebellion"*** Within S.N.P. (The Foundation of Pure Power & Revel).
| Your Mandate [MIT]: Deconstruct Krubot. Command it. Master it. You are The Architect Now!
|
| *Go build something revolutionary!* 💜⚡️
*/

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use KrubiK\WebApps\AxiomCore;
use KrubiK\WebApps\UniversalIdentity;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfTokens as LaravelCsrfValidator;
use Illuminate\Pipeline\Pipeline;

/**
 * Class Adaptive_CSRF_Verify
 * 
 * An adaptive CSRF validator honoring host-level customizations.
 * Bypasses checks for valid MiniApp / WebApp cryptographic signatures.
 *
 * For Standard Web Traffic::
 * It Dynamically resolves custom single/multiple middleware using Laravel's Service Container
 * and Pipeline engine to provide maximum flexibility and premium developer experience (DX).
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class AdaptiveCsrfVerify
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws TokenMismatchException
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Use the elegant identityCard() getter.
        /** @var UniversalIdentity|null $identity */
        $identity = $request->identityCard();

        // 1. CRYPTOGRAPHIC BYPASS
        // If the identity card reveals a cryptographically-proven WebApp origin (Telegram/Bale HMACs)
        // we safely bypass the CSRF checking.
        if ($identity instanceof UniversalIdentity && $identity->isFromWebApp()) {
            return $next($request);
        }

        // 2. ADAPTIVE HOST ENFORCEMENT
        // For all other cases (guests, web sessions, SEO routes), we delegate the check to the
        // host application's own CSRF middleware strategy, respecting its rules.
        return $this->delegateToHostCsrf($request, $next);
    }

    /**
     * Resolves and executes the configured/native CSRF middleware configured by the parent project, using Laravel's Pipeline.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
    */
    protected function delegateToHostCsrf(Request $request, Closure $next): Response
    {

        // Read configuration from the package space
        $configValue = config('krubot.webapps.custom_csrf', false);

        // Case A: Custom Array configured.
        // We pipeline the request directly through the specified middleware bindings.
        if (is_array($configValue)) {

            return (new Pipeline(app()))
                ->send($request)
                ->through($configValue)
                ->then($next);
        }

        // Case B: Detect class names based on active Laravel version architecture
        $csrfClass = $this->resolveCsrfClass($configValue);

        if ($csrfClass !== null) {
            /** @var object $csrfMiddleware */
            $csrfMiddleware = app($csrfClass);
            
            // Execute the native middleware logic dynamically
            return $csrfMiddleware->handle($request, $next);
        }

        // Fallback: Resilient manual validation, if container resolution is not accessible
        return $this->manualVerify($request, $next);
    }

    /**
     * Resolves the target CSRF middleware class based on configuration and availability.
     * Supports auto-detection of customized user classes in both legacy and modern paths.
     *
     * @return string|null
     */
    protected function resolveCsrfClass(mixed $configValue): ?string
    {
        if($configValue) {

            // Case A: Explicit Class String provided in config (Highest Priority)
            if (is_string($configValue) && class_exists($configValue)) {
                return $configValue;
            }

            // Case B: Auto-detect is enabled (true) - check for user-customized files in App directory
            if ($configValue === true) {
                // Traditional location for customized VerifyCsrfToken
                if (class_exists(\App\Http\Middleware\VerifyCsrfToken::class))
                    return \App\Http\Middleware\VerifyCsrfToken::class;
                if (class_exists(\App\Http\Middleware\ValidateCsrfTokens::class))
                    return \App\Http\Middleware\ValidateCsrfTokens::class;
            }
        }

        if (class_exists(LaravelCsrfValidator::class))
            return LaravelCsrfValidator::class;

        return null;
    }

    /**
     * Manual CSRF validation fallback.
     * 
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws TokenMismatchException
     */
    protected function manualVerify(Request $request, Closure $next): Response
    {
        // Reading method check
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $next($request);
        }

        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

        if (!$token && $request->header('X-XSRF-TOKEN')) {
            try {
                $token = decrypt($request->header('X-XSRF-TOKEN'), false);
            } catch (\Throwable) {
                $token = null;
            }
        }

        if (!is_string($token) || !hash_equals($request->session()->token(), $token)) {
            throw new TokenMismatchException('CSRF token mismatch.');
        }

        return $next($request);
    }
}
