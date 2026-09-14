<?php

declare(strict_types=1);

namespace KrubiK\Drivers;

/*
|--------------------------------------------------------------------------
| Krubot WebAppDriver — Quantum Fusion 🌐⚡️
|--------------------------------------------------------------------------
| MERGE Powers of:
|   - v1   : makeRequest pipeline, NeonVitality queue, keyboard
|            normalizer, HMAC validation, response serializer
|   - v2   : handleWebUpdate(), findRouteEntry() with regex,
|           static $webRegistry, castParameter(), formatResponse()
|   - v3 ✨: AxiomCore integration — driver now asks the certified
|           Identity Orchestrator "who are you?" instead of doing
|           ad-hoc HMAC parsing inline. UniversalIdentity flows
|           through $this->identity and is injectable into Nexus
|           handlers via buildMethodArguments(). WebAppInitData DTO
|           is also directly injectable. resolveSenderUser() now
|           reads from the certified UniversalIdentity.
|
| Best of all worlds. No logic dropped. Zero compromise. Full power.
|--------------------------------------------------------------------------
*/

use KrubiK\Krubot;
use KrubiK\Drivers\Contracts\MultiverseEnforcer;
use KrubiK\Drivers\Arcane\NeonVitality;
use KrubiK\WebApps\DTOs\WebAppInitData;
use KrubiK\WebApps\UniversalIdentity;
use KrubiK\WebApps\AxiomCore;
use KrubiK\Render\RenderAura;

use KrubiK\Keyboard\Keyboard as KrubiKInlineKeyboard;
use KrubiK\Keyboard\ReplyKeyboard as KrubiKReplyKeyboard;
use KrubiK\Render\RichMan;
use KrubiK\Enums\Platform;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;

final class WebAppDriver implements MultiverseEnforcer
{
    // =========================================================================
    // 💉 NeonVitality: Context API + HasDriverIdentity (alias, getName, setName…)
    // =========================================================================
    use NeonVitality;

    // =========================================================================
    // STATE
    // =========================================================================

    protected array $config;

    /** Parsed incoming HTTP payload (JSON body ∪ POST ∪ GET). */
    protected array $payload = [];

    /** Bot messages queued by $bot->reply()->send() calls inside handlers. */
    protected array $responseQueue = [];

    /** HTTP status code to emit. */
    protected int $httpStatusCode = 200;

    /** Synthetic "bot" identity from config. */
    protected array $botUser = [];

    /** Resolved caller identity (initData / session / auth / anonymous). */
    protected array $senderUser = [];

    /** Synthetic chat context. */
    protected array $chatContext = [];

    /** The active Laravel Request (set by handleWebUpdate). */
    protected Request $currentRequest;

    /** URI params extracted from {param} segments (set by findRouteEntry). */
    protected array $routeParameters = [];

    /** The route key that was matched (for debugging / logging). */
    protected ?string $matchedRouteKey = null;
    
    // =========================================================================
    // 🆔 UNIVERSAL IDENTITY — the certified, AxiomCore-resolved identity
    // =========================================================================

    /**
     * The single source of truth for "who is calling this endpoint".
     *
     * Resolved by AxiomCore::inspect() during handleWebUpdate().
     * In pre-request contexts (middleware, constructor injection) it falls
     * back to a guest identity so the driver is never in an undefined state.
     *
     * Nexus handlers can receive it by type-hinting:
     *   public function myAction(UniversalIdentity $identity): array { … }
     *
     * Or reach the raw WebAppInitData proof object via:
     *   public function myAction(WebAppInitData $initData): array { … }
    */
    protected UniversalIdentity $identity;

    // =========================================================================
    // 📋 STATIC WEB REGISTRY
    // =========================================================================

    /**
     * Registry of all discovered Web* attributes, populated by
     * Krubot::discoverAndIntegrateNexuses() at boot time.
     *
     * Shape of each entry:
     * [
     *   'route'     => 'game.dashboard.order_vip_product',   // dot-path key
     *   'class'     => App\Nexus\GamePanelNexus::class,
     *   'method'    => 'orderVipProduct',
     *   'attribute' => 'WebAction',                          // 'WebApp'|'WebPage'|'WebAction'
     *   'http'      => ['POST'],                             // empty = all methods
     *   'restrict'  => ['*'],                                // from #[RestrictTo]
     * ]
    */
    protected static array $webRegistry = [];

    /**
     * Called once by Krubot at boot (discoverAndIntegrateNexuses).
     * Drop-in — no changes needed to existing discovery code beyond calling this.
    */
    public static function setWebRegistry(array $registry): void
    {
        static::$webRegistry = $registry;
    }

    /**
     * Append a single entry (useful when hot-discovering new Nexuses at runtime).
    */
    public static function registerWebRoute(array $entry): void
    {
        static::$webRegistry[] = $entry;
    }

    /**
     * Expose the registry (for testing / krubik:list-nexuses).
    */
    public static function getWebRegistry(): array
    {
        return static::$webRegistry;
    }

    // =========================================================================
    // 🔌 BOOT
    // =========================================================================

    public function __construct(array $config)
    {
        $this->config       = $config;
        $this->assignCodeName($config['driver_alias'] ?? 'web');

        $this->botUser = [
            'id'         => 0,
            'is_bot'     => true,
            'first_name' => $config['bot_name']     ?? 'KrubotWebApp',
            'username'   => $config['bot_username'] ?? 'krubot_web',
        ];

        // ── Identity: start as guest, AxiomCore will certify in handleWebUpdate ──
        // We do NOT call AxiomCore here because a proper Laravel Request object
        // is not yet available. The guest state keeps the driver usable everywhere.
        $this->identity    = UniversalIdentity::guest('web');

        // Payload is hydrated here so Enforcer::param() works even before
        // handleWebUpdate() is called (e.g. middlewares or constructor injections).
        $this->payload     = $this->resolveIncomingPayload();
        $this->senderUser  = $this->resolveSenderUser(); /// -> $this->buildSenderArrayFromIdentity($this->identity);
        $this->chatContext = $this->resolveChatContext();

        $this->igniteNeon($this->config);
    }

    /**
     * =========================================================================
     * 🎯 PRIMARY ENTRY POINT — called by QuantumGatewayController
     * =========================================================================
     *
     * Flow:
     *   1. Bind the active Request so param resolution has it
     *   2. Normalise the URI path to a dot-route key
     *   3. Walk $webRegistry to find the matching entry (with {param} support)
     *   4. Guard HTTP method (POST-only actions etc.)
     *   5. Check #[RestrictTo] restrictions
     *   6. Build handler arguments via reflection (DI-style)
     *   7. Invoke the Nexus handler
     *   8. Serialize the return value to the correct HTTP response type
     *
     * @param  Request     $request   Laravel's current request
     * @param  string      $routePath The {path} capture from routes/web.php
     * @return mixed                  A Symfony/Laravel Response
    */
    public function handleWebUpdate(Request $request, string $routePath = ''): mixed
    {
        $this->currentRequest = $request;

        // Re-hydrate payload from the actual Laravel Request (more reliable than php://input)
        $this->payload = $this->extractPayload($request);

        // ── ✨ Certify Identity via AxiomCore ──────────────────────────
        // If the middleware (AuthenticateWebApp) already ran, its result lives in
        // the request attribute bag. We honour that to avoid double-validation.
        // If not (e.g. the driver is used without the middleware), we certify now.
        $this->identity = $this->resolveIdentityFromRequest($request);

        // Rebuild senderUser from the certified identity
        $this->senderUser  = $this->buildSenderArrayFromIdentity($this->identity);
        $this->chatContext = $this->resolveChatContext();

        // Normalise path: '/webapps/game/dashboard/order_vip_product' → 'game.dashboard.order_vip_product'
        $dotPath = $this->uriToDotPath($routePath ?: $request->path());

        // ── 1. Match route ────────────────────────────────────────────────
        $entry = $this->findRouteEntry($dotPath);

        if ($entry === null) {
            return $this->notFoundResponse($dotPath);
        }

        $this->matchedRouteKey  = $entry['route'];
        $this->routeParameters  = $entry['_params'] ?? [];

        // ── 2. HTTP method guard ──────────────────────────────────────────
        $allowedMethods = array_map('strtoupper', $entry['http'] ?? []);
        if (!empty($allowedMethods) && !in_array(strtoupper($request->method()), $allowedMethods, true)) {
            return response()->json([
                'ok'    => false,
                'error' => "Method [{$request->method()}] not allowed. Allowed: " . implode(', ', $allowedMethods),
            ], 405)->header('X-Krubot-Driver', 'web');
        }

        // ── 3. #[RestrictTo] guard ────────────────────────────────────────
        if (!$this->passesRestriction($entry['restrict'] ?? [])) {
            return response()->json(['ok' => false, 'error' => 'Access denied.'], 403)
                ->header('X-Krubot-Driver', 'web');
        }

        // ── 4. Invoke handler ─────────────────────────────────────────────
        try {
            $nexusInstance = app($entry['class']);
            $args          = $this->buildMethodArguments($entry['class'], $entry['method']);
            $result        = $nexusInstance->{$entry['method']}(...$args);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }

        // ── 5. Serialize ──────────────────────────────────────────────────
        return $this->formatResponse($result, $entry['attribute']);
    }

    // =========================================================================
    // 🔍 ROUTE MATCHING — DeepSeek's pattern, hardened
    // =========================================================================

    /**
     * Find the registry entry whose 'route' dot-key matches $dotPath.
     * Supports {param} wildcards and both exact and normalised (kebab→snake) paths.
     *
     * Returns the entry array with an extra '_params' key, or null on miss.
    */
    protected function findRouteEntry(string $dotPath): ?array
    {
        // Also try with hyphens normalised to underscores
        $dotPathAlt = str_replace('-', '_', $dotPath);

        foreach (static::$webRegistry as $entry) {
            $routeKey = $entry['route'] ?? '';
            if (!$routeKey) {
                continue;
            }

            $regex = $this->routeKeyToRegex($routeKey);

            foreach ([$dotPath, $dotPathAlt] as $candidate) {
                if (preg_match($regex, $candidate, $matches)) {
                    // Extract named {param} captures
                    $namedParams = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    $entry['_params'] = $namedParams;
                    return $entry;
                }
            }
        }

        return null;
    }

    /**
     * Convert a dot-path route key with optional {param} segments into a PCRE
     * named-capture regex.
     *
     * 'game.dashboard.show_vip_product.{productId}'
     *   → /^game\.dashboard\.show_vip_product\.(?P<productId>[^.\/]+)$/i
    */
    protected function routeKeyToRegex(string $routeKey): string
    {
        // Temporarily replace {param} so preg_quote doesn't escape the braces
        $tmp = preg_replace_callback('/\{(\w+)\}/', fn($m) => "\x00{$m[1]}\x00", $routeKey);
        $escaped = preg_quote($tmp, '/');

        // Restore as named captures
        $regex = preg_replace_callback(
            '/\x00(\w+)\x00/',
            fn($m) => '(?P<' . $m[1] . '>[^.\/]+)',
            $escaped
        );

        return '/^' . $regex . '$/i';
    }

    // =========================================================================
    // 🏗️ ARGUMENT BUILDER — Claude's reflection DI, DeepSeek's castParameter, now with GroK identity-injectable
    // =========================================================================

    /**
     * Build the exact argument list for the Nexus handler method.
     *
     * Resolution priority (mirrors how #[WebAction] auto-injects):
     *   1. MultiverseEnforcer / WebAppDriver type → $this (the active driver)
     *   2. Illuminate\Http\Request   → $this->currentRequest
     *   3. UniversalIdentity      ✨ → $this->identity (certified)
     *   4. WebAppInitData         ✨ → $this->identity->getData()
     *   5. Route params {productId}  → $this->routeParameters
     *   6. Request payload (body)    → $this->payload
     *   7. Default value             → $param->getDefaultValue()
     *   8. Nullable                  → null
     *   9. Laravel IoC Registry      → app($typeName)
    */
    protected function buildMethodArguments(string $class, string $method): array
    {
        $refMethod = new \ReflectionMethod($class, $method);
        $args      = [];

        foreach ($refMethod->getParameters() as $param) {
            $name     = $param->getName();
            $type     = $param->getType();
            $typeName = $type instanceof \ReflectionNamedType ? $type->getName() : null;

            // 1. Krubot / WebAppDriver injection
            if ($typeName && (
                $typeName === self::class ||
                is_a($typeName, MultiverseEnforcer::class, true)
            )) {
                $args[] = $this;
                continue;
            }

            if ($typeName && is_a($typeName, Krubot::class, true)) {
                $args[] = $this->warlord();
                continue;
            }

            // 2. Laravel Request injection
            if ($typeName && is_a($typeName, Request::class, true)) {
                $args[] = $this->currentRequest;
                continue;
            }            

            // 3. ✨ UniversalIdentity injection — certified identity object
            if ($typeName === UniversalIdentity::class) {
                $args[] = $this->identity;
                continue;
            }

            // 4. ✨ WebAppInitData injection — the raw proof DTO (nullable-safe)
            if ($typeName === WebAppInitData::class) {
                $initData = $this->identity->getData();

                if ($initData === null && !$param->allowsNull()) {
                    // Handler demands non-null InitData but identity has none.
                    // Return 403 by throwing — formatResponse / errorResponse handles it.
                    throw new \RuntimeException(
                        "WebAppDriver: Handler {$class}::{$method}() requires WebAppInitData " .
                        "but the current identity was not forged from a MiniApp context. " .
                        "Ensure AuthenticateWebApp middleware runs before this route, or use " .
                        "?WebAppInitData to accept null."
                    );
                }

                $args[] = $initData;
                continue;
            }

            // 5. Route {param} segments
            if (isset($this->routeParameters[$name])) {
                $args[] = $this->castParameter($this->routeParameters[$name], $type);
                continue;
            }

            // 6. Request payload (POST body / JSON)
            if (array_key_exists($name, $this->payload)) {
                $args[] = $this->castParameter($this->payload[$name], $type);
                continue;
            }

            // 7. Default value
            if ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
                continue;
            }

            // 8. Nullable
            if ($param->allowsNull()) {
                $args[] = null;
                continue;
            }

            // 9. Query in Laravel IoC (services, repositories, etc.)
            if ($typeName && (class_exists($typeName) || interface_exists($typeName))) {
                try {
                    $args[] = app($typeName);
                    continue;
                } catch (\Throwable) {
                    // fall through to exception
                }
            }

            throw new \RuntimeException(
                "WebAppDriver: Cannot resolve parameter \${$name} ({$typeName}) " .
                "for {$class}::{$method}(). " .
                "Not in route params, payload, or IoC container."
            );
        }

        return $args;
    }

    /**
     * Cast a raw scalar value to the expected PHP built-in type.
     * Handles nullable types correctly.
    */
    protected function castParameter(mixed $value, ?\ReflectionType $type): mixed
    {
        if ($value === null) {
            return null;
        }

        $typeName = $type instanceof \ReflectionNamedType ? $type->getName() : null;

        return match ($typeName) {
            'int'    => (int)    $value,
            'float'  => (float)  $value,
            'bool'   => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'string' => (string) $value,
            'array'  => is_array($value)
                            ? $value
                            : (json_decode((string) $value, true) ?? [(string) $value]),
            default  => $value,
        };
    }

    // =========================================================================
    // ⚡️ makeRequest — $bot->reply()->send() pipeline
    // =========================================================================

    /**
     * The universal dispatcher — mirrors BaleDriver's contract exactly.
     *
     * In bot drivers → fires HTTP to Telegram/Bale.
     * Here          → queues the message; flushed into the final response.
     *
     * Called transparently by NeonVitality when your handler does:
     *   $bot->reply("Hello!")->send();
    */
    public function makeRequest(string $method, array $params = []): array
    {
        $finalParams = $params;

        // ── RichMan → HTML (Web loves HTML) ──────────────────────────────
        if (isset($finalParams['text']) && $finalParams['text'] instanceof RichMan) {
            $this->warlord()->listensAura(true);
            RenderAura::infuse(Platform::Web());
            $finalParams['text']       = $finalParams['text']->toHtml();
            $finalParams['parse_mode'] = 'html';
            $finalParams['_rich']      = true;
            unset($finalParams['isRich'], $finalParams['rich_blocks']);
        } elseif (!empty($finalParams['isRich'])) {
            if (!empty($finalParams['rich_blocks'])) {
                $finalParams['text'] = $this->convertRichBlocksToHtml($finalParams['rich_blocks']);
            }
            $finalParams['text'] = $finalParams['text'] ?? '';
            unset($finalParams['isRich'], $finalParams['isRtl'], $finalParams['rich_blocks']);
        }

        // ── Normalise keyboard / keypad ───────────────────────────────────
        $normalizedParams = $this->normalizePayload($finalParams);

        // ── Enqueue ───────────────────────────────────────────────────────
        $this->responseQueue[] = [
            'method'    => $method,
            'params'    => $normalizedParams,
            'chat_id'   => $normalizedParams['chat_id'] ?? ($this->chatContext['id'] ?? 'web'),
            'timestamp' => microtime(true),
        ];

        return [
            'ok'     => true,
            'result' => array_merge(['message_id' => $this->pseudoId()], $normalizedParams),
        ];
    }

    // =========================================================================
    // 📤 RESPONSE FORMATTER — DeepSeek's attribute awareness + Claude's types
    // =========================================================================

    /**
     * Convert the handler's return value to the correct HTTP response.
     *
     * Per Krubot docs (from your code comments):
     *   array / Arrayable / Jsonable  → JSON   (WebAction default)
     *   Illuminate\View\View          → HTML render
     *   Htmlable (RichMan, Article)   → HTML string
     *   Symfony/Laravel Response      → pass through
     *   string                        → HTML
     *   null / void                   → flush $bot->reply() queue as JSON
    */
    protected function formatResponse(mixed $result, string $attributeType): mixed
    {
        // 1. Already a Symfony/Laravel Response → pass through + attach bot messages
        if ($result instanceof \Symfony\Component\HttpFoundation\Response) {
            if (!empty($this->responseQueue)) {
                $result->headers->set('X-Krubot-Bot-Messages', (string) count($this->responseQueue));
            }
            return $result;
        }

        // 2. Illuminate View → render to HTML
        if ($result instanceof View) {
            return response($result->render(), $this->httpStatusCode)
                ->header('Content-Type', 'text/html; charset=utf-8')
                ->header('X-Krubot-Driver', 'web');
        }

        // 3. Htmlable (RichMan, Article, etc.)
        if ($result instanceof \Illuminate\Contracts\Support\Htmlable) {
            return response($result->toHtml(), $this->httpStatusCode)
                ->header('Content-Type', 'text/html; charset=utf-8')
                ->header('X-Krubot-Driver', 'web');
        }

        // 4. Array / Arrayable / JsonSerializable → JSON
        //    (this is what #[WebAction] handlers almost always return)
        if (
            is_array($result) ||
            $result instanceof \Illuminate\Contracts\Support\Arrayable ||
            $result instanceof \JsonSerializable ||
            $result instanceof \Illuminate\Contracts\Support\Jsonable
        ) {
            $data = match (true) {
                is_array($result)                                       => $result,
                $result instanceof \Illuminate\Contracts\Support\Jsonable => json_decode($result->toJson(), true),
                default                                                  => $result->toArray(),
            };

            // Attach any queued $bot->reply() messages as a sidecar key
            if (!empty($this->responseQueue)) {
                $data['_bot_messages'] = $this->buildBotMessageList();
            }

            return response()->json($data, $this->httpStatusCode)
                ->header('X-Krubot-Driver', 'web');
        }

        // 5. Plain string → HTML (WebApp / WebPage index methods)
        if (is_string($result)) {
            return response($result, $this->httpStatusCode)
                ->header('Content-Type', 'text/html; charset=utf-8')
                ->header('X-Krubot-Driver', 'web');
        }

        // 6. null / void (handler only used $bot->reply()->send())
        //    → flush the bot reply queue as JSON
        return response()->json(
            $this->buildQueuedResponsePayload(),
            $this->httpStatusCode
        )->header('X-Krubot-Driver', 'web');
    }

    /**
     * Public flush (for controllers that want manual control or non-Laravel envs).
    */
    public function flushResponse(bool $emit = true): array
    {
        $payload = $this->buildQueuedResponsePayload();

        if ($emit) {
            if (!headers_sent()) {
                http_response_code($this->httpStatusCode);
                header('Content-Type: application/json; charset=utf-8');
                header('X-Krubot-Driver: web');
            }
            echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }

        return $payload;
    }

    public function buildQueuedResponsePayload(): array
    {
        $messages = $this->buildBotMessageList();

        if (count($messages) === 1) {
            return array_merge(['ok' => true], $messages[0]);
        }

        return ['ok' => true, 'messages' => $messages, 'count' => count($messages)];
    }

    public function buildBotMessageList(): array
    {
        return array_map(fn($entry) => [
            'method'   => $entry['method'],
            'chat_id'  => $entry['chat_id'],
            'text'     => $entry['params']['text']         ?? null,
            'keyboard' => $entry['params']['reply_markup'] ?? null,
            'params'   => $entry['params'],
        ], $this->responseQueue);
    }

    // =========================================================================
    // 🧠 PAYLOAD NORMALIZER (identical contract to BaleDriver)
    // =========================================================================

    protected function normalizePayload(array $params): array
    {
        if (isset($params['keypad'])) {
            $params['reply_markup'] = $params['keypad'];
            unset($params['keypad']);
        }

        if (isset($params['reply_markup'])) {
            $markup = $params['reply_markup'];

            if ($markup instanceof KrubiKInlineKeyboard) {
                $params['reply_markup'] = $this->transformInlineKeyboard($markup);
            } elseif ($markup instanceof KrubiKReplyKeyboard) {
                $params['reply_markup'] = $markup->toArray();
            } elseif (is_string($markup)) {
                $decoded = json_decode($markup, true);
                $params['reply_markup'] = $decoded ?: $markup;
            }
            // Already array → pass through
        }

        return $params;
    }

    protected function transformInlineKeyboard(KrubiKInlineKeyboard $keyboard): array
    {
        $data    = $keyboard->toArray();
        $rows    = $data['rows'] ?? [];
        $webRows = [];

        foreach ($rows as $row) {
            $buttons = $row['buttons'] ?? $row;
            $webRow  = [];

            foreach ($buttons as $btn) {
                $webBtn = [
                    'text' => $btn['text'],
                    'col'  => $btn['col'] ?? 6,
                ];

                if (!empty($btn['url']) || (($btn['type'] ?? '') === 'Link')) {
                    $webBtn['type'] = 'url';
                    $webBtn['url']  = $btn['url'] ?? ($btn['link_data']['url'] ?? '#');
                } elseif (!empty($btn['web_app'])) {
                    $webBtn['type']    = 'web_app';
                    $webBtn['web_app'] = $btn['web_app'];
                } elseif (!empty($btn['request_location'])) {
                    $webBtn['type'] = 'request_location';
                } elseif (!empty($btn['action_id'])) {
                    $webBtn['type']        = 'callback';
                    $webBtn['action_id']   = $btn['action_id'];
                    $webBtn['action_data'] = $btn['action_data'] ?? [];
                } else {
                    $webBtn['type']      = 'callback';
                    $webBtn['action_id'] = 'NO_ACTION';
                }

                $webRow[] = $webBtn;
            }

            $webRows[] = $webRow;
        }

        return ['inline_keyboard' => $webRows];
    }

    // =========================================================================
    // 📥 PAYLOAD RESOLVER
    // =========================================================================

    /**
     * Parse the incoming HTTP payload.
     * Priority: JSON body > multipart/form-data > query string.
    */
    protected function resolveIncomingPayload(): array
    {
        $raw = file_get_contents('php://input');

        if (!empty($raw)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return array_merge($_GET, $_POST, $decoded);
            }
        }

        return array_merge($_GET, $_POST);
    }

    /**
     * Re-parse from the actual Laravel Request (called inside handleWebUpdate).
     * More reliable than php://input for multipart or already-consumed streams.
    */
    protected function extractPayload(Request $request): array
    {
        if ($request->isJson()) {
            return array_merge($request->query->all(), $request->json()->all());
        }

        return array_merge($request->query->all(), $request->post());
    }

    /**
     * Strip /webapps/ prefix and convert URI slashes to dots.
     * '/webapps/game/dashboard/order_vip_product' → 'game.dashboard.order_vip_product'
    */
    protected function uriToDotPath(string $uri): string
    {
        $uri = ltrim($uri, '/');

        // Strip 'webapps/' prefix if present
        if (str_starts_with($uri, 'webapps/')) {
            $uri = substr($uri, strlen('webapps/'));
        }

        return str_replace('/', '.', $uri);
    }

    // =========================================================================
    // 👤 IDENTITY RESOLVERS — now delegating to UniversalIdentity
    // =========================================================================

    /**
     * @deprecated  Use $this->identity directly.
     *              Kept for backward compatibility with NeonVitality trait internals.
    */
    protected function resolveSenderUser(): array
    {
        return $this->buildSenderArrayFromIdentity($this->identity);
    }

    protected function resolveChatContext(): array
    {
        return [
            'id'    => $this->senderUser['id'] ?? $this->deriveAnonymousId(),
            'type'  => 'web',
            'title' => 'WebApp Session',
        ];
    }

    protected function deriveAnonymousId(): int
    {
        $fingerprint = ($_SERVER['REMOTE_ADDR'] ?? '') . ($_SERVER['HTTP_USER_AGENT'] ?? '');
        return abs(crc32($fingerprint));
    }

    // =========================================================================
    // 🔒 RESTRICTION CHECK — now identity-aware
    // =========================================================================

    /**
     * Evaluate #[RestrictTo] rules against the certified UniversalIdentity.
     *
     * '*' → any authenticated (non-guest) identity
     * 'telegram' / 'bale' / 'web' → platform-specific check
     * '@admin' style tags can be expanded here as needed
    */
    protected function passesRestriction(array $restrictions): bool
    {
        if (empty($restrictions)) {
            return true;
        }

        foreach ($restrictions as $r) {
            if ($r === '*') {
                return true; // Or:: $this->identity->isAuthenticated if you want to allow only authenticated users
            }

            // Platform-based restriction: 'telegram', 'bale', 'web', etc.
            if ($this->identity->platform === $r) {
                return true;
            }

            // Source-based restriction: 'webapp_init_data', 'web_session', etc.
            if ($this->identity->source === $r) {
                return true;
            }
        }

        return false;
    }

    // =========================================================================
    // 🆔 IDENTITY RESOLUTION — AxiomCore integration
    // =========================================================================

    /**
     * Resolve the UniversalIdentity for the current request.
     *
     * Priority:
     * 1. Already set by AuthenticateWebApp middleware → read from request attribute bag.
     * 2. Not set → ask AxiomCore to inspect the request now (late resolution).
     *
     * This means the driver works correctly whether the middleware ran or not.
    */
    protected function resolveIdentityFromRequest(Request $request): UniversalIdentity
    {
        // Check if AuthenticateWebApp middleware already resolved and attached identity
        $cached = $request->identityCard();  // macro from KrubotServiceProvider (getter mode)

        if ($cached instanceof UniversalIdentity) {
            return $cached;
        }

        // Late resolution — driver resolves identity itself via AxiomCore
        /** @var AxiomCore $axiom */
        $axiom = app(AxiomCore::class);
        $identity = $axiom->inspect($request);

        // Attach to request so downstream code (e.g. other middleware) can read it
        $request->identityCard($identity);

        return $identity;
    }

    /**
     * Build the legacy $senderUser array from the certified UniversalIdentity.
     *
     * This bridges the new identity system with the existing NeonVitality
     * trait and any code that calls $this->getUser() / $bot->user().
    */
    protected function buildSenderArrayFromIdentity(UniversalIdentity $identity): array
    {
        if ($identity->isGuest) {
            return [
                'id'         => $this->deriveAnonymousId(),
                'is_bot'     => false,
                'first_name' => 'WebVisitor',
                'username'   => null,
                'platform'   => $identity->platform ?? 'web',
            ];
        }

        // WebApp / MiniApp path — rich data from WebAppInitData DTO
        if ($identity->isFromWebApp() && ($initData = $identity->getData()) !== null) {
            return [
                'id'            => $initData->getUserId(),
                'is_bot'        => false,
                'first_name'    => $initData->getFirstName(),
                'last_name'     => $initData->getLastName(),
                'username'      => $initData->getUsername(),
                'language_code' => $initData->getLanguageCode(),
                'platform'      => $identity->platform,
                '_source'       => UniversalIdentity::SRC_WEBAPP_INIT_DATA,
            ];
        }

        // Standard web session (Eloquent user) or API token
        $userId = $identity->id();
        return [
            'id'         => $userId,
            'is_bot'     => false,
            'first_name' => $identity->name ?? $identity->first_name ?? 'User',
            'last_name'  => $identity->last_name ?? null,
            'username'   => $identity->email ?? $identity->username ?? null,
            'platform'   => $identity->platform ?? 'web',
            '_source'    => $identity->source,
        ];
    }

    // =========================================================================
    // 🆔 IDENTITY ACCESSORS — expose the power to Nexuses & NeonVitality
    // =========================================================================

    /**
     * The certified identity of the caller.
     * Nexuses that need the full object should type-hint UniversalIdentity instead.
    */
    public function getIdentity(): UniversalIdentity
    {
        return $this->identity;
    }

    /**
     * The WebAppInitData proof DTO, or null if the request is not from a MiniApp.
     * Nexuses that need the full object should type-hint WebAppInitData instead.
    */
    public function getInitData(): ?WebAppInitData
    {
        return $this->identity->getData();
    }

    /**
     * Convenience: is the current caller coming from a verified MiniApp?
    */
    public function isFromWebApp(): bool
    {
        return $this->identity->isFromWebApp();
    }

    /**
     * Convenience: is the current caller a guest (unauthenticated)?
    */
    public function isGuest(): bool
    {
        return $this->identity->isGuest;
    }

    // =========================================================================
    // 🪧 STANDARD INTERFACE STUBS (BotDriverInterface / StandardDriverInterface)
    // =========================================================================

    public function getMe(): array
    {
        return $this->botUser;
    }

    public function getUser(): array
    {
        return $this->senderUser;
    }

    public function getChat(): array
    {
        return $this->chatContext;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function param(string $key, mixed $default = null): mixed
    {
        return $this->payload[$key] ?? $default;
    }

    public function setStatus(int $code): static
    {
        $this->httpStatusCode = $code;
        return $this;
    }

    // Webhook stubs (no-ops in web context)
    public function deleteWebhook(): bool     { return true; }
    public function getWebhookInfo(): array   { return ['url' => '', 'has_custom_certificate' => false, 'pending_update_count' => 0]; }
    public function getWebhookUpdate(): array { return $this->payload; }

    public function sendMessage(array $params): array
    {
        //$this->warlord()->setCurrentDriver($this->getCodeName());
        $this->warlord()->enforcer($this);
        return $this->makeRequest('sendMessage', $params);
    }

    // =========================================================================
    // 🛠 HELPERS
    // =========================================================================

    // @Todo: Connect RichMan
    protected function convertRichBlocksToHtml(array $blocks): string
    {

        // return RichMan::summon()->import($blocks)->toHtml();
        
        $html = '';
        foreach ($blocks as $block) {
            $text = htmlspecialchars($block['text'] ?? '', ENT_QUOTES);
            $html .= match ($block['type'] ?? 'text') {
                'bold'      => "<strong>{$text}</strong><br>",
                'italic'    => "<em>{$text}</em><br>",
                'code'      => "<code>{$text}</code><br>",
                'pre'       => "<pre>{$text}</pre>",
                'separator' => "<hr>",
                default     => "<span>{$text}</span><br>",
            };
        }
        return $html;
    }

    protected function notFoundResponse(string $dotPath): JsonResponse
    {
        return response()->json([
            'ok'      => false,
            'error'   => "No WebRoute matched [{$dotPath}].",
            'hint'    => 'Check that your Nexus is discovered and #[WebApp|WebPage|WebAction] URI is correct.',
            'path'    => $dotPath,
            'registry'=> array_column(static::$webRegistry, 'route'),
        ], 404)->header('X-Krubot-Driver', 'web');
    }

    protected function errorResponse(\Throwable $e): JsonResponse
    {
        return response()->json([
            'ok'    => false,
            'error' => $e->getMessage(),
            'trace' => config('app.debug') ? $e->getTraceAsString() : null,
        ], 500)->header('X-Krubot-Driver', 'web');
    }

    protected function pseudoId(): int
    {
        static $counter = 0;
        return ++$counter;
    }
}
