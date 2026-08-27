<?php

namespace KrubiK\WebApps\DTOs;
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

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JsonSerializable;

/**
 * HyperDX WebRequest — The Ultimate DTO
 * =================================================================
 * A definitive, immutable, and ultra-performant DTO representing an incoming Web Request.
 * This is the fusion of three powerful design philosophies, engineered for supreme
 * developer experience (HyperDX), zero-overhead performance, and absolute correctness.
 * It serves as a battle-hardened, self-contained bridge carrying HTTP context
 * into the Krubot Message-driven world with maximum speed and developer joy.
 *
 * Design Goals (The Merged Trinity):
 * - Construction requires ONLY an Illuminate\Http\Request (nothing else).
 * - `final readonly class` → engine-level optimizations + true immutability.
 * - Public promoted properties → direct access, zero getter overhead.
 * - Single-pass, allocation-conscious factory that performs all normalizations ONCE.
 * - Powerful `Illuminate\Support\Collection` for Body, Query, and Headers for a celestial DX.
 * - Correct body/query separation (never pollutes body with query params).
 * - Headers normalized to lowercase keys once at construction → O(1) subsequent access.
 * - A rich, yet near-zero cost, convenience API (input, bearerToken, isMutation, etc.).
 * - Native `Arrayable` + `JsonSerializable` for queues, logging, and debugging.
 * - Includes client IP and a reference to the original Request object as an escape hatch.
 * - Robust, multi-header fallback for Telegram InitData extraction.
 * - No external dependencies, no reflection, no magic beyond PHP 8.2.
 * - Trusted-source assumption (as original) → no heavy validation tax.
 *
 * PHP 8.2.30 + Laravel 12.x ready.
 * Perfect for high-throughput Telegram WebApp / Mini App pipelines.
 *
 * @property-read string $path The request's URI path (e.g., "telegram/webapp/entry").
 * @property-read string $method The uppercase HTTP request method (e.g., 'GET', 'POST').
 * @property-read Collection<string, array<int, string>> $headers A collection of all request headers, with lowercase keys.
 * @property-read Collection<string, mixed> $body The decoded request body (JSON or form data).
 * @property-read Collection<string, mixed> $query The URL query parameters.
 * @property-read string $initData Raw Telegram Init Data for validation.
 * @property-read string|null $ip The client's IP address.
 * @property-read Request $original A reference to the original Laravel Request for advanced use cases.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
final readonly class WebRequest implements Arrayable, JsonSerializable
{
    /**
     * The constructor is kept private to enforce creation through the static factory method.
     * This ensures that every instance is built consistently and correctly from a Request object.
     * It immediately converts arrays to powerful Illuminate Collections for a better API.
     *
     * @internal Use ::from() to build an instance.
     *
     * @param string                           $path     Request path (usually without leading slash)
     * @param string                           $method   HTTP method (UPPERCASE)
     * @param Collection<string, array<int, string>> $headers  Normalized (lowercase keys), multi-value
     * @param Collection<string, mixed>             $body     Pure body only (JSON decoded or form data)
     * @param Collection<string, mixed>             $query    Pure query string parameters
     * @param WebAppInitData|null                   $initData The certified WebApp metadata (Utilizing The Soul Stone Genesis).
     * @param string|null                      $ip       The client's IP address.
     * @param Request                          $original The original Laravel Request instance.
     */
    private function __construct(
        public string $path,
        public string $method,
        public Collection $headers,
        public Collection $body,
        public Collection $query,
        public ?WebAppInitData $initData,
        public ?string $ip,
        public Request $original,
    ) {}

    /**
     * THE single, divinely-inspired, hyper-optimized factory.
     * It accepts the Laravel Request and perfectly translates it into this immutable structure.
     * This static factory method encapsulates all the "how-to-build" logic.
     * It is updated to accept an optional, pre-validated WebAppInitData object to prevent redundant crypto parsing.
     * Everything is extracted and normalized in one tight, cache-friendly pass.
     *
     * This is the only entry point. It:
     *  - Normalizes method (uppercase)
     *  - Normalizes header keys (lowercase)
     *  - Prepares body & query correctly
     *  - Extracts Telegram initData in a robust way
     *
     * @param Request $request The incoming Illuminate HTTP Request.
     * @param WebAppInitData|null $preValidatedInitData The pre-resolved genesis block, if available.
     * @return self A new, fully-populated instance of the DTO.
     */
    public static function from(Request $request, ?WebAppInitData $preValidatedInitData = null): self
    {
        // --- PATH ---
        // path() is cheap and gives route path without query string.
        $path = $request->path();

        // --- METHOD NORMALIZATION ---
        // We uppercase once to avoid case issues downstream. Already normalized by Laravel, but we enforce it.
        $method = strtoupper($request->method());

        // --- HEADERS (PERFORMANCE-CRITICAL) ---
        // Normalize keys to lowercase ONCE for O(1) later access.
        // Performance Cost is negligible (cause typical requests has < 20 headers).
        $rawHeaders = $request->headers->all();
        $normalizedHeaders = [];
        foreach ($rawHeaders as $name => $values) {
            $normalizedHeaders[strtolower($name)] = $values;
        }

        // --- BODY (CORRECTNESS-CRITICAL) ---
        // We use a dedicated, private strategy for correct extraction.
        $body = self::extractBody($request);

        // --- INIT DATA EXTRACTION (ROBUST) ---
        //               +++
        // --- ZERO-REDUNDANT-VALIDATION ---
        // Logic is centralized in its own method for clarity and maintainability.
        // But If we already have the validated object from the Identity Card, we use it directly.
        // Otherwise, and only otherwise, we attempt to parse and validate it from scratch.
        $initData = $preValidatedInitData ?? self::generateInitData($request);

        // --- QUERY ---
        // Pure query string parameters from URL.
        $query = $request->query();

        // --- ADDITIONAL CONTEXT ---
        $ip = $request->ip();

        // --- INSTANTIATE DTO ---
        // Finally build the immutable DTO instance. All arrays are elevated to Collections.
        return new self(
            path: $path,
            method: $method,
            headers: new Collection($normalizedHeaders),
            body: new Collection($body),
            query: new Collection($query),
            initData: $initData,
            ip: $ip,
            original: $request,
        );
    }

    /**
     * PERF-CRITICAL: Body extraction strategy.
     * ---------------------------------------
     * - Prefer framework's already-parsed JSON when possible (Laravel caches it).
     * - Never pollute body with query parameters (major correctness + clarity win).
     * - Fall back to raw request bag only (form / multipart / urlencoded).
     * - Empty result always becomes [] (predictable for consumers).
     */
    private static function extractBody(Request $request): array
    {
        // Fast path — JSON is the common case for modern WebApps / APIs
        if ($request->isJson()) {
            $jsonPayload = $request->json()?->all(); // Nullsafe call on json()
            return is_array($jsonPayload) ? $jsonPayload : [];
        }

        // Non-JSON: only the request body bag (never query). This is crucial.
        return $request->request->all();
    }

    /**
     * Telegram WebApp / MiniApp InitData generation with pragmatic fallbacks.
     * Fallback extractor when no pre-validated Genesis block is provided.
     * Logic is centralized here to keep the factory clean.
     */
    private static function generateInitData(Request $request): ?WebAppInitData
    {
        // We fetch the raw initData from the request, as the DTO requires it.
        // Order is intentional (most common -> least common)
        $rawInitData = $request->header('X-WebApp-Init-Data') ??
            $request->header('X-Telegram-Init-Data') ??
            $request->header('X-Telegram-WebApp-Init-Data') ??
            $request->input('_auth') ??
        '';

        if (!is_string($rawInitData) || $rawInitData === '') {
            return null;
        }

        // PAYLOAD INSIGHT: Leverages Nemesis's deep webhook payload signatures (Telegram, Rubika, Bale, Web)
        // WebRequest just asks Him 'Who is active'? instead of re-implement 'How to find who is active'.
        if ($platform = app('nemesis')->platform()) {
            return WebAppInitData::from($rawInitData, $platform);
        }

        return null;
    }

    // =========================================================================
    // HyperDX Convenience API — Rich, Deliberately Tiny & Near-Zero Cost
    // Prefer direct property access ($dto->body->get('x')) in hottest paths.
    // These helpers exist for readability and common patterns only.
    // =========================================================================

    /**
     * Retrieves a value from the body or query parameters, with body taking precedence.
     * This is the equivalent of Laravel's $request->input().
     * It supports dot notation for accessing nested data thanks to Collections.
     *
     * @param string|null $key The key to retrieve. e.g., 'user.id'
     * @param mixed $default A default value to return if the key is not found.
     * @return mixed
     */
    public function input(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            // Return merged body and query data if no key is specified.
            return $this->body->merge($this->query)->all();
        }
        return $this->body->get($key, $this->query->get($key, $default));
    }
    
    /**
     * Checks if an input key exists in the body or query parameters.
     */
    public function has(string $key): bool
    {
        return $this->body->has($key) || $this->query->has($key);
    }

    /**
     * Retrieves a header from the request. O(1) access thanks to lowercase normalization at construction.
     *
     * @param string $key The header key (case-insensitive).
     * @param string|null $default The default value.
     * @return string|null The first value for the header.
     */
    public function header(string $key, ?string $default = null): ?string
    {
        // Headers from Laravel's request->headers->all() are arrays of strings.
        // We'll return the first value, which is the most common use case.
        $header = $this->headers->get(strtolower($key));
        return $header[0] ?? $default;
    }
    
    /**
     * Retrieves all values for a header (multi-value safe).
     *
     * @return array<int, string>
     */
    public function headerValues(string $name): array
    {
        return $this->headers->get(strtolower($name), []);
    }

    /**
     * Gets the Bearer token from the Authorization header.
     * This is a prime example of encapsulating logic within the DTO.
     *
     * @return string|null The token, or null if not present.
     */
    public function bearerToken(): ?string
    {
        $header = $this->header('Authorization', '');
        return Str::startsWith($header, 'Bearer ') ? Str::substr($header, 7) : null;
    }

    /**
     * Checks if the request method is of a certain type. Case-insensitive.
     *
     * @param string $method The method to check against (e.g., 'POST').
     * @return bool
     */
    public function isMethod(string $method): bool
    {
        return $this->method === strtoupper($method);
    }
    
    public function isGet(): bool { return $this->method === 'GET'; }
    public function isPost(): bool { return $this->method === 'POST'; }

    /**
     * Checks if request is read-only (e.g., GET, HEAD).
     */
    public function isReadOnly(): bool
    {
        return in_array($this->method, ['GET', 'HEAD'], true);
    }

    /**
     * Checks if request is a POST-like mutation (e.g., POST, PUT, PATCH, DELETE).
     */
    public function isMutation(): bool
    {
        return in_array($this->method, ['POST', 'PUT', 'PATCH', 'DELETE'], true);
    }

    /**
     * Immutable "with" method for the rare case you need a slightly altered copy.
     * Still cheap because the DTO is tiny and properties are collections/primitives.
     *
     * @param array{
     *     path?: string,
     *     method?: string,
     *     headers?: Collection|array,
     *     body?: Collection|array,
     *     query?: Collection|array,
     *     initData?: string,
     *     ip?: string,
     *     original?: Request
     * } $overrides
     * @return self A new, altered instance.
     */
    public function with(array $overrides): self
    {
        $get = fn($key, $default) => $overrides[$key] ?? $default;

        return new self(
            path: $get('path', $this->path),
            method: $get('method', $this->method),
            headers: is_array($h = $get('headers', $this->headers)) ? new Collection($h) : $h,
            body: is_array($b = $get('body', $this->body)) ? new Collection($b) : $b,
            query: is_array($q = $get('query', $this->query)) ? new Collection($q) : $q,
            initData: $get('initData', $this->initData),
            ip: $get('ip', $this->ip),
            original: $get('original', $this->original)
        );
    }

    /**
     * Converts the DTO to a compact array for logging / debugging.
     * NOTE: The original Request object is excluded to avoid huge, circular dumps.
     *
     * @return array{
     *     path: string,
     *     method: string,
     *     ip: string|null,
     *     headers: array,
     *     body: array,
     *     query: array,
     *     initData: string
     * }
     */
    public function toArray(): array
    {
        return [
            'path'     => $this->path,
            'method'   => $this->method,
            'ip'       => $this->ip,
            'headers'  => $this->headers->all(),
            'body'     => $this->body->all(),
            'query'    => $this->query->all(),
            'initData' => $this->initData?->toArray(),
        ];
    }

    /**
     * Get the JSON-serializable representation of the object.
     * Useful for HyperDX-style observability/logging and API responses.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
