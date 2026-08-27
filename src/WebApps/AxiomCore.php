<?php

namespace KrubiK\WebApps;
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

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use KrubiK\Enums\Platform;
use KrubiK\WebApps\DTOs\WebAppInitData; /// KrubiK\WebApps\DTVOs\WebAppInitData;
use KrubiK\WebApps\Exceptions\InvalidSignatureException;
use InvalidArgumentException;

/**
 * Service Class :: Identity Orchestrator :: The Weaver of Fates
 * Archetype: The Architect (System Design, Integration)
 *
 * The central nervous system for identity resolution. It scrutinizes any incoming
 * request and answers the fundamental question: "Who are you?".
 *
 * It acts as the gatekeeper, prioritizing cryptographic proofs from external platforms
 * (MiniApps) over internal web sessions, enforcing context isolation.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
final class AxiomCore
{
    /**
     * Inspects the incoming request to resolve the definitive identity.
     *
     * This is the primary entry point for identity resolution. It scrutinizes
     * various identity proofs (like WebApp headers) and internal states (like sessions)
     * based on a strict priority order to return a single, authoritative identity.
     *
     * @param Request $request The incoming HTTP request to inspect.
     * @return UniversalIdentity The resolved identity (Authenticated or Guest).
     */
    public function inspect(Request $request): UniversalIdentity
    {
        // PRIORITY 1: Attempt to resolve identity from WebApp/MiniApp headers.
        if (config('krubot.webapps.enabled', true)) {
            $webAppIdentity = $this->detectRequest($request);

            if ($webAppIdentity !== null) {
                // If a matchin header was found (valid or not), the identity is decided.
                // We DO NOT fall back to the web session to prevent session hijacking.
                return $webAppIdentity;
            }
        }

        // PRIORITY 2: Fallback to standard Laravel web session authentication.
        // This only runs if webapps are disabled or no relevant headers were found.
        if (Auth::guard('web')->check()) {
            /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
            $user = Auth::guard('web')->user();
            return UniversalIdentity::fromWebSession($user);
        }

        // FINAL: If all else fails, the entity is a guest from the web.
        return UniversalIdentity::guest('web');
    }

    /**
     * Scans request headers for valid MiniApp InitData based on krubot.php config.
     *
     * Returns a UniversalIdentity if a platform-specific header is detected,
     * otherwise returns null to allow fallback.
     *
     * @param Request $request
     * @return UniversalIdentity|null
     */
    private function detectRequest(Request $request): ?UniversalIdentity
    {
        $identityHeaders = config('krubot.webapps.identity_headers.platforms', []);
        
        foreach ($identityHeaders as $platform => $headerName) {
            if ($request->hasHeader($headerName)) {
                $initDataString = trim($request->header($headerName));

                // An empty header is treated as an invalid attempt, not an absence of one.
                if (empty($initDataString) || strlen($initDataString) < 50) {
                    // We return a guest for that platform.
                    return UniversalIdentity::guest($platform); // Signal: Invalid attempt from this platform.
                }

                // Certify and get the validated WebAppInitData Value Object.
                $initData = $this->validate($platform, $initDataString);
                
                if ($initData !== null) {
                    // Success: We have a valid, authenticated user from a MiniApp. now Create identity directly from the certified proof object.
                    return UniversalIdentity::fromWebApp($initData);
                }

                // Critical: A header existed but was invalid (bad hash, expired, etc.).
                // Hard-stop by returning a platform-aware guest to block session fallback.
                return UniversalIdentity::guest($platform);
            }
        }
        
        // No relevant WebApp headers were found. Return null to allow fallback to other methods.
        return null;
    }

    /**
     * Certifies the validity of the provided proof data.
     *
     * This method orchestrates the validation by leveraging the WebAppInitData VO.
     *
     * @param string $platform The platform identifier string (e.g., 'telegram', 'bale').
     * @param string $proofData The raw initialization data string.
     * @return WebAppInitData|null The validated Value Object on success, null on failure.
     */
    private function validate(string $platform, string $proofData): ?WebAppInitData
    {
        // This logic can be expanded for other platforms based on our config.
        // For now, it handles Telegram/Bale style validation.
        $token = config("krubot.drivers.{$platform}.token");
        if (!is_string($token) || empty($token) || $token === '_') {
            return null; // Cannot validate without a secret token.
        }

        // Safely convert the platform string from config into a Platform Enum case.
        $platformEnum = Platform::tryFrom($platform);
        if ($platformEnum === null) {
            // Config mismatch error: A platform is defined in identity_headers but not in the Platform Enum.
            // Log::error("Invalid platform '{$platform}' configured in krubot.webapps.");
            return null;
        }

        try {
            // Instantiate the Value Object.
            $initData = WebAppInitData::from($proofData, $platformEnum);
            
            // Perform cryptographic signature verification.
            $initData->validate($token);
            
            // Enforce temporal freshness of the token.
            if (!$this->isFresh($initData)) {
                 return null; // Proof is valid but expired.
            }

            return $initData;

        } catch (InvalidSignatureException | InvalidArgumentException $e) {
            // Catches both invalid hash and malformed initData (e.g., missing 'hash' key).
            // Log::warning("WebApp Identity Certification Failed: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Checks if the auth_date inside the init data is within the configured TTL.
     *
     * @param WebAppInitData $initData
     * @return bool
    */
    private function isFresh(WebAppInitData $initData): bool
    {

        $ttl = (int) config('krubot.webapps.auth_ttl', 3600);

        // We call the new, expressive getter method which returns a Carbon instance.
        // To make this time comparison logic, Extremely Clean and Readable.
        return $initData->getAuthDate()->addSeconds($ttl)->isFuture();
    }
}
