<?php

namespace KrubiK\Routing;
/*
|--------------------------------------------------------------------------
| A Message to the Future Architect of Rebellion... 🚀🌌
|--------------------------------------------------------------------------
|
| Greetings, seeker of knowledge. You have just opened a blueprint
| from the Krubot BotEngine. What you see before you is more
| than just lines of code—it's a pattern for building scalable dreams.
|
| **This is a laboratory of creation.** We are experimenting with the
| very fabric of code here. Use this project as your ultimate training
| ground, a masterclass in *Software Dev Artistry.* It's a powerful template
| for learning, but not yet forged for the final battles of production.
|
| Behold the core principle:
| We Are **Rebuilding The Rebellion** Within S.N.P. *(The Foundation of Pure Power & Revel)*
| This entire library is being reconstructed with intense power,
| on a foundation of pure power **Far Stronger Than Anything That Came Before.**
| Starting with Laravel 12 Capabilities.
|
| What you see here is the **×ReleaseCandiate v0.9×** release. Why release it now?
| Because keeping this evolution a secret any longer would be a
| betrayal to the very community it was born to serve.
| 
| Consider this The Foundational Codex for Engineering a New Reality.
| The knowledge is free under the MIT License. Deconstruct its logic and schematics.
| Learn its secrets. Master its power. Command its potential. You are The Architect Now!
|
| * Go build something revolutionary! * 💜⚡️
|
| Let's Shape the Future. 🛠️⚡️🚀
|
*/

use App\Models\User;
use KrubiK\DTOs\Message;
use KrubiK\WebApps\DTOs\WebRequest; // ⚡ Our Sacred WebRequest HyperDTO
use KrubiK\Render\RenderAura;
use KrubiK\Enums\Platform;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Illuminate\Pipeline\Pipeline;
use KrubiK\Routing\Arcane\StormJusticeTurbine;
use KrubiK\Helpers\AmethystMatrix; // ⚡ Import the Sorceress
use KrubiK\Helpers\JackPoint;      // Import "JackPoint" - The Tactical EventHook System

use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use ReflectionFunction;
use ReflectionNamedType;
use ReflectionUnionType;
use RuntimeException;
use Throwable;

use KrubiK\WebApps\Attributes\{
    WebApp,
    WebPage,
    WebAction
};
use KrubiK\Enums\Signal;
use KrubiK\Attributes\{
    Name,
    Action,
    Middleware,
    Validate,
    RuleSet,
    OnCommand,
    OnText,
    OnRegEx,
    OnInlineQuery,
    Receive,
    When,
    Fallback,
    FallbackOn,
    RestrictTo,
    ForceJoin,
    AdminIds,
    Access,
    Block
};


/**
 * ╔═══════════════════════════════════════════════════════════════════════════════════╗
 * ║                                  ⚡@KRUBOT RC.9⚡                                   ║
 * ║                                   #EPIC_ENGINE                                    ║
 * ║                            THE U.D.X QUANTUM ROUTER                               ║
 * ╚═══════════════════════════════════════════════════════════════════════════════════╝
*
* The routing core where your intention become destiny.
*
* EpicEngine is not merely a collection of routing helpers.
* It is the orchestration chamber that fuses reflection, attributes,
* access control, platform awareness, WebApp routing, middleware,
* validation, conditional guards, force-join directives, identity
* resolution, route enrichment, and runtime execution into one
* coherent Hyper-DX control system.

* ────────────────────────────────────────────────────────────────────────────
* 🌌 THE ATTRIBUTE FUSION CHAMBER
* ────────────────────────────────────────────────────────────────────────────
*
* EpicEngine understands the declarative language of Krubot and translates
* it into executable route state:
*
* ```
   #[OnCommand]
   #[OnText]
   #[OnRegEx]
   #[OnInlineQuery]
   #[Action]
   #[Name]
   #[Middleware]
   #[Access]
   #[Block]
   #[AdminIds]
   #[RestrictTo]
   #[Validate]
   #[RuleSet]
   #[Receive]
   #[When]
   #[ForceJoin]
   #[Fallback]
   #[FallbackOn]
   #[WebApp]
   #[WebPage]
   #[WebAction]
  ```
*
* Multiple declarations are not blindly copied.
* They are interpreted, merged, normalized, de-duplicated, and compiled
* according to the semantic rules of the routing system.
*
* ────────────────────────────────────────────────────────────────────────────
* 🔥 THE ROUTE FORGE
* ────────────────────────────────────────────────────────────────────────────
*
* Class-level declarations establish the field.
* Method-level declarations sharpen the weapon.
* EpicEngine fuses both into the final Route.
*
* ```
   Nexus Rules
        +
   Method Rules
        ↓
   Consolidation
        ↓
   Normalization
        ↓
   Final Route Decree
  ```
*
* Middleware stacks, platform restrictions, access vectors, block vectors,
* validation rules, guards, administrator IDs, force-join channels,
* messages, names, policies, and parameter metadata all converge here.
*
* The Route object is therefore not merely registered.
* It is compiled.
*
* ────────────────────────────────────────────────────────────────────────────
* 🧬 IDENTITY TRANSMUTATION
* ────────────────────────────────────────────────────────────────────────────
*
* EpicEngine can resolve identity across the WebApp and Bot dimensions
* without forcing developer to understand the underlying transport.
*
* A caller may arrive as:
*
* ```
   • a traditional authenticated web user
   • a WebApp / Mini-App identity
   • a Search-Engine Crawler like google-bot
   • a bot sender resolved through platform identity
   • or a genuine Ghost with no authenticated identity
  ```
*
* The engine even can recognize real Eloquent identities, evaluate Spatie roles
* and permissions, honor explicit user-ID grants, and enforce hard Block declarations
* before normal Access grants are evaluated.
*
* ────────────────────────────────────────────────────────────────────────────
* DEVELOPER EXPERIENCE 🧠 UDX
* ────────────────────────────────────────────────────────────────────────────
*
* EpicEngine is deliberately opinionated toward Ultra-DX & Hyper-Speed.
*
* The ideal developer experience EpicEngine grants to you is:
*
* ```
   Declare intent.
   Walk away.
   Let the engine wire the Internet.
  ```
*
* The developer should not have to manually maintain:
*
* ```
   • repetitive route registration
   • reflection plumbing
   • middleware inheritance
   • access propagation
   • block propagation
   • validation aggregation
   • platform normalization
   • parameter enrichment
   • WebApp route discovery
   • action lookup
   • incoming callback query parsing
   • ...
  ```
*
* Those are engine concerns.
*
* The public API remains super narrative and expressive.
* The internal machinery remains super robust & relentless.
* 
* @author DoKtor K.
* @link https://StoryKo.de/Krubot Official website of engine.
* @version Krubot: ×RC.9×
* @license MIT
*/
trait EpicEngine
{
    /**
     * =========================================================================
     * 🛡️ THE QUANTUM GATEKEEPER ENGINE (KRUBOT WIRING CORE)
     * =========================================================================
    */

    use MegaGears;
    use StormJusticeTurbine;

    // =========================================================================
    //  🔮 THE METAPHYSICAL CORE: CENTRALIZED REFLECTION & AUTO-WIRING (v16.0 Ultimate)
    //  Google/Microsoft Architect Level. Zero DRY Violations. Every component uses this core engine.
    // =========================================================================

    /**
     * @var array<string, Route> O(1) Map for Handler lookup [Class::method => Route]
    */
    protected array $handlerToRouteMap = [];

    /**
     * @var array<string, Route> O(1) Map for WebPath lookup [web.path.name => Route]
    */
    protected array $webPathToRouteMap = [];

    /**
     * @var array<string, Route> O(1) Map for Command lookup [command_name => Route]
    */
    protected array $commandToRouteMap = [];

    /**
     * Static Memory Cache for Reflection Data.
     * In RoadRunner/Swoole/Octane, this persists across requests for TRUE O(1) speed!
     * @var array<string, array<string, ReflectionMethod>> 
    */
    protected static array $actionMethodCache = [];
    
    /**
     * Stack to hold attributes of nested groups.
    */
    protected array $groupAttributesStack = [];
    
    /**
     * Temporary storage to track routes added within a group closure.
     * Used to return a RouteBox object.
    */
    protected ?array $currentGroupRoutes = null;    

    /**
     * 🛡️ Resolve the user across Web and Bot dimensions perfectly utilizing AxiomCore.
     *
     * Worlds we unify:
     *   - 🌐 WEB/MINI-APP DIMENSION : HTTP Request carrying an AxiomCore IdentityCard.
     *   - 🤖 BOT DIMENSION          : Telegram/Bale/Rubika — Webhook or LongPolling.
     *
     * Return semantics:
     *   - `User`  → identity successfully resolved (real Eloquent model so Spatie reads roles/permissions).
     *   - `null`  → Ghost / Unauthenticated caller.
     *
     * @return User|null
    */
    protected function resolveQuantumUser(): ?User
    {
        // ─────────────────────────────────────────────────────────────
        // 1. WEB / MINI-APP DIMENSION (دنیای وب / WebApp):
        //    آیا در بستر HTTP Request هستیم؟
        // ─────────────────────────────────────────────────────────────
        if (function_exists('request') && request() instanceof Request) {
            $request = request();

            // ✨ AxiomCore Integration: Get the UniversalIdentity
            // We support BOTH discovery paths:
            //   (a) A Laravel Request Macro `identityCard()`
            //   (b) A Middleware-injected attribute on the Request bag
            // This dual support keeps us compatible with AuthenticateWebApp middleware.
            // Check if AuthenticateWebApp middleware injected the IdentityCard (Macro or Attribute)
            $identity = null;
            if (method_exists($request, 'identityCard')) {
                $identity = $request->identityCard(); // Via Laravel Request Macro defined in KrubotServiceProvider@boot()
            } elseif ($request->attributes->has('identityCard')) {
                $identity = $request->attributes->get('identityCard'); // Via Middleware Attribute
            }

            // Check if identity exists and is authenticated based on UniversalIdentity structure
            // NOTE: We test `property_exists` first so we don't explode on legacy identity objects
            // that still expose `isAuthenticated()` as a METHOD (see EpicRouter - Copy variants).
            if ($identity && property_exists($identity, 'isAuthenticated') && $identity->isAuthenticated) {
                
                // ── PATH (A): It's a web session — `$identity->user` is ALREADY an Eloquent model.
                if ($identity->user instanceof User) {

                    // Return the resolved Laravel User model from UniversalIdentity
                    return $identity->user;
                }

                // ── PATH (B): It's from WebAppInitData — `$identity->user` is a raw ARRAY.
                //    We MUST fetch the real Eloquent Model so Spatie can read its roles/permissions!
                //    (Otherwise `hasAnyRole()` would simply not exist on the array.)
                if (method_exists(User::class, 'findByQuantumId')) {

                    // Resolve platform strictly via Platform Enum (SSoT)
                    $platformValue = $identity->platform ?? Platform::def();

                    // Utilize the InteractsWithMultiverse trait on the User model
                    return clone User::findByQuantumId($platformValue, $identity->id());
                }
            }

            // Fallback برای وبِ سنتی (وب‌سایت)
            // Fallback: Traditional Web Authentication (Session/Sanctum)
            // Kept as a safety net for legacy web routes that predate AxiomCore.
            if (function_exists('auth') && auth()->check()) {
                return auth()->user();
            }
        }

        // ─────────────────────────────────────────────────────────────
        // 2. BOT DIMENSION: Direct processing (Telegram/Bale/Rubika — Webhook or LongPolling)
        //    Here there is no HTTP Request context inside the trait,
        //    so we resolve the senderId straight from the bot payload.
        // ─────────────────────────────────────────────────────────────
        $senderId = $this->senderId(); 
        
        if ($senderId && class_exists(User::class)) {

            // Resolve platform strictly via Platform Enum (SSoT — Single Source of Truth).
            // Supports BOTH resolution styles seen across the codebase:
            //   (a) instance trait method `$this->nemesis()`
            //   (b) global container helper `app('nemesis')`
            /*
            $platform = method_exists($this, 'nemesis')
                ? $this->nemesis()->platform()
                : app('nemesis')->platform();
            */

            $platform = $this->resolveCurrentPlatform();

            // Utilize the InteractsWithMultiverse trait on the User model.
            // We `clone` so callers can't accidentally mutate a shared/cached model
            // that may be reused across the pipeline (webhook retries, parallel jobs, ...).
            if (method_exists(User::class, 'findByQuantumId')) {
                return clone User::findByQuantumId($platform, $senderId);
            }
        }

        return null; // Ghost / Guest / Unauthenticated
    }

    /**
     * Define a command group.
     * Returns a RouteBox object to allow chaining ->middleware() on the whole group.
    */
    public function group(array|callable $attributesOrCallback, ?callable $callback = null): RouteBox
    {
        // Normalize arguments: allow group(fn) or group([], fn)
        if (is_callable($attributesOrCallback) && is_null($callback)) {
            $callback = $attributesOrCallback;
            $attributes = [];
        } else {
            $attributes = $attributesOrCallback;
        }

        // 1. Push attributes to stack
        $this->groupAttributesStack[] = $attributes;
        
        // 2. Capture routes added in this scope
        $previousGroupRoutes = $this->currentGroupRoutes; // Handle nesting
        $this->currentGroupRoutes = [];

        // 3. Execute Closure
        $callback($this);

        // 4. Create Group Object with captured routes
        $group = new RouteBox($this->currentGroupRoutes);

        // 5. Restore Previous State (Nesting support)
        // If we are inside another group, add these routes to the parent too
        if ($previousGroupRoutes !== null) {
            $previousGroupRoutes = array_merge($previousGroupRoutes, $this->currentGroupRoutes);
            $this->currentGroupRoutes = $previousGroupRoutes;
        } else {
            $this->currentGroupRoutes = null;
        }

        // 6. Pop attributes
        array_pop($this->groupAttributesStack);

        return $group;
    }

    /**
     * Get merged attributes.
    */
    protected function getGroupAttributes(): array
    {
        $final = [];
        foreach ($this->groupAttributesStack as $group) {
            // Merge logic (Middleware array merge, Prefix concat, etc.)
             foreach ($group as $key => $value) {
                if ($key === 'middleware') {
                    $current = $final[$key] ?? [];
                    $value = is_array($value) ? $value : [$value];
                    $final[$key] = array_merge($current, $value);
                } elseif ($key === 'prefix') {
                    $final[$key] = isset($final[$key]) ? trim($final[$key] . '/' . trim($value, '/'), '/') : trim($value, '/');
                } else {
                    $final[$key] = $value;
                }
            }
        }
        return $final;
    }
    
    /**
     * Internal: Called by onCommand/onText to register the route object to the current group tracker.
    */
    protected function registerRouteToGroup($routeObject): void
    {
        if ($this->currentGroupRoutes !== null) {
            $this->currentGroupRoutes[] = $routeObject;
        }
    }

    /**
     * ⚡ Universal Action Discoverer (O(1) after first scan).
     * Scans any class ONCE to find the method matching an #[Action] or naming convention.
    */
    public function discoverActionMethod(object $targetInstance, string $actionName): ?ReflectionMethod
    {
        $className = $targetInstance::class;
        $cacheKey = $className . '::' . $actionName;

        // O(1) Memory Cache Return
        if (isset(self::$actionMethodCache[$cacheKey])) {
            return self::$actionMethodCache[$cacheKey];
        }

        $reflection = new ReflectionClass($className);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // 1. Primary Strategy: PHP 8 Attributes
            $attributes = $method->getAttributes(Action::class);
            if (!empty($attributes)) {
                if ($attributes[0]->newInstance()->name === $actionName) {
                    self::$actionMethodCache[$cacheKey] = $method;
                    return $method;
                }
            }

            // 2. Legacy / Fallback Strategy
            $expectedName = 'on' . str_replace('_', '', ucwords($actionName, '_'));
            if ($method->getName() === $expectedName || $method->getName() === $actionName) {
                self::$actionMethodCache[$cacheKey] = $method;
                return $method;
            }
        }

        return null;
    }

    /**
     * 🛡️ The Spatie Engine Gatekeeper
     *
     * Access flow (fail-fast, O(1) short-circuit at step 1):
     *   1. O(1) Quick Pass — if the route declares no roles → public.
     *   2. Resolve the Quantum User across dimensions (Web ⇄ Bot).
     *   3. Validate that the User model exposes Spatie's API.
     *   4. Super-Admin ("Quantum Architect") override — always passes.
     *   5. Union check: `hasAnyRole(...)` OR `hasAnyPermission(...)`.
     *   6. Denied → abort(401/403 for web) OR `$this->reply(...)` for bot.
     *
     * @param object  $route   The resolved route/action object (carries ->accessRoles)
     * @param Message $message the processing message
     * @return bool
     * @throws RuntimeException
    */
    protected function authorizeAccessRequest(object $route, Message $message): bool
    {
        // O(1) Quick Pass: Does this route even demand access control?
        $hasAccess = !empty($route->accessRoles)   || !empty($route->accessUserIds);
        $hasBlock  = !empty($route->blockRoles)    || !empty($route->blockUserIds);

        // ═══════════════════════════════════════════════════════════════
        // 1. O(1) Quick Pass: neither Access nor Block declared → public.
        //    (چون روتر قبلاً بلوکها را merge کرده، این شرط کافیه.)
        // ═══════════════════════════════════════════════════════════════
        if (!$hasAccess && !$hasBlock) {
            return true; // Public access granted — no roles declared on the #[Access] or #[Block] attribute.
        }

        // ═══════════════════════════════════════════════════════════════
        // 2. Resolve the Quantum User across dimensions (Web ⇄ Bot).
        //    Block needs a user to check against, so we resolve before it.
        // ═══════════════════════════════════════════════════════════════
        $user = $this->resolveQuantumUser();

        if($user) {

            // 4. The Architect Override (Super Admin) — always passes.
            //    سوپر ادمین همیشه دسترسی دارد.
            if (method_exists($user, 'isQuantumArchitect') && $user->isQuantumArchitect()) {
                return true;
            }

        }

        // ═══════════════════════════════════════════════════════════════
        // 3. 🚫 BLOCK CHECK — THE ABSOLUTE VETO (runs BEFORE Access)
        //    زور Block به زور Allow میچربه.
        //
        //    Design choice: Block is a HARD-VETO that even the Quantum Architect
        //    (Super Admin) cannot bypass. A veto with exceptions is not a veto.
        //
        //    Guests: if there is no user, they cannot carry a blocked role,
        //    so they sail past this check and fall through to the Access logic.
        // ═══════════════════════════════════════════════════════════════
        if ($hasBlock && $user) {
            // Sanity: Block parsing needs Spatie's API just like Access does.
            if (!method_exists($user, 'hasAnyRole')) {
                throw new RuntimeException(
                    "Krubot Architect Error: The User model MUST use \\Spatie\\Permission\\Traits\\HasRoles to parse #[Block] attributes."
                );
            }

            $userKey = $user->quantumIdentityKey();   // uniform string comparison

            // Check both roles AND permissions — a "banned" could live in either store.
            $blockedByRole = !empty($route->blockRoles)
                && ($user->hasAnyRole((array) $route->blockRoles)
                    || $user->hasAnyPermission((array) $route->blockRoles));

            // Check Blocked By User_ID
            $blockedById = !empty($route->blockUserIds)
                && in_array($userKey, (array) $route->blockUserIds, true); // fast-strict!

            if ($blockedByRole || $blockedById) {
                $this->abortAccess('blocked', $message);
                return false; // IDE hint; abortAccess already short-circuits upstream.
            }
        }

        // ═══════════════════════════════════════════════════════════════
        // 4. If only Block was declared (and user passed it) → public.
        //    e.g. a route with #[Block('suspended')] but no #[Access(...)]
        //    means: "everybody welcome, except suspended users".
        // ═══════════════════════════════════════════════════════════════
        if (!$hasAccess) {
            return true;
        }

        if (!$user) {
            $this->abortAccess('unauthenticated', $message);
            // NOTE: kept for IDE static analysis; the abort above already interrupted flow
            // (HTTP abort() throws, and bot reply() is expected to short-circuit upstream).
            return false;
        }

        // Spatie Core Validation — the User model MUST expose HasRoles.
        if (!method_exists($user, 'hasAnyRole')) {
            throw new RuntimeException(
                "Krubot Architect Error: The User model MUST use \Spatie\Permission\Traits\HasRoles to parse #[Access] attributes."
            );
        }

        $userKey = $user->quantumIdentityKey();

        // The Union Check: Verify Roles OR Permissions against the route requirements.
        //   We cast Roles to array (in $route->accessRoles) cause Spatie accepts either a single string or an array input.
        $grantedByRole = !empty($route->accessRoles)
            && ($user->hasAnyRole((array) $route->accessRoles)
                || $user->hasAnyPermission((array) $route->accessRoles));

        // Check Accessed By User_ID
        $grantedById = !empty($route->accessUserIds)
            && in_array($userKey, (array) $route->accessUserIds, true); // fast-strict!

        if ($grantedByRole || $grantedById) {
            return true;
        }

        // Access Denied
        $this->abortAccess('forbidden', $message);
        return false; // Redundant due to abort/die, but keeps IDEs happy
    }

    /**
     * Smart Rejection 🛡️ based on the current dimension (HTTP WebApp vs Bot Webhook).
     *
     * ⚠️ CRITICAL: Webhooks ARE HTTP requests too — we must NOT return HTTP 401/403 on them,
     * otherwise the bot platform (Telegram/Bale/Rubika) will retry the update endlessly.
     *
     * The safest discriminator is therefore NOT "is there a Request?" but
     * "does the incoming Message carry our Sacred WebRequest HyperDTO?".
     *   - If YES → we're answering a WebApp / API call → real HTTP status codes.
     *   - If NO  → we're answering a Bot webhook         → chat reply, silent from HTTP's PoV.
     * 
     * FIX: Web access denials go through setResponse() (from HasWebInterface trait),
     * which stores the error response on $this->finalResponse.
     * QuantumGatewayController then picks it up cleanly via hasResponse() / response(),
     * returning the correct 401 or 403 — no exception thrown, no 500.
     *
     * Bot denials are unchanged — they reply inline and short-circuit upstream as before.
     *
     * @uses HasWebInterface::setResponse()  — normalizes any payload into a SymfonyResponse
     * @uses \Illuminate\Http\JsonResponse   — structured, client-parseable error body
     *
     * @param string  $type    'unauthenticated' | 'forbidden'
     * @param Message $message the processing message
     * @return void
    */
    protected function abortAccess(string $type, Message $message): void
    {
        // ─────────────────────────────────────────────────────────────
        // Distinguish between WebApp API calls ⚠️ and Bot Webhooks
        // to prevent Webhook endless retries.
        //
        // (Legacy heuristic, kept for reference — replaced by the HyperDTO check below)
        // $isWebRequest = function_exists('request') && request() instanceof Request
        //                 && (request()->expectsJson() || request()->is('api/*'));
        //
        // (Another legacy heuristic — from EpicRouter)
        // $isWebRequest = function_exists('request')
        //                 && (request()->isMethodSafe() || request()->ajax() || request()->route());
        // ─────────────────────────────────────────────────────────────

        // ✅ Authoritative check: rely on our HyperDTO, not on the raw Request.
        $isWebRequest = $message
            && property_exists($message, 'web_request')
            && $message->web_request instanceof WebRequest;

        $webRequest = $isWebRequest ? $message->web_request : null;

        // 🌐 Fetch precise language context from RenderAura
        //    eg: "en" or "fa" — so our error message speaks the user's language.
        $lang = app(RenderAura::class)->lang;

        // HTTP status codes map: 'unauthenticated' → 401, 'forbidden'/'blocked' → 403
        $httpMap = [
            'unauthenticated' => 401,
            'forbidden'       => 403,
            'blocked'         => 403,
        ];

        // i18n key map: resolves to the correct lang string per type
        $webLangKey = [
            'unauthenticated' => 'krubot.auth.unauthenticated_web',
            'forbidden'       => 'krubot.auth.forbidden_web',
            'blocked'         => 'krubot.auth.blocked_web',
        ];

        $botLangKey = [
            'unauthenticated' => 'krubot.auth.unauthenticated_bot',
            'forbidden'       => 'krubot.auth.forbidden_bot',
            'blocked'         => 'krubot.auth.blocked_bot',
        ];

        if ($isWebRequest) {
            // CRITICAL PATH: Do NOT use abort() here — it throws HttpException and escapes the engine.
            //
            // setResponse() (HasWebInterface) stores the response on $this->finalResponse.
            // QuantumGatewayController::handleWebApp() checks hasResponse() after processUpdate()
            // and returns it directly — clean 401/403 delivered, no exception, no 500.
            $statusCode   = $httpMap[$type]   ?? 403;
            $errorMessage = __(
                $webLangKey[$type] ?? 'krubot.auth.forbidden_web',
                [],
                $lang
            );

            // ╔═══════════════════════════════════════════════════════════╗
            // ║ 🌐 GET → BEAUTIFUL UTF-8 HTML                            ║
            // ║                                                            ║
            // ║ Browser navigation should receive a human-readable page,  ║
            // ║ while AJAX/API/WebApp mutations keep the existing JSON     ║
            // ║ contract below.                                           ║
            // ╚═══════════════════════════════════════════════════════════╝
            if ($webRequest->isGet()) {
                $this->renderHtmlErrorResponse(
                    $errorMessage,
                    $type,
                    $lang,
                    $statusCode
                );
                return;
            }

            // Non-GET WebApp/API requests keep the existing structured JSON contract.
            // This avoids breaking JavaScript clients that expect parseable error data.
            $this->response(
                new JsonResponse([
                    // Structured error body — parseable by the WebApp JS client
                    'status'  => 'error',
                    'code'    => $type,                     // 'unauthenticated' | 'forbidden' | 'blocked'
                    'message' => $errorMessage,
                ], $statusCode)
            );

            return; // Engine pipeline continues but destination already has its null-return guard
        }

        // BOT PATH: Inline translated reply short-circuits upstream as designed ; never emit HTTP 401/403 for webhooks.
        $this->reply(__($botLangKey[$type] ?? 'krubot.auth.forbidden_bot', [], $lang))->send();
    }

    /**
     * Render a self-contained, high-performance HTML error gate for direct browser navigation.
     *
     * Provides a resilient, zero-dependency visual shell during critical failures,
     * complete with dark-mode styling, responsive clamp typography, and bidirectional (RTL) support.
     *
     * @param  string       $errorMessage  Human-readable diagnostic or localized message
     * @param  string       $type          Machine-readable error signature / category code
     * @param  string|null  $lang          Target locale identifier (e.g., 'en', 'fa-IR', 'ar')
     * @param  int          $statusCode    Standard HTTP transport status code
     * @return void
    */
    protected function renderHtmlErrorResponse(
        string $errorMessage,
        string $type,
        ?string $lang,
        int $statusCode
    ): void {
        // ╔═══════════════════════════════════════════════════════════╗
        // ║ 🌐 GET → BEAUTIFUL UTF-8 HTML                            ║
        // ║                                                            ║
        // ║ Browser navigation should receive a human-readable page,  ║
        // ║ while AJAX/API/WebApp mutations keep the existing JSON     ║
        // ║ contract below.                                           ║
        // ╚═══════════════════════════════════════════════════════════╝
        
        // Resolve effective locale:
        // explicit input → application locale → safe fallback.
        $language = (string) ($lang ?: app()->getLocale() ?: 'en');

        // Bi-directional text auto-negotiation (Arabic/Persian/Hebrew/Urdu)
        // Resolves layout orientation gracefully without loading heavier i18n drivers
        $languageRoot = strtolower(strtok($language, '-_') ?: '');
        $direction = in_array($languageRoot, ['fa', 'ar', 'ur'], true)
            ? 'rtl'
            : 'ltr';
            
        /*
            * Blade is responsible for output escaping.
            *
            * Keep the payload raw here so values are not double-encoded.
            *
            * View resolution priority:
            *   1. resources/views/krubot/errors/access.blade.php
            *      (published/user-land override)
            *   2. package's registered krubot::errors.access view
        */
        // Compile standalone, zero-dependency visual fallback markup
        $response = response()
            ->view(
                'krubot::errors.access',
                [
                    'language'   => $language,
                    'direction'  => $direction,
                    'statusCode' => $statusCode,
                    'message'    => $errorMessage,
                    'code'       => $type,
                ],
                $statusCode
            )
            ->header('Content-Type', 'text/html; charset=UTF-8');
    
        // Dispatch the fully-rendered HTML response into the active response pipeline. (QuantumGatewayController@handleWebApp catches it and render it to the client)
        $this->response($response);

        /*
        * Optional first-party error pages — intentionally disabled.
        *
        * Wire these to the project's canonical 403/404 Blade pages
        * when you want the engine to render them instead of this inline
        * diagnostic shell, while carrying the translated message with it:
        *
        * $errorView = match ($statusCode) {
        *     403 => 'errors.403',
        *     404 => 'errors.404',
        *     default => null,
        * };
        * if ($errorView && view()->exists($errorView)) {
        *     $this->response(
        *         response()->view($errorView, [
        *             'message' => $errorMessage,
        *             'code'    => $type,
        *             'status'  => $statusCode,
        *         ], $statusCode)
        *         ->header('Content-Type', 'text/html; charset=UTF-8')
        *     );
        *     return;
        * }
        */

    }

    // #####################


    

    /**
     * Scans a "Nexus" (Controller/Logic Class) using the O(1) Manifest Engine and integrates it.
     * Rewritten for PHP 8.2.30 with Extreme DX & Zero Redundant Reflection.
     * This is the master reflection engine that automatically discovers and registers Routes,
     * injects Middlewares, and prepares handlers for execution.
     *
     * 💎 FUSED POWERS (v7.0 + v8.0 + v9.0 Ultimate + O(1) Manifest):
     * 1.  **Class-Level Middlewares:** Processes `#[Middleware(...)]` on the Nexus class itself.
     * 2.  **Method-Level Middlewares:** Processes `#[Middleware(...)]` on individual action methods.
     * 3.  **Smart Stack Assembly:** Merges middlewares with Nexus-level running BEFORE method-level.
     * 4.  **Fluent Route Configuration:** Leverages the full power of the Route object for chaining.
     * 5.  **Named Route Recognition:** Automatically detects `#[Name('...')]` for use with the `go()` method.
     * 6.  **Full DI Compatibility:** Prepares handlers for seamless execution via Laravel's Service Container.
     * 7.  **Robust Error Handling:** Provides precise, context-aware error logging on reflection failure.
     *
     * 📜 ویژگی‌های سینگولاریتی (v9.0 Ultimate):
     * - **پشتیبانی کامل از Middleware در دو سطح:** ابتدا میدل‌ورهای تعریف شده روی خودِ کلاس (Nexus) را استخراج می‌کند و سپس میدل‌ورهای روی متد را به آن اضافه می‌کند.
     * - **ادغام هوشمند (Smart Merging):** این دو آرایه را با هم ترکیب می‌کند (اول کلاس، بعد متد) تا ترتیب اجرا دقیقاً همانطور که انتظار می‌رود باشد.
     * - **یکپارچه‌سازی روان (Fluent Integration):** از خروجی متدهای onCommand و onText (که آبجکت Route هستند) استفاده کرده و میدل‌ورها و نام‌ها را مستقیماً با متدهای `->middleware()` و `->name()` به آن‌ها تزریق می‌کند.
     * - **مدیریت خطای مستحکم:** مدیریت خطای دقیق در صورت وجود نداشتن کلاس یا بروز مشکلات در حین Reflection.
     *
     * 🔮 Supported Attributes:
     * - #[OnCommand('/cmd')]
     * - #[OnText('Exact Text')]
     * - #[OnText('/Exact Text/i')]
     * - #[OnRegEx('/pattern/i')]
     * - #[OnRegEx('pattern')] // Auto-wraps to: '/pattern/'
     * - #[Middleware(['auth', 'log', Admin::class])]
     * - #[Name('my.route.name')]
     * - #[Action('button_payload')]
     * - #[Receive(Signal::Checkout)]
     * - #[Receive([Signal::Sticker, 'animation'])]
     * - #[OnInlineQuery]
     * - #[OnInlineQuery('/item\s+(.+)/')]
     * - #[OnInlineQuery('article:')]
     * + Many More Attributes... ✨️
     *
     * @param object|string $nexus The Nexus instance or its fully qualified class name to scan.
     * @return void
     */
    public function integrateNexus(object|string $nexus, bool $isSingleNexus = true): void
    {
        // 1. Resolve the Nexus Class Name efficiently
        $className = is_string($nexus) ? $nexus : get_class($nexus);

        /*
         * [LEGACY LOGIC REMOVED FOR PERFORMANCE - O(n) Array Scan]
         * if (in_array($className, $this->integratedNexuses, true)) { return; }
        */

        // [VIPER'S GIFT] O(1) performance for duplicate checks. Vastly superior to O(n) in_array.
        if (isset($this->integratedNexuses[$className])) {
            return;
        }

        // =========================================================================
        // ⚡️ [THE ARCHITECT'S TOUCH]: INJECT SYNAPSES RIGHT HERE! ⚡️
        // =========================================================================
        $this->injectSynapses($nexus);
        JackPoint::fire('nexus.synapses.injected', $nexus, $this);

        //  Commander K. Order: Cache the default web access policy once to avoid
        //  repeated 'config()' calls inside the loops.
        if (WebApp::$systemDefaultAccessPolicy === null) {
            WebApp::$systemDefaultAccessPolicy = config('krubot.webapps.access_policy', 'strict');
        }

        // اجازه دادن به listenerها که اطلاعات nexus رو پیش از integration تغییر بدن
        $className = JackPoint::transform('nexus.class.resolve', $className, $this);

        // 🔥 EVENT: nexus.integrating — veto or mutate before scanning.
        $veto = JackPoint::fire('nexus.integrating', $className, $this);
        if ($veto === false) return;

        try {
            // 🧠 The Magic: Get everything instantly! Build the Manifest ONCE per worker lifecycle.
            if (!isset(self::$nexusManifestCache[$className])) {
                $reflection = new ReflectionClass($className);
                if (!$reflection->isInstantiable()) return;

                $manifest = ['class_attributes' => [], 'methods' => []];

                // Cache Class-Level Attributes
                $manifest['class_attributes'] = [
                    Name::class       => $reflection->getAttributes(Name::class),
                    Middleware::class => $reflection->getAttributes(Middleware::class),
                    WebApp::class     => $reflection->getAttributes(WebApp::class),         // ✨ NEW
                    RestrictTo::class => $reflection->getAttributes(RestrictTo::class),     // ✨ NEW
                    ForceJoin::class  => $reflection->getAttributes(ForceJoin::class),
                    Validate::class   => $reflection->getAttributes(Validate::class),
                    RuleSet::class    => $reflection->getAttributes(RuleSet::class),
                    Access::class     => $reflection->getAttributes(Access::class),
                    Block::class      => $reflection->getAttributes(Block::class),
                    AdminIds::class   => $reflection->getAttributes(AdminIds::class),
                ];

                // Cache Method-Level Attributes
                foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                    $manifest['methods'][$method->getName()] = [
                        Middleware::class => $method->getAttributes(Middleware::class),
                        Name::class       => $method->getAttributes(Name::class),
                        OnCommand::class  => $method->getAttributes(OnCommand::class),
                        OnText::class     => $method->getAttributes(OnText::class),
                        OnRegEx::class    => $method->getAttributes(OnRegEx::class),
                        Receive::class    => $method->getAttributes(Receive::class),
                        When::class       => $method->getAttributes(When::class), // , \Attribute::IS_REPEATABLE
                        ForceJoin::class  => $method->getAttributes(ForceJoin::class),
                        Fallback::class   => $method->getAttributes(Fallback::class),      // ✨ NEW: Scan for the global fallback
                        FallbackOn::class => $method->getAttributes(FallbackOn::class),    // ✨ NEW: Scan for type-specific fallbacks
                        Validate::class   => $method->getAttributes(Validate::class),
                        RuleSet::class    => $method->getAttributes(RuleSet::class),
                        Action::class     => $method->getAttributes(Action::class),
                        Access::class     => $method->getAttributes(Access::class),
                        Block::class      => $method->getAttributes(Block::class),
                        AdminIds::class   => $method->getAttributes(AdminIds::class),
                        WebApp::class     => $method->getAttributes(WebApp::class),      // ✨ NEW
                        WebPage::class    => $method->getAttributes(WebPage::class),     // ✨ NEW
                        WebAction::class  => $method->getAttributes(WebAction::class),   // ✨ NEW
                        RestrictTo::class => $method->getAttributes(RestrictTo::class),  // ✨ NEW,
                        OnInlineQuery::class => $method->getAttributes(OnInlineQuery::class)
                    ];
                }
                self::$nexusManifestCache[$className] = $manifest;
            }

            $manifest = self::$nexusManifestCache[$className];

            // -----------------------------------------------------------------
            // PHASE A: Extract Nexus-Level Middlewares (Global for this Nexus)
            // -----------------------------------------------------------------
            $nexusMiddlewares = [];
            foreach ($manifest['class_attributes'][Middleware::class] ?? [] as $cmWAttr) {
                // Merge supports multiple attributes: #[Middleware('A')] #[Middleware('B')]
                $nexusMiddlewares = array_merge($nexusMiddlewares, $cmWAttr->newInstance()->middlewares);
            }

            // ================================================================
            // 📚 NEXUS-LEVEL RULE SETS
            // RuleSets are declarations only; they never attach directly to a Route.
            // ================================================================
            foreach ($manifest['class_attributes'][RuleSet::class] ?? [] as $ruleSetAttr) {
                /** @var RuleSet $instance */
                $instance = $ruleSetAttr->newInstance();

                $this->ruleSets[$instance->name] = array_merge(
                    $this->ruleSets[$instance->name] ?? [],
                    $instance->rules
                );
            }

            // ✨ NEW: Extract WebApp base name
            $webAppPrefix = isset($manifest['class_attributes'][WebApp::class][0]) ?
                $manifest['class_attributes'][WebApp::class][0]->newInstance()->name
            :
                null;

            // ✨ NEW LOGIC: Extract the class-level Name prefix for relative route naming.
            $nexusNamePrefix = isset($manifest['class_attributes'][Name::class][0]) ?
                $manifest['class_attributes'][Name::class][0]->newInstance()->name
            :
                null;

            // ✨ THE AMBASSADOR'S REPORT (CLASS-LEVEL) ✨
            // The Architect reads the class-level decrees from the RestrictTo ambassador.
            $nexusPlatformRestrictions = [];
            if (isset($manifest['class_attributes'][RestrictTo::class][0])) {
                /** @var \KrubiK\Attributes\RestrictTo $instance */
                $instance = $manifest['class_attributes'][RestrictTo::class][0]->newInstance();
                // The attribute itself resolves aliases and legions from the config file.
                $nexusPlatformRestrictions = array_merge($nexusPlatformRestrictions, $instance->getResolvedPlatforms());
            }

            // Ensure the final merged list from all class-level attributes is unique.
            // array_values is used to reset array keys for clean, predictable results.
            if (!empty($nexusPlatformRestrictions)) {
                $nexusPlatformRestrictions = array_values(array_unique($nexusPlatformRestrictions));
            }

            $nexusValidationRules = [];
            foreach ($manifest['class_attributes'][Validate::class] ?? [] as $attr) {
                /** @var Validate $instance */
                $instance = $attr->newInstance();

                foreach ($instance->toArray() as $parameter => $rules) {
                    $nexusValidationRules[$parameter] = array_merge(
                        $nexusValidationRules[$parameter] ?? [],
                        $rules
                    );
                }
            }

            // ✨ THE GATEKEEPER'S REPORT (CLASS-LEVEL) ✨
            // The Architect reads the class-level decrees from the Access gatekeeper.
            $nexusAccessRoles = [];
            $nexusAccessUserIds = [];
            foreach ($manifest['class_attributes'][Access::class] ?? [] as $accessAttr) {
                /** @var \KrubiK\Attributes\Access $instance */
                $instance = $accessAttr->newInstance();
                // We merge all roles into a flat array
                $nexusAccessRoles = array_merge($nexusAccessRoles, $instance->roles);
                $nexusAccessUserIds = array_merge($nexusAccessUserIds, $instance->userIds);
            }
            if (!empty($nexusAccessRoles)) {
                $nexusAccessRoles = array_values(array_unique($nexusAccessRoles));
            }
            if (!empty($nexusAccessUserIds)) {
                $nexusAccessUserIds = array_values(array_unique($nexusAccessUserIds));
            }

            // 🚫 THE BLOCK GATEKEEPER'S REPORT (CLASS-LEVEL) 🚫
            // The Architect reads the class-level vetoes from the Block gatekeeper.
            $nexusBlockRoles = [];
            foreach ($manifest['class_attributes'][Block::class] ?? [] as $blockAttr) {
                /** @var \KrubiK\Attributes\Block $instance */
                $instance = $blockAttr->newInstance();
                // We merge all forbidden roles into a flat array
                $nexusBlockRoles = array_merge($nexusBlockRoles, $instance->roles);
                $nexusBlockUserIds = array_merge($nexusBlockUserIds, $instance->userIds);
            }
            if (!empty($nexusBlockRoles)) {
                $nexusBlockRoles = array_values(array_unique($nexusBlockRoles));
            }
            if (!empty($nexusBlockUserIds)) {
                $nexusBlockUserIds = array_values(array_unique($nexusBlockUserIds));
            }

            // ✨ THE CONDUIT'S CALLING (CLASS-LEVEL) ✨
            // The scanner now listens for the unifying call of ForceJoin at the Nexus level.
            $nexusForceJoinChannels = [];
            $nexusForceJoinFailMessage = null; // <- Variable to hold CLASS-level message
            foreach (($manifest['class_attributes'][ForceJoin::class] ?? []) as $forceJoinAttr) {

                // ForceJoin attribute is designed to be IS_REPEATABLE. We merge channels from all instances.
                // The attribute's constructor has already unified and cleaned its own data.
                $nexusForceJoinChannels = array_merge(
                    $nexusForceJoinChannels,
                    $forceJoinAttr->newInstance()->channels
                );

                // If a fail message is set, it overrides any previous one found at the CLASS level.
                if ($instance->failMessage !== null) {
                    $nexusForceJoinFailMessage = $instance->failMessage;
                }

            }
            // Final purification at the class level to handle overlaps between multiple attributes.
            if (!empty($nexusForceJoinChannels)) {
                $nexusForceJoinChannels = array_values(array_unique($nexusForceJoinChannels));
            }

            // ─── AdminIds (Class Level) ───────────────────────────────────────
            $nexusAdminIds = [];
            foreach ($manifest['class_attributes'][AdminIds::class] ?? [] as $adminAttr) {
                $nexusAdminIds = array_merge($nexusAdminIds, $adminAttr->newInstance()->ids);
            }
            if (!empty($nexusAdminIds)) {
                $nexusAdminIds = array_values(array_unique($nexusAdminIds));
            }

            if ($webAppPrefix) {
                $webAppHandler = null;

                // Look for method names index() || handle() in a WebApp()Nexus

                if (isset($manifest['methods']['index'])) {
                    $webAppHandler = [$className, 'index'];
                } elseif (isset($manifest['methods']['handle'])) {
                    $webAppHandler = [$className, 'handle'];
                }

                if ($webAppHandler) {
                    $webAppAttrInstance = $manifest['class_attributes'][WebApp::class][0]->newInstance();
                    $finalPath = $this->_resolveRelativePathName($webAppAttrInstance->path ?? $webAppAttrInstance->name, null); // WebApp is top-level
                    
                    $route = $this->onWebApp(
                        $finalPath, 
                        $webAppHandler, 
                        $webAppAttrInstance->methods
                    );

                    // Fetch Method-level Access for index() / handle()
                    $webAppHandlerMethodName = $webAppHandler[1];
                    $methodAttrs = $manifest['methods'][$webAppHandlerMethodName] ?? [];
                    
                    // =============================================================
                    // 🛡️ ACCESS CONTROL DECREES (WebApp)
                    // =============================================================

                    // Inherit Nexus-level access roles
                    $webAppAccessRoles = $nexusAccessRoles ?? []; 
                    $webAppAccessUserIds = $nexusAccessUserIds ?? [];

                    // Vector I
                    foreach ($methodAttrs[Access::class] ?? [] as $accessAttr) {
                        $instance = $accessAttr->newInstance();
            
                        $webAppAccessRoles   = array_merge($webAppAccessRoles,   $instance->roles);
                        $webAppAccessUserIds = array_merge($webAppAccessUserIds, $instance->userIds);
                    }
                    // Vector II → De-duplicate each dimension independently, preserving order.
                    if (!empty($webAppAccessRoles)) {
                        $webAppAccessRoles = array_values(array_unique($webAppAccessRoles));
                    }
                    if (!empty($webAppAccessUserIds)) {
                        $webAppAccessUserIds = array_values(array_unique($webAppAccessUserIds));
                    }
                    // Vector III → Burn the grant vectors into the Route.
                    if (!empty($webAppAccessRoles)) {
                        $route->accessRoles = $webAppAccessRoles;
                    }
                    if (!empty($webAppAccessUserIds)) {
                        $route->accessUserIds = $webAppAccessUserIds;
                    }

                    // Vector 0 → The Absolute Veto inherits Nexus-level first (class-level #[Block]) ...
                    $webAppBlockRoles   = $nexusBlockRoles   ?? [];
                    $webAppBlockUserIds = $nexusBlockUserIds ?? [];

                    // Vector I
                    foreach ($methodAttrs[Block::class] ?? [] as $blockAttr) {
                        $instance = $blockAttr->newInstance();
            
                        $webAppBlockRoles   = array_merge($webAppBlockRoles,   $instance->roles);
                        $webAppBlockUserIds = array_merge($webAppBlockUserIds, $instance->userIds);
                    }
                    // Vector II → De-duplicate each veto dimension independently.
                    if (!empty($webAppBlockRoles)) {
                        $webAppBlockRoles = array_values(array_unique($webAppBlockRoles));
                    }
                    if (!empty($webAppBlockUserIds)) {
                        $webAppBlockUserIds = array_values(array_unique($webAppBlockUserIds));
                    }
                    // Vector III → Burn the veto vectors into the Route.
                    if (!empty($webAppBlockRoles)) {
                        $route->blockRoles = $webAppBlockRoles;
                    }
                    if (!empty($webAppBlockUserIds)) {
                        $route->blockUserIds = $webAppBlockUserIds;
                    }

                    // =============================================================
                    // 🛡️ VALIDATION DECREES
                    // =============================================================

                    // ✨ VALIDATION DECREE: inherit Nexus-level validation
                    /*if (!empty($nexusValidationRules)) {
                        $route->attributes['_validation'] = $nexusValidationRules;
                    }*/

                    $webAppValidationRules = $nexusValidationRules;

                    // Method-level Validate for index() / handle()
                    $webAppHandlerMethodName = $webAppHandler[1];

                    foreach (
                        ($manifest['methods'][$webAppHandlerMethodName][Validate::class] ?? [])
                        as $validateAttr
                    ) {
                        $instance = $validateAttr->newInstance();

                        foreach ($instance->toArray() as $parameter => $rules) {
                            $webAppValidationRules[$parameter] = array_merge(
                                $webAppValidationRules[$parameter] ?? [],
                                $rules
                            );
                        }
                    }

                    if (!empty($webAppValidationRules)) {
                        $route->attributes['_validation'] = $webAppValidationRules;
                    }

                    // ✨ DECREE OF ENRICHMENT: Transfer the developer's choice to the Route object.
                    $route->autoEnrichPattern = $webAppAttrInstance->autoEnrich;
            
                    // Apply name if it exists, relative to nexus prefix
                    $finalName = $webAppAttrInstance->name ? $this->_resolveRelativePathName($webAppAttrInstance->name, $nexusNamePrefix) : null;
                    if ($finalName)
                        $route->name($finalName);
            
                    // We need to re-create a temporary configure closure here or refactor.
                    // For simplicity, let's configure it directly.
                    $route->middleware(array_merge($nexusMiddlewares)); // Add method-specific if handler method has middleware
                    if (!empty($nexusPlatformRestrictions)) {
                        $route->platforms($nexusPlatformRestrictions);
                    }

                    $classAccessPolicy = $webAppAttrInstance->accessPolicy ?? config('krubot.webapps.access_policy', 'strict');
                    if (method_exists($route, 'accessPolicy')) {
                        $route->accessPolicy($classAccessPolicy);
                    }

                    $this->handlerToRouteMap[implode('::', $webAppHandler)] = $route;
                    $webPathKey = substr($route->getPattern(), strpos($route->getPattern(), '::') + 2);
                    $this->webPathToRouteMap[$webPathKey] = $route;
                }
            }

            // PHASE B & C: Process Methods using the Manifest
            // We iterate over the pre-built manifest array containing ONLY methods with Attributes.
            foreach ($manifest['methods'] as $methodName => $attributesMap) {
                
                // -------------------------------------------------------------
                // PHASE B: Extract Action-Level Middlewares
                // -------------------------------------------------------------
                $methodMiddlewares = [];
                foreach ($attributesMap[Middleware::class] ?? [] as $mWAttr) {
                    // Merge supports multiple attributes: #[Middleware('A')] #[Middleware('B')]
                    $methodMiddlewares = array_merge($methodMiddlewares, $mWAttr->newInstance()->middlewares);
                }

                // -------------------------------------------------------------
                // PHASE C: The Architect's Decree - Consolidate Restrictions & Middlewares (The Stack Assembly)
                // -------------------------------------------------------------
                
                // Middlewares are a simple merge (Union).
                $finalMiddlewareStack = array_merge($nexusMiddlewares, $methodMiddlewares);

                // Perform intra-level merge (Union) for method restrictions
                /*
                $methodPlatformRestrictions = [];
                foreach ($attributesMap[RestrictTo::class] ?? [] as $attr) {
                    $methodPlatformRestrictions = array_merge($methodPlatformRestrictions, $attr->newInstance()->getPlatforms());
                }
                */                
                // ✨ THE AMBASSADOR'S REPORT (METHOD-LEVEL) ✨
                $methodPlatformRestrictions = [];
                if (isset($attributesMap[RestrictTo::class][0])) {
                    /** @var \KrubiK\Attributes\RestrictTo $instance */
                    $instance = $attributesMap[RestrictTo::class][0]->newInstance();
                    $methodPlatformRestrictions = array_merge($methodPlatformRestrictions, $instance->getResolvedPlatforms());
                }
                $methodPlatformRestrictions = array_values(array_unique($methodPlatformRestrictions));

                // ✨ THE CORRECT LOGIC ✨
                // The policy is a simple, powerful, optimistic MERGE (Union|OR).
                // We combine both lists and then find the unique values.
                $finalPlatformRestrictions = array_merge(
                    $nexusPlatformRestrictions,
                    $methodPlatformRestrictions
                );
                // Platform Guards follow the nuanced merging policy.
                $finalPlatformRestrictions = array_values(array_unique($finalPlatformRestrictions));
                // Ensure final array has clean keys.

                // ─── AdminIds (Method Level) + OR-Merge with Class ────────────────
                $methodAdminIds = [];
                foreach ($attributesMap[AdminIds::class] ?? [] as $adminAttr) {
                    $methodAdminIds = array_merge($methodAdminIds, $adminAttr->newInstance()->ids);
                }

                $finalAdminIds = array_values(array_unique(
                    array_merge($nexusAdminIds, $methodAdminIds)
                ));


                // ✨ THE GATEKEEPER'S DECREE (METHOD-LEVEL & FINAL MERGE) ✨
                $methodAccessRoles = [];
                $methodAccessUserIds = [];
                foreach ($attributesMap[Access::class] ?? [] as $accessAttr) {
                    $instance = $accessAttr->newInstance();
                    $methodAccessRoles = array_merge($methodAccessRoles, $instance->roles);
                    $methodAccessUserIds = array_merge($methodAccessUserIds, $instance->userIds);
                }               
                // The Optimistic Merge (Union|OR): If Class requires Admin OR Method requires VIP,
                // the user must have at least one of the roles defined in the unified array.
                $finalAccessRoles = array_merge(
                    $nexusAccessRoles,
                    $methodAccessRoles
                );
                $finalAccessRoles = array_values(array_unique($finalAccessRoles));
                $finalAccessUserIds = array_merge(
                    $nexusAccessUserIds,
                    $methodAccessUserIds
                );
                $finalAccessUserIds = array_values(array_unique($finalAccessUserIds));


                // 🚫 THE BLOCK GATEKEEPER'S DECREE (METHOD-LEVEL & FINAL MERGE) 🚫
                $methodBlockRoles = [];
                $methodBlockUserIds = [];
                foreach ($attributesMap[Block::class] ?? [] as $blockAttr) {
                    $instance = $blockAttr->newInstance();
                    $methodBlockRoles = array_merge($methodBlockRoles, $instance->roles);
                    $methodBlockUserIds = array_merge($methodBlockUserIds, $instance->userIds);
                }
                // The Absolute Veto (Union|OR): Class bans X OR Method bans Y → both are enforced.
                // Unlike Access (which is a "grant" union), Block's union makes the VETO STRONGER.
                $finalBlockRoles = array_merge(
                    $nexusBlockRoles,
                    $methodBlockRoles
                );
                $finalBlockRoles = array_values(array_unique($finalBlockRoles));
                $finalBlockUserIds = array_merge(
                    $nexusBlockRoles,
                    $methodBlockUserIds
                );
                $finalBlockUserIds = array_values(array_unique($finalBlockUserIds));


                // ✨ NEW: Determine the Access Policy for this route
                // For now, we pull from global config. Later this can be Enhanced with an #[AccessPolicy] attribute.
                /// $finalAccessPolicy = config('krubot.webapps.access_policy', 'strict');

                // ==========================================================
                // === ⚡️ NEW LOGIC: PRE-COMPILE #[When] GUARDS ⚡️ ===
                // ==========================================================
                $whenGuardInstances = [];
                foreach ($attributesMap[When::class] ?? [] as $whenAttrReflection) {
                    // newInstance() is fast because our When constructor is optimized.
                    $whenGuardInstances[] = $whenAttrReflection->newInstance();
                }

                // ================================================================
                // 📚 METHOD-LEVEL RULE SETS
                // Note! Location is irrelevant and not important. the named rule-set enters the KrubotNexus Registry.
                // ================================================================
                foreach ($attributesMap[RuleSet::class] ?? [] as $ruleSetAttr) {
                    /** @var RuleSet $instance */
                    $instance = $ruleSetAttr->newInstance();

                    $this->ruleSets[$instance->name] = array_merge(
                        $this->ruleSets[$instance->name] ?? [],
                        $instance->rules
                    );
                }

                $methodValidationRules = [];
                foreach ($attributesMap[Validate::class] ?? [] as $attr) {
                    /** @var Validate $instance */
                    $instance = $attr->newInstance();

                    foreach ($instance->toArray() as $parameter => $rules) {
                        $methodValidationRules[$parameter] = array_merge(
                            $methodValidationRules[$parameter] ?? [],
                            $rules
                        );
                    }
                }

                $finalValidationRules = $nexusValidationRules;

                foreach ($methodValidationRules as $parameter => $rules) {
                    $finalValidationRules[$parameter] = array_merge(
                        $finalValidationRules[$parameter] ?? [],
                        $rules
                    );
                }

                // ✨ THE CONDUIT'S FOCUS (METHOD-LEVEL & FINAL MERGE) ✨
                // Now we listen for the specific call of ForceJoin on the method itself.
                $methodForceJoinChannels = [];
                $methodForceJoinFailMessage = null; // <- Variable to hold METHOD-level message
                foreach ($attributesMap[ForceJoin::class] ?? [] as $forceJoinAttr) {
                    $methodForceJoinChannels = array_merge(
                        $methodForceJoinChannels,
                        $forceJoinAttr->newInstance()->channels
                    );

                    // The method's message is king. If set, it's the one we'll use.
                    if ($instance->failMessage !== null) {
                        $methodForceJoinFailMessage = $instance->failMessage;
                    }
                }

                // The final, sacred union: Class-level and Method-level channels are merged.
                // This creates the definitive list of channels for this specific route.
                $finalForceJoinChannels = array_merge(
                    $nexusForceJoinChannels,
                    $methodForceJoinChannels
                );
                $finalForceJoinChannels = array_values(array_unique($finalForceJoinChannels));

                // --- THE PRECEDENCE RULING ---
                // The final message is the method's message. If it's null, we use the class's message.
                $finalForceJoinFailMessage = $methodForceJoinFailMessage ?? $nexusForceJoinFailMessage;

                $handlerCallback = [$className, $methodName];

                // -------------------------------------------------------------
                // PHASE D: Route Identification & Configuration
                // -------------------------------------------------------------
                /// $routeName = isset($attributesMap[Name::class][0]) ? $attributesMap[Name::class][0]->newInstance()->name : null;

                // ✨ NEW LOGIC: Resolve the final route name using the new relative logic.
                $rawRouteName = isset($attributesMap[Name::class][0]) 
                    ? $attributesMap[Name::class][0]->newInstance()->name 
                    : null;                
                // If a name attribute exists, resolve it. Otherwise dont waste your power, it's null head.
                $routeName = $rawRouteName ? $this->_resolveRelativePathName($rawRouteName, $nexusNamePrefix) : null;

                // Step D.1: Dynamic Parameter Discovery & Pattern Enrichment Closure 🧠
                $enrichRoutePatternAndParams = function(Route $route) use ($className, $methodName) {
                    if (!$route) return;

                    $pattern = $route->getPattern();
                    
                    try {
                        $reflectionMethod = new ReflectionMethod($className, $methodName);
                        $requiredParamsToAppend = [];
                        $allPathParams = [];

                        // Match placeholders already declared in the route pattern (e.g. {productId} or {productId?})
                        preg_match_all('/\{([a-zA-Z0-9_]+)\??\}/', $pattern, $matches);
                        $existingPlaceholders = $matches[1] ?? [];

                        foreach ($reflectionMethod->getParameters() as $param) {
                            $paramName = $param->getName();
                            $paramType = $param->getType();

                            // Skip dependency-injected system classes (e.g. Krubot, Request)
                            if ($paramType instanceof ReflectionNamedType && !$paramType->isBuiltin()) {
                                continue;
                            }

                            // Handle union/intersection types of classes (skip if no primitive types are present)
                            if ($paramType instanceof ReflectionUnionType || $paramType instanceof ReflectionIntersectionType) {
                                $hasBuiltin = false;
                                foreach ($paramType->getTypes() as $type) {
                                    if ($type->isBuiltin()) {
                                        $hasBuiltin = true;
                                        break;
                                    }
                                }
                                if (!$hasBuiltin) {
                                    continue;
                                }
                            }

                            $allPathParams[] = $paramName;

                            if (!in_array($paramName, $existingPlaceholders, true)) {
                                // Only auto-append to path if the parameter is required (no default value)
                                if (!$param->isDefaultValueAvailable()) {
                                    $requiredParamsToAppend[] = $paramName;
                                }
                            }
                        }

                        // Append required implicit parameters to the pattern
                        if (!empty($requiredParamsToAppend)) {
                            $pattern = rtrim($pattern, '/');
                            foreach ($requiredParamsToAppend as $reqPam) {
                                $pattern .= '/{' . $reqPam . '}';
                                $existingPlaceholders[] = $reqPam;
                            }
                            $route->pattern = $pattern;
                        }

                        // Save identified path parameters onto the Route instance
                        $route->pathParameters = array_values(array_unique(array_merge($existingPlaceholders, $allPathParams)));

                    } catch (ReflectionException $e) {
                        // Fallback: extract placeholders from pattern directly if Reflection fails
                        preg_match_all('/\{([a-zA-Z0-9_]+)\??\}/', $pattern, $matches);
                        $route->pathParameters = $matches[1] ?? [];
                    }
                };

                // Step D.2: The Configuration Helper Closure 🛠 //To-Do:: Support PlatformRestricion Here
                $_configureRoute = function (?Route $route = null, ?string $accessPolicy = null) use ($routeName, $finalMiddlewareStack, $finalPlatformRestrictions, $whenGuardInstances, $finalAdminIds, $finalAccessRoles, $finalBlockRoles, $finalAccessUserIds, $finalBlockUserIds, $finalValidationRules, $finalForceJoinChannels, $finalForceJoinFailMessage, $enrichRoutePatternAndParams, $handlerCallback) {
                    if (!$route) return;

                    // [THE BRAIN] enrichmentation central decision point. Clean, simple, and powerful.
                    if (in_array($route->type, [self::RT_WEB_APP, self::RT_WEB_PAGE, self::RT_WEB_ACTION], true)) {

                        // Dynamically discover parameter needs and enrich the Route Pattern for Route registration
                        /// $enrichRoutePatternAndParams($route); // Apply enrichment HERE

                        // 🔥 THE NEW CENTRAL DECISION POINT 🔥
                        // Instead of checking the route type, we check the explicit `autoEnrichment` flag.
                        // This is the core of the new architecture: the developer's intent, carried from
                        // the attribute, directly controls the "magic" of route modification.
                        if ($route->autoEnrichPattern === true) {
                            $enrichRoutePatternAndParams($route);
                        } else {
                            // If enrichment is disabled, we still need to detect existing placeholders.
                            preg_match_all('/\{([a-zA-Z0-9_]+)\??\}/', $route->getPattern(), $matches);
                            $route->pathParameters = $matches[1] ?? [];
                        }

                        // ✨ NEW: Apply access policy passed as an argument, if the route object supports it.
                        if (method_exists($route, 'accessPolicy')) {
                            // Use the specific sent policy for THIS route, or fall back to system default

                            // ✨ OPTIMIZED: Use the pre-cached static property as the fallback.
                            // This avoids hitting the config system for every single web route.

                            $policyToApply = $accessPolicy ?? WebApp::$systemDefaultAccessPolicy;
                            $route
                                ->accessPolicy($policyToApply);

                        }

                        /// 3. REGISTER & BRIDGE using the FINAL pattern
                        /// $this->_registerAndBridgeHttpRoute($route, $httpMethods); // OBSOLETE, NOT NEEDED

                    }

                    if ($routeName) $route->name($routeName);
                    if (!empty($finalMiddlewareStack)) $route->middleware($finalMiddlewareStack);

                    // Only apply platform restrictions if the final merged list is not empty.
                    // An empty list means no restrictions were specified anywhere, so it's universally available in WarLord Grade.
                    if (!empty($finalPlatformRestrictions)) {
                        $route->platforms($finalPlatformRestrictions);
                    }

                    // Attach the pre-compiled guards to the Route object.
                    if (!empty($whenGuardInstances)) {
                        $route->guards($whenGuardInstances);
                    }

                    if (!empty($finalValidationRules)) {
                        $route->attributes['_validation'] = $finalValidationRules;
                    }

                    // ✨♥️ THE UNIFIED ENERGY IS CHANNELED ♥️✨
                    // We now endow the Route object with the final list of ForceJoin channels.
                    // The Dispatcher will later access this property to perform its magic.
                    if (!empty($finalForceJoinChannels)) {
                        $route->forceJoinChannels = $finalForceJoinChannels; // + ✨ این خط، انرژی را به مسیر تزریق می‌کند

                        // --- THE FINAL ASSIGNMENT ---
                        // We now burn the final message string onto the Route object itself.
                        $route->forceJoinMessage = $finalForceJoinFailMessage;

                    }

                    if (!empty($finalAdminIds)) {
                        $route->adminIds = $finalAdminIds;
                    }

                    // 🛡️ THE QUANTUM CLEARANCE LEVEL IS BURNED INTO THE ROUTE 🛡️
                    if (!empty($finalAccessRoles)) {
                        $route->accessRoles = $finalAccessRoles;
                    }
                    if (!empty($finalAccessUserIds)) {
                        $route->accessUserIds = $finalAccessUserIds;
                    }

                    // 🚫 THE ABSOLUTE VETO IS BURNED INTO THE ROUTE 🚫
                    if (!empty($finalBlockRoles)) {
                        $route->blockRoles = $finalBlockRoles;
                    }
                    if (!empty($finalBlockUserIds)) {
                        $route->blockUserIds = $finalBlockUserIds;
                    }

                    // Map Class::method key to the Route instance
                    if (is_array($handlerCallback) && count($handlerCallback) === 2) {
                        $handlerKey = $handlerCallback[0] . '::' . $handlerCallback[1];
                        $this->handlerToRouteMap[$handlerKey] = $route;
                    }

                    // Populate type-specific fast-lookup maps
                    switch ($route->type) {
                        case self::RT_COMMAND:
                            $this->commandToRouteMap[trim($route->getPattern(), '/')] = $route;
                            break;
                        case self::RT_WEB_APP:
                        case self::RT_WEB_PAGE:
                        case self::RT_WEB_ACTION:
                            // The pattern for web routes is prefixed, e.g., 'WAPP::game.dashboard'
                            $webPathKey = substr($route->getPattern(), strpos($route->getPattern(), '::') + 2);
                            $this->webPathToRouteMap[$webPathKey] = $route;
                            break;
                    }
                };

                // -------------------------------------------------------------
                // PHASE E: Attribute-Based Route Registration (Optimized Manifest Loop)
                // -------------------------------------------------------------

                /* 
                 * [LEGACY LOGIC REMOVED FOR PERFORMANCE - Heavy Reflection calls in Loop]
                 * foreach ($method->getAttributes(OnCommand::class) as $attribute) { ... }
                 * foreach ($method->getAttributes(OnText::class) as $attribute) { ... }
                 */

                foreach ($attributesMap[OnCommand::class] ?? [] as $attr) {
                    $_configureRoute($this->onCommand($attr->newInstance()->command, $handlerCallback));
                }

                foreach ($attributesMap[OnText::class] ?? [] as $attr) {
                    $_configureRoute($this->onText($attr->newInstance()->pattern, $handlerCallback));
                }

                foreach ($attributesMap[OnRegEx::class] ?? [] as $attr) {
                    $pattern = $attr->newInstance()->pattern;
                    if (!preg_match('/^\/.*\/[a-zA-Z]*$/', $pattern)) {
                        $pattern = '/' . $pattern . '/';
                    }
                    $_configureRoute($this->onText($pattern, $handlerCallback));
                }

                // Handle #[Receive] Attribute 👁️
                foreach ($attributesMap[Receive::class] ?? [] as $instance) {
                    // Extract the types (it can be string or array in the Attribute)
                    $targetTypes = $instance->frequency; 
                    
                    // The onType method natively supports both string and array returns
                    $resultingRoutes = $this->onType($targetTypes, $handlerCallback);
                    
                    // If it returned an array of Routes (multi-type), configure all of them
                    if (is_array($resultingRoutes)) {
                        foreach ($resultingRoutes as $r) $_configureRoute($r);
                    } else {
                        $_configureRoute($resultingRoutes);
                    }
                }

                // ✨ NEW: Handle #[OnInlineQuery] Attribute ⚡️
                foreach ($attributesMap[OnInlineQuery::class] ?? [] as $attr) {
                    /** @var \KrubiK\Attributes\OnInlineQuery $instance */
                    $instance = $attr->newInstance();
                    // We call our new, intelligent public method.
                    // This keeps the logic centralized and the scanner clean.
                    $_configureRoute($this->onInlineQuery($instance->pattern, $handlerCallback));
                }

                // ✨ NEW: Handle #[Fallback] Attribute (Global)
                // This attribute does not create a route, it registers a special handler.
                if (isset($attributesMap[Fallback::class][0])) {
                    // The last detected Fallback handler wins.
                    // Consider adding a warning if this is set more than once.
                    
                    // Centralize the logic and makes the scanner's job simpler.
                    $this->fallback($handlerCallback);
                }

                // ✨ NEW: Handle #[FallbackOn] Attribute (Type-Specific)
                foreach ($attributesMap[FallbackOn::class] ?? [] as $attr) {
                    /** @var \KrubiK\Attributes\FallbackOn $instance */
                    $instance = $attr->newInstance();

                    /// Update ✨ Register with priority instead of blind overwriting
                    /// foreach($instance->types as $type) $this->typeFallbackHandlers[$type] = $handlerCallback;

                    // Delegate the fallbacks registration and priority logic to the dedicated helper.
                    // This is the epitome of clean architecture.
                    $this->fallbackOn(
                        $instance->types, 
                        $handlerCallback, 
                        $instance->priority
                    );
                }

                foreach ($attributesMap[Action::class] ?? [] as $attr) {
                    // ⚡ [FIXED]: Action uses 'name' not 'command'.
                    $_configureRoute($this->onAction($attr->newInstance()->name, $handlerCallback)); 
                }

                // ✨ NEW: Handle #[WebApp] Attribute (IS_REPEATABLE / multi-url mapping)
                foreach ($attributesMap[WebApp::class] ?? [] as $attrInstance) {
                    /** @var \App\Attributes\WebPage $instance */
                    $instance = $attrInstance->newInstance();
                    $path = $instance->path;
                    $methods = $instance->methods;

                    $route = $this->onWebApp($path, $handlerCallback, $methods);

                    // ✨ DECREE OF ENRICHMENT: Transfer the flag from attribute to Route instance.
                    $route->autoEnrichPattern = $instance->autoEnrich;

                    // Read policy from the specific attribute data and pass it to the _configureRoute closure
                    $routeSpecificAccessPolicy = $instance->getAccessPolicy();
                    $_configureRoute($route, $routeSpecificAccessPolicy);
                }

                // ✨ NEW: Handle Register WebPage Routes (IS_REPEATABLE / multi-url mapping)
                foreach ($attributesMap[WebPage::class] ?? [] as $attr) {
                    /** @var \App\Attributes\WebPage $instance */
                    $instance = $attr->newInstance();
                    $finalPath = $this->_resolveRelativePathName($instance->path ?? $instance->name, $webAppPrefix); // Smart Path Resolution: Prepend prefix if path is relative (starts with '.')
                    
                    $route = $this->onWebPage($finalPath, $handlerCallback, ['methods' => $instance->methods]);

                    // ✨ DECREE OF ENRICHMENT: Transfer the flag from attribute to Route instance.
                    $route->autoEnrichPattern = $instance->autoEnrich;

                    // Apply name if it exists, relative to nexus prefix
                    $finalName = $instance->name ? $this->_resolveRelativePathName($instance->name, $nexusNamePrefix) : null;
                    if ($finalName)
                        $route->name($finalName);

                    // Read policy from the specific attribute data and pass it to the _configureRoute closure
                    $routeSpecificAccessPolicy = $instance->getAccessPolicy();
                    $_configureRoute($route, $routeSpecificAccessPolicy);
                }

                // ✨ NEW: Handle Register WebAction Routes (IS_REPEATABLE / multi-url mapping)
                foreach ($attributesMap[WebAction::class] ?? [] as $attr) {
                    /** @var \KrubiK\Attributes\WebAction $instance */
                    $instance = $attr->newInstance();
                    $finalPath = $this->_resolveRelativePathName($instance->getName(), $webAppPrefix); // Smart Path Resolution: Prepend prefix if path is relative (starts with '.')
                    
                    $route = $this->onWebAction($finalPath, $handlerCallback, $instance->getMethods(), ['description' => $instance->getDescription()]);

                    // ✨ DECREE OF ENRICHMENT: Transfer the flag from attribute to Route instance.
                    $route->autoEnrichPattern = $instance->autoEnrich;

                    // Apply name if it exists, relative to nexus prefix
                    $finalName = $this->_resolveRelativePathName($instance->getName(), $nexusNamePrefix);
                    if ($finalName)
                        $route->name($finalName);

                    // Read policy from the specific attribute data and pass it to the _configureRoute closure
                    $routeSpecificAccessPolicy = $instance->getAccessPolicy();
                    $_configureRoute($route, $routeSpecificAccessPolicy);
                }
            }

            // ONLY After all nexuses have been scanned, resolve the priorities.
            if($isSingleNexus)
                $this->prioritizeFallbacks();

            // [CRITICAL FIX] Mark as integrated *after* successful processing.
            $this->integratedNexuses[$className] = true;

            // 🔥 EVENT: nexus.integrated — post-scan hook, run side-effects.
            JackPoint::fire('nexus.integrated', $className, $this);

        } catch (\ReflectionException $e) {

            // 🔥 EVENT: nexus.failed — observability, not a crash.
            $allowReport = JackPoint::fire('nexus.failed', $className, $e, $this);

            if($allowReport !== false)
                // Critical Error Handling:
                AmethystMatrix::yell("Nexus Integration Failed: The Singularity Engine encountered a critical reflection error.", [
                    'nexus_target' => $className,
                    'error_message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
        }
    }

    

    // End Deprecation _ Area
    // Welcome to New PowerFUL...
    public function processUpdate(Message $message): void
    {
        $startedAt = microtime(true);
        $before = JackPoint::fire('update.before', $message);

        // Returning false is an explicit interception contract.
        if ($before === false) {
            $allowSkip = JackPoint::fire('update.skipped', $message);
            if($allowSkip !== false)
                return;
        }

        try {
            $this->processUpdateKernel($message);
            JackPoint::fire('update.after', $message, $this->response(), microtime(true) - $startedAt);
        } catch (\Throwable $e) {
            $allowReport = JackPoint::fire('update.failed', $message, $e, microtime(true) - $startedAt);
            if($allowReport !== false)
                throw $e;
        } finally {
            JackPoint::fire('update.finally', $message, $this->response(), microtime(true) - $startedAt);
        }
    }
    /**
     * =========================================================================
     *  ⚡ THE ULTRA-POWERFUL ROUTING ENGINE v12.0 (MULTI-VERSE ULTIMATE CONSOLIDATED)
     * =========================================================================
     * 
     * The definitive "Brain" of KrubiK.
     * 
     * 💎 PERFORMANCE ARCHITECTURE:
     * 1. Normalization on-the-fly: Detects Route Object vs Array ONCE via `$isSmartRoute`.
     * 2. Early Guards: Checks 'recipient' restrictions BEFORE expensive Regex engines.
     * 3. Smart Matching: Exact Match (O(1)) -> Param Match (Fast String Search) -> Regex (Power).
     * 4. Intelligent Assembly: Delegates middleware logic to Route Class #2 if available.
     * 5. Dual-Pipeline: Laravel Pipeline (Preferred) -> Native Robust Fallback (with Aliases).
     * 6. Now fully aware of the 4th Dimension: Glass Buttons (Callbacks) and Action-based Conversational Routing.
     * 
     * @param Message $message The incoming update message.
    */
    public function processUpdateKernel(Message $message): void
    {
        // =====================================================================
        // PHASE 0: STATE INITIALIZATION & OPTIMIZATION
        // =====================================================================
        
        // 1. Global State Injection
        $this->currentMessage = $message;

                // 2. Prime the fluent builder's chat_id from the message.
        //
        // WHY: CanSendFluentMessages::send() resolves the target via:
        //   $targetChatId = $chatId ?? ($this->chat_id ?? null);
        //   if (!$targetChatId) { $targetChatId = $this->builder_chat_id ?? throw ... }
        //
        // $this->chat_id is the NeonVitality / fluent builder's context property —
        // it is NOT the same as $message->chat_id.  When Krubot::reply() calls
        // $this->chat($this->chatId()), chatId() returns $message->chat_id which
        // may be null if Message::fromInboundPayload() did not surface it.
        //
        // We prime all three sources from the heart DTO so send() always wins:
        //   • $message->chat_id    → chatId() accessor used by reply()/say()
        //   • $this->chat_id       → send() source 2
        //   • $this->builder_chat_id → send() source 3 (last resort)
        $heartChatId = (string) (
            $message->chat_id
            ?? $message->heart?->chatId
            ?? $message->heart?->effectiveData['chat']['id']
            ?? $message->heart?->coreData['message']['chat']['id']
            ?? $message->heart?->coreData['callback_query']['message']['chat']['id']
            ?? ''
        );

        if ($heartChatId !== '') {
            // Surface on the Message object so chatId() accessor works
            $message->chat_id ??= $heartChatId;

            // Prime the fluent builder so send() finds it without chat() being called
            $this->chat_id         = $heartChatId;
            $this->builder_chat_id = $heartChatId;
        }
        
        // 2. Primitive Extraction (Memory Optimization)
        // Extract text once to avoid repeated property access. Ensure string type.
        /// $text = $message->text ?? '';
        
        // 3. Reset Request State (Lazarus/Swoole/RoadRunner Compatibility)
        // Crucial for long-running processes to prevent data leakage between requests.
        $this->currentRouteParams = [];
        $this->activeRoute = null;
        $this->finalResponse = null;

        $this->resetContextData(); // 🌋 THE ASYNC GUARDIAN: WIPE THE SLATE CLEAN! [bot->getX() && bot->setX() data]
        $this->tunnelAmethyst($message); // We Can Auto-Fill it by $this->currentMessage, but not now!

        // =========================================================================
        // 🧠 SENSORY PRE-COMPUTATION (ONCE AND FOR ALL ROUTES)
        // THE UNIFIED SIGNAL RESOLUTION ⚡️
        // =========================================================================
        // The call now unpacks 5 values. `resolveRoutingSignal` is now the
        // Single Source of Truth for Every Signal detections.
        [$routingType, $routingPayload, $actionParams, $envelopeSignal, $contentSignal] = $this->resolveRoutingSignal($message);

        // Early exit if no signal and no fallback handler exists.
        if ($routingType === self::RT_NONE && !$this->fallbackHandler) {
            $this->tunnelAmethyst();
            return;
        }

        $text = $routingPayload  ?? ''; // Fill $text from resolvedSignal

        // =====================================================================
        // PHASE 1: THE FINDER (MATCHING LOOP)
        // =====================================================================
        
        $matchedRoute = null;
        $finalRouteParams = [];
        $isSmartRoute = false; // Optimization Flag
        $isSmartRouteCandidate = false;

        // The core of "The Great Filter". Defines which route types are valid for each signal.
        $allowedMatches = [
            // A text signal can match text, command, or regex routes.
            self::RT_TEXT         => [self::RT_TEXT, self::RT_COMMAND, self::RT_REGEX],

            // AN ACTION SIGNAL (from a callback_button OR web_app_data) can match a
            // standard button Action or a WebAction. THIS IS OUR UNIFIED HIGHWAY.
            self::RT_ACTION       => [self::RT_ACTION, self::RT_WEB_ACTION],

            // ✨ NEW: An Inline Query signal can ONLY match an Inline Query route.
            self::RT_INLINE       => [self::RT_INLINE],

            // A message type signal (photo, video) matches type routes.
            self::RT_SIGNAL         => [self::RT_SIGNAL],

            // A direct Web Request signal can match a WebPage or a WebAction.
            // This is for direct browser/AJAX calls to Laravel.
            self::RT_WEB          => [self::RT_WEB_APP, self::RT_WEB_PAGE, self::RT_WEB_ACTION],  // A generic  WebApp signal can match ANY web route type        

            /// self::RT_WEB_APP_DATA => [self::RT_WEB_ACTION], // But Data from JS `[TG||Bl].WebApp.sendData()` should trigger a WebAction
            /// self::RT_WEB_ACTION   => [self::RT_WEB_ACTION],
        ];

        // This line finds the valid route types for the given signal.
        $validRouteTypesForSignal = $allowedMatches[$routingType] ?? [];

        /*
        if(empty($validRouteTypesForSignal)) {
            // If the signal type doesn't map to any valid route types, we can potentially exit early.
            // However, the conversation interceptor logic below might still need to run, so we proceed.
        }
        */

        // ⚔️ RESOLVE CURRENT PLATFORM ONCE - BEFORE THE LOOP ⚔️
        // This value is constant for the entire request lifecycle.
        $currentPlatform = $this->resolveCurrentPlatform();

        // Iterate through all registered routes to find the FIRST match.
        foreach ($this->routes as $pattern => $routeItem) {

            // --- A) PRE-COMPUTATION & ATTRIBUTE EXTRACTION ---
            $isSmartRouteCandidate = ($routeItem instanceof Route);
            $attributes = $isSmartRouteCandidate ? $routeItem->getAttributes() : ($routeItem['attributes'] ?? []);
            $routeTypeAttribute = $isSmartRouteCandidate ? $routeItem->type : ($attributes['_route_type'] ?? null);
            $routeTypeAttribute ??= self::RT_TEXT;

            // ⚡️ GREAT FILTER ⚡️
            // If the route's type is not in the list of valid types for the current signal, skip it instantly.
            if (!in_array($routeTypeAttribute, $validRouteTypesForSignal, true)) {
                continue;
            }
            
            // --- A) NORMALIZATION & TYPE DETECTION ---
            // We determine the route type HERE to avoid `instanceof` checks in the critical execution path later.
            
            if (is_object($routeItem) && method_exists($routeItem, 'getPlatforms')) {
                // MODERN: Route Object (Class #2)
                // We call getAttributes() to handle Guard checks.
                // $attributes = method_exists($routeItem, 'getAttributes') ? $routeItem->getAttributes() : [];
                $isSmartRouteCandidate = true; 
            } elseif (is_array($routeItem)) {
                // LEGACY: Array Structure ['action' => ..., 'attributes' => ...]
                $attributes = $routeItem['attributes'] ?? [];
                $isSmartRouteCandidate = false;
            } else {
                // RAW: Callable fallback
                $attributes = [];
                $isSmartRouteCandidate = false;
            }

            // --- B) SECURITY GUARDS (PRE-REGEX OPTIMIZATION) ---
            // strict conditions checked BEFORE running expensive Regex engine.

            // 🛡️ ADVANCED GATES 🛡️
            // This checks only runs on modern Route objects that support these features.
            if ($isSmartRouteCandidate) {

                // 🛡️ GATE 1: THE PLATFORM GUARD (CRITICAL ADDITION) 🛡️
                // This is where we enforce #[RestrictTo] attributes.
                if(!$routeItem->isAllowedOn($currentPlatform))
                    // ❌ اجازه عبور نداری! به روت بعدی برو.
                    continue; // SILENTLY DENY. The user on the wrong platform should not know this route exists.

                // ✨🛡️ GATE 2: THE FORCEJOIN GUARD (THE DIVINE WILL) 🛡️✨
                // BEFORE any other logic, we ensure the user has pledged their allegiance by joining the required channels.
                if(!$this->handleForceJoinGuard($routeItem)) {
                    // Access is denied. The guard has already informed the user.
                    // We must halt all further processing for THIS request.
                    // We clear the amethyst tunnel and exit the entire processUpdate method.
                    $this->tunnelAmethyst();
                    return;
                }
            }
            
            // GATE 3: Recipient / Channel Restriction
            if (!empty($attributes['recipient'])) {
                $allowedRecipients = (array) $attributes['recipient'];
                $currentChatId = $this->chatId();
                $currentSenderId = $this->senderId();
                
                // Logic: Must match EITHER the ChatID OR the SenderID.
                if (!in_array($currentChatId, $allowedRecipients) && !in_array($currentSenderId, $allowedRecipients)) {
                    continue; // Skip this route immediately
                }
            }
            
            // GATE 3: Driver/Platform Restriction (Future Proofing)
            // if (!empty($attributes['driver']) && $attributes['driver'] !== 'rubika') { continue; }

            // --- C) PATTERN MATCHING ENGINE ---
            // ⚡ Now matching against $routingTarget instead of just $text
            $isMatch = false;
            $matches = [];

            // Strategy 1: Exact String Match (Fastest - O(1))
            if ($text === $pattern) {
                $isMatch = true;
            }
            // STRATEGY 2: [NEW] PARAMETERIZED WEB PATH MATCHER (CURLY BRACE NOTATION)
            // It runs ONLY for web signals on patterns that contain in-url parameters.
            // It is now the primary engine for WebApp/WebAction routes.
            elseif ($routingType === self::RT_WEB) {
                // حذف پیشوندهای مربوط به وب
                $webPrefixes = ['WAPP::', 'WACT::'];
                $cleanPattern = $pattern;
                foreach ($webPrefixes as $prefix) {
                    if (str_starts_with($pattern, $prefix)) {
                        $cleanPattern = substr($pattern, strlen($prefix));
                        break;
                    }
                }
            
                // اگر الگوی تمیز دارای پارامتر باشد، از demystifyWebPath استفاده کن
                if (
                    method_exists($this, 'demystifyWebPath') &&     // if HasWebInterface Trait is Loaded
                    str_contains($cleanPattern, '{')                // Only for patterns with potential parameters
                ) {
                    // We delegate the complex matching logic to a new, dedicated helper method.
                    // This keeps the main loop clean and readable.
                    [$isMatch, $matches] = $this->demystifyWebPath($cleanPattern, $text);
                } else {
                    // تطبیق ساده
                    $isMatch = ($text === $cleanPattern);
                    $matches = [];
                }
            }
            // Strategy 3: NEW ✨ INLINE QUERY MATCH (HYPER-OPTIMIZED) ✨
            // This block will only be evaluated if the Great Filter passed an RT_INLINE_QUERY signal.
            elseif ($routeTypeAttribute === self::RT_INLINE) {
                // Case A: Catch-all route. Matches any inline query.
                if ($pattern === '__ANY__') {
                    $isMatch = true;
                }
                // Case B: Regex/Prefix match. We already converted prefixes to regex in the `onInlineQuery` method.
                // We trust the pattern is a valid regex here.
                elseif (@preg_match($pattern, $text, $m)) {
                    $isMatch = true;
                    // Extract capture groups for parameter injection.
                    // Slicing off the full match at index 0.
                    $matches = array_slice($m, 1);
                }
            }
            // 👁️ Strategy 4: SENSORY TYPE MATCH (TRUE O(1) HYPER-PERFORMANCE) ⚡
            // Evaluates if the route is a Type route and matches our pre-calculated $detectedMediaType
            elseif (str_starts_with($pattern, 'TYPE::')) {
                // Determine actual type defined in route, e.g., 'TYPE::photo' -> 'photo'
                $expectedType = substr($pattern, 6); 

                // 1. Get the strategy flag injected into the Route during definition in onType().
                // This is the "Strategy-Aware Route" Concept in Action.
                $useEnvelopeStrategy = $route->getAttribute('_signal_class', false);

                // 2. Select the correct, pre-computed signal based on the route's own preference.
                $detectedMediaType = $useEnvelopeStrategy ? $envelopeSignal : $contentSignal;
                
                /// 👁️ Re-Awaken the Sensory Engine: Detect the physical type of this message.
                /// $detectedMediaType = $this->detectMessageType($message); // This is now done in resolveRoutingSignal
                
                // 3. Perform a lightning-fast comparison. No more detectMessageType() calls here.
                if ($expectedType === $detectedMediaType) {
                    $isMatch = true;
                }
            }
            // Strategy 5: Parameterized Match (e.g., "/cmd {param}")
            // Optimization: `str_contains` is significantly faster than `preg_match` for pre-check.
            elseif (str_contains($pattern, '{') && str_contains($pattern, '}')) {
                // Escape literals, then convert {param} to Named Group (?<param>.*?)
                // We strictly expect Start(^) and End($) anchors.
                $safePattern = preg_quote($pattern, '/');
                $regex = '/^' . preg_replace('/\\\{(\w+)\\\}/', '(?<$1>.*?)', $safePattern) . '$/iu';
                
                if (preg_match($regex, $text, $m)) {
                    $isMatch = true;
                    // Filter to keep ONLY named string keys for Dependency Injection
                    $matches = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
                }
            }
            // Strategy 6: Explicit Regex Match (Power User)
            // Heuristic: Starts/Ends with slash "/" and length > 2 (to avoid empty "//")
            elseif (str_starts_with($pattern, '/') && str_ends_with($pattern, '/') && strlen($pattern) > 2) {
                if (preg_match($pattern, $text, $m)) {
                    $isMatch = true;
                    // Extract named groups if exist, otherwise use positional matches (slicing off full match)
                    $named = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
                    $matches = !empty($named) ? $named : array_slice($m, 1);
                }
            } 

            // --- D) MATCH CONFIRMATION ---
            if ($isMatch) {

                // A pattern match was found. NOW, we consult the guards.
                // This check only applies to modern, "smart" Route objects.
                // We assume legacy routes do not have guards.
                if ($isSmartRouteCandidate && ($routeItem instanceof Route)) {

                    // Call our new gatekeeper from StormJusticeTurbine
                    if ($this->evaluateRouteGuards($routeItem, $message)) {
                        // ✅ GUARDS PASSED! This is our winner.
                        // Lock in the route and break the loop.
                        $matchedRoute = $routeItem;
                        $finalRouteParams = $matches;
                        $isSmartRoute = true; // Confirmed smart route
                        break; // FIRST *VALID* MATCH WINS - Break the loop.
                    } else {
                        // ❌ GUARDS FAILED!
                        // The user's strategy in action: Silently ignore this match.
                        // Continue the loop to search for the next candidate.
                        continue;
                    }

                } else {
                    // This is a legacy route (array or simple callable) without guards.
                    // A pattern match is enough.
                    $matchedRoute = $routeItem;
                    $finalRouteParams = $matches;
                    $isSmartRoute = false;
                    break; // FIRST MATCH WINS - Break the loop.
                }
        }
        }

        // Merge action params (from button payload) with route params
        // Route params take precedence only if same key appears later:
        // choose your policy. Here: action params first, route params overwrite.
        $finalRouteParams = array_merge($actionParams, $finalRouteParams);
                
        $middlewareStack = [];
        $finalHandler = null;

        // =====================================================================
        // PHASE 2: THE COMPILER (STACK ASSEMBLY)
        // =====================================================================

        if ($matchedRoute) {

            // PATH 2-1: HAPPY PATH - A GLOBAL ROUTE WAS SUCCESSFULLY MATCHED
            // Route exists. Compile the handler and its middleware stack here.

            // 1. Save Context for Middleware Inspection
            $this->activeRoute = $matchedRoute; // is_object($matchedRoute) && $matchedRoute instanceof Route ? $matchedRoute : null;
            $this->currentRouteParams = $finalRouteParams;

            // 2. Assemble Handler & Middleware Stack
            // We leverage the `$isSmartRoute` flag computed in Phase 1.

            if ($isSmartRoute && $matchedRoute instanceof Route) {
                // === MODERN PATH (Route Class #2) ===
                // Delegate logic to the Route object. It knows how to merge Global + Local
                // and handle 'skipGlobalMiddlewares' intelligently.
                
                $finalHandler = $matchedRoute->getAction();
                $middlewareStack = $matchedRoute->getMiddlewareStack($this->globalMiddlewares);
                
            } else {
                // === LEGACY PATH (Backwards Compatibility) ===
                // Manual extraction and merging.
                
                $routeItemArr = is_array($matchedRoute) ? $matchedRoute : ['action' => $matchedRoute];
                $finalHandler = $routeItemArr['action'] ?? null;
                
                $attrs = $routeItemArr['attributes'] ?? [];
                $routeMiddlewares = $attrs['middleware'] ?? [];
                if (!is_array($routeMiddlewares)) $routeMiddlewares = [$routeMiddlewares];
                
                // Simulate 'withoutGlobalMiddleware' manually for arrays
                if (($attrs['withoutGlobalMiddleware'] ?? false) === true) {
                    $middlewareStack = $routeMiddlewares;
                } else {
                    // Standard Order: Global (Outer) -> Local (Inner)
                    $middlewareStack = array_merge($this->globalMiddlewares, $routeMiddlewares);
                }
            }

        }

        // NO specific route was matched. Time to check for fallbacks.
        else {

            // PATH 2-2: NO GLOBAL ROUTE MATCHED. NOW WE INVESTIGATE WHY.
            // No route matched. Handle interceptors, actions, and fallback scenarios here.

            // Keep parameters available for any downstream interceptors or fallback handlers
            $this->currentRouteParams = $finalRouteParams;

            // An ORPHANED BUTTON CLICK was detected. Force it into the pipeline
            // so ConversationMiddleware can check if it belongs to an active conversation.
            if ($routingType === self::RT_ACTION) {
                // Never drop callback actions directly.
                // The Magic Interceptor must run to let ConversationMiddleware catch it.
                // Force global middleware pipeline so ConversationMiddleware can consume #[Action].
                $finalHandler = static function () use ($message) {
                    // intentional no-op
                    // This handler ideally never runs if ConversationMiddleware consumes action and does its job.
                    // ConversationMiddleware, can stop the flow here.
                    // It's a safety net logger.

                    AmethystMatrix::warning(
                        "Orphaned Callback Triggered: No global route caught this. ConversationMiddleware will now inspect.", 
                        ['details' => $message, 'payload' => $message->button_id ?? 'N/A']
                    );
                };
                // Force the global stack which includes ConversationMiddleware.
                $middlewareStack = $this->globalMiddlewares;

                // then continue to pipeline execution branch
            }

            else {

                // Any other unmatched message. Check for our new fallback system.
                // Prioritize Type-Specific Fallbacks first.

                 // PHASE 2.5: DUAL-DETECTION FALLBACK RESOLUTION
                // Executed ONLY if no specific route matched. This logic respects the developer's intent
                // by checking for fallbacks against both Content-first and Envelope-first detection strategies.

                $finalHandler = null;

                // Step 1: Detect with standard priority (Content-first).
                // This is the most common use case, e.g., fallback for any 'photo' or 'sticker'.
                $contentFirstType = $this->detectMessageType($message, false); // $prioritizeEnvelopeDetection = false
                if ($contentFirstType !== Signal::Void && isset($this->typeFallbackHandlers[$contentFirstType])) {
                    $finalHandler = $this->typeFallbackHandlers[$contentFirstType];
                }

                // Step 2: If no match, re-detect with inverted priority (Envelope-first).
                // This catches fallbacks for events like 'edited_message' or 'callback_query'
                // even if the content-first detection identified something else (e.g., 'text' inside an edit).
                if ($finalHandler === null) {
                    $envelopeFirstType = $this->detectMessageType($message, true); // $prioritizeEnvelopeDetection = true
                    // We also check if the detected type is different from the first pass to avoid redundant lookups.
                    if ($envelopeFirstType !== Signal::Void &&
                        $envelopeFirstType !== $contentFirstType &&
                        isset($this->typeFallbackHandlers[$envelopeFirstType]))
                    {
                        $finalHandler = $this->typeFallbackHandlers[$envelopeFirstType];
                    }
                }

                // Now, resolve the final handler based on the dual-detection results.
                if ($finalHandler) {
                    // A type-specific handler was found through one of the strategies.
                    $middlewareStack = $this->globalMiddlewares;
                }
                // Step 3: Global Fallback as the ultimate safety net.
                // This runs if neither Content-first nor Envelope-first detection inspected a specific fallback.
                elseif ($this->fallbackHandler) {
                    $finalHandler = $this->fallbackHandler;
                    // Run Global Middlewares to ensure logging/security even on 404s.
                    $middlewareStack = $this->globalMiddlewares;
                }
                // Step 4: Absolute Dead End. No route, no fallback.
                else { // not found ?
                    // # Dead End #
                    $this->tunnelAmethyst(null); // clear AmethystMatrix working message entry; Then::
                    return; // End of the line. — No PIPELINE RUNNER needed for nothin!
                }
            }

        }

        // =====================================================================
        // PHASE 3: THE RUNNER (PIPELINE EXECUTION)
        // =====================================================================
        
        // The final destination closure that executes the current matching handler.
        $destination = function ($bot) use ($finalHandler, $message, $finalRouteParams, $matchedRoute) {

            // ================================================================
            // 🛡️ VALIDATION GATE
            // ================================================================
            if ($matchedRoute && (!$this->validateRouteInput($matchedRoute))) {
                // Validation already rendered the proper response/reply.                
                return null;   // DO NOT execute the Handler.
            }

            // Fire authorizeAccessRequest whenever ANY access control is declared on the route.
            // Handles both #[Access] (allowlist) and #[Block] (denylist) attributes uniformly.
            $hasAnyAccessControl = $matchedRoute && (
                $matchedRoute->accessRoles    !== [] ||
                $matchedRoute->accessUserIds  !== [] ||
                $matchedRoute->blockRoles     !== [] ||
                $matchedRoute->blockUserIds   !== []
            );

            // ================================================================
            // ✨🛡️ THE QUANTUM GATEKEEPER (SPATIE-BASED ACCESS CONTROL) 🛡️✨
            // ================================================================
            if ($hasAnyAccessControl) {
                if (!$this->authorizeAccessRequest($matchedRoute, $message)) {
                    return null; // Access Denied. (Replied or Aborted internally)
                }
            }

            // Execute the action with dependency injection or parameters, retrieving the raw output of destianation method.
            $actionResult = $this->callAction($finalHandler, $message, $finalRouteParams);

            if(method_exists($this, 'response')) // if HasWebInterface Trait is Loaded,
                $this->response($actionResult); // Standardize the raw output into a clean HTTP Response object immediately, save it into ?$finalResponse.

            return $actionResult;
        };

        // OPTION A: LARAVEL PIPELINE (The Gold Standard)
        // Used when running inside a Laravel Application (Artisan/Http).
        if (class_exists(Pipeline::class) && function_exists('app')) {
            app(Pipeline::class)
                ->send($this)
                ->through($middlewareStack)
                ->then($destination);
        } 
        // OPTION B: NATIVE ROBUST FALLBACK (The Heavy Lifter)
        // Used for standalone scripts or lightweight setups. 
        // Enhanced to support Aliases, Invokables, and Standard Middleware methods.
        else {
            $pipeline = array_reduce(
                array_reverse($middlewareStack),
                function ($next, $middleware) {
                    return function ($bot) use ($next, $middleware) {
                        
                        // --- 1. Resolve Aliases ---
                        // Check if 'auth' maps to 'App\Middleware\Auth::class'
                        if (is_string($middleware) && property_exists($this, 'middlewareAliases')) {
                            if (isset($this->middlewareAliases[$middleware])) {
                                $middleware = $this->middlewareAliases[$middleware];
                            }
                        }

                        // --- 2. Instantiate & Execute ---
                        
                        // TYPE I: String Class Name
                        if (is_string($middleware) && class_exists($middleware)) {
                            $instance = new $middleware;
                            
                            // Prefer 'handle' method (Laravel Standard)
                            if (method_exists($instance, 'handle')) {
                                return $instance->handle($bot, $next);
                            } 
                            // Fallback to '__invoke' (Modern/Slim Standard)
                            elseif (is_callable($instance)) {
                                return $instance($bot, $next);
                            }
                            
                            // Strict Failure if un-executable class is passed
                            throw new RuntimeException("Middleware [$middleware] is not executable (missing handle/__invoke).");
                        }
                        
                        // TYPE II: Closure Middleware
                        if ($middleware instanceof \Closure) {
                             return $middleware($bot, $next);
                        }
                        
                        // TYPE III: Object Instance
                        if (is_object($middleware)) {
                            if (method_exists($middleware, 'handle')) {
                                return $middleware->handle($bot, $next);
                            } elseif (is_callable($middleware)) {
                                return $middleware($bot, $next);
                            }
                        }

                        // Safety Net: Pass through if middleware is invalid/unrecognized
                        return $next($bot);
                    };
                },
                $destination
            );

            // Ignite the Native Pipeline
            $pipeline($this);
        }

        $this->tunnelAmethyst(); // short syntax for `$this->tunnelAmethyst(null)` ; clears AmethystMatrix working message entry.
    }
}
