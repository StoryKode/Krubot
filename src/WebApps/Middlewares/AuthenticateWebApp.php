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

/**
 * An INTERNAL KrubiK Middleware to authorize requests based on the user's platform.
 * This middleware MUST run AFTER the Router Engine has resolved the user's identity.
 * It reads 'allowed_platforms' from the route definition and compares it against
 * the resolved UniversalIdentity.
 *
 * A global WebApp middleware that opportunistically authenticates a user via InitData.
 *
 * This middleware runs on EVERY request hitting the WebApp gateway. Its sole responsibility
 * is to check for an InitData header, validate it, and attach the resulting UniversalIdentity
 * object to the request's attributes. It NEVER throws an AuthenticationException itself,
 * as the decision to require a user is up to the final route's access policy.
 * 
 * The single, unified , primary identity resolution middleware for all KrubiK web-facing routes.
 *
 * This middleware intelligently detects the context of the request (MiniApp vs. Standard Web)
 * and attempts to resolve a unified UniversalIdentity.
 *
 * - If InitData header is present: It's treated as a MiniApp request.
 *   The signature MUST be valid. Failure results in an AuthenticationException.
 *
 * - If InitData header is NOT present: It's treated as a standard web request.
 *   It attempts to resolve identity from the standard Laravel session (auth()->user()).
 *
 * The resolved identity (or null for guests) is attached to the request attributes.
 *
 * This class's ONLY responsibility is to delegate the complex task of identity
 * resolution to the AxiomCore. It then attaches the resulting, standardized
 * UniversalIdentity object to the request for downstream use by the router and application logic.
 * It does not contain any business logic itself.
 * 
 * This code is now backed by a powerful and safe binding.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
final class AuthenticateWebApp
{
    /**
     * The orchestrator is injected by Laravel's service container.
     */
    public function __construct(protected AxiomCore $axiom)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next): Response
    {

        // STEP 1: Delegate the entire resolution process to the orchestrator.
        // The orchestrator encapsulates all logic: finding headers, validation,
        // checking sessions, and handling failures gracefully.
        $identity = $this->axiom->inspect($request);

        // STEP 2: Attach the resolved identity (which could be a guest)
        // to the request's attributes. This becomes the single source of truth for identity.
        $request->identityCard($identity); // SetterMode always returns bool to check success

        // STEP 3: Pass the enriched request to the next stage.
        return $next($request);
    }

    /**
     * Generates a standard 403 Forbidden response.
     *
     * @param string $platform The platform that was denied access.
     * @return \Illuminate\Http\JsonResponse
     */
    private function accessDeniedResponse(string $platform): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => "Access denied. This endpoint is not available for the '{$platform}' platform."
        ], Response::HTTP_FORBIDDEN, ['Cache-Control' => 'no-cache, no-store, must-revalidate']);
    }
}
