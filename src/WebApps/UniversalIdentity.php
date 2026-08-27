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

use KrubiK\WebApps\DTOs\WebAppInitData; /// KrubiK\WebApps\DTVOs\WebAppInitData;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

/**
 * [Value Object] The Universal Identity :: The Soul Stone
 * Archetype: The One (Unity, Singularity)
 *
 * The ultimate, unified representation of an identity. It encapsulates its soul
 * in a single, immutable, type-safe form. It is the Single Source of Truth
 * for "who" is interacting with the system.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
final readonly class UniversalIdentity
{
    public const SRC_WEBAPP_INIT_DATA = 'webapp_init_data';
    public const SRC_WEB_SESSION = 'web_session';
    public const SRC_API_TOKEN = 'api_token';
    public const SRC_GUEST = 'guest';

    public bool $isAuthenticated;
    public bool $isGuest;

    /**
     * @param string $source The origin of this identity (e.g., self::SRC_WEBAPP_INIT_DATA).
     * @param ?string $platform The canonical platform name (e.g., 'telegram', 'bale', 'web').
     * @param Authenticatable|array|null $user The user entity. Can be an Eloquent model or a MiniApp user array.
     * @param ?array $payload The raw, validated data payload from the source.
     */
    public function __construct(
        public string $source,
        public ?string $platform,
        public Authenticatable|array|null $user,
        public ?array $payload, // Kept for other identity sources like WebSession
        public ?WebAppInitData $genesis = null
    ) {
        $this->isAuthenticated = ($this->user !== null);
        $this->isGuest = !$this->isAuthenticated;
    }

    /**
     * Creates a guest identity, aware of its origin platform.
     * The default state for unknown visitors, now it's context-aware.
     * @param string $platform The platform this guest is visiting from (e.g., 'web', 'telegram').
     */
    public static function guest(string $platform = 'web'): self
    {
        return new self(self::SRC_GUEST, $platform, null, null);
    }

    /**
     * [Factory] Creates an identity from a validated WebAppInitData object.
     * CHANGE: Adapts to the new property-based, DTO-centric structure of WebAppInitData.
     *
     * @param WebAppInitData $initData The validated and certified initData value object.
     * @return self
     */
    public static function fromWebApp(WebAppInitData $initData): self
    {
        // CHANGE: The user entity is now a rich WebAppUser DTO.
        // We convert it to an array to satisfy the `Authenticatable|array|null` type hint.
        $userPayload = $initData->user?->toArray(); // Using null-safe operator for extra safety

        // @Todo Optionally, we can find/create an Eloquent user here based on $user['id']

        return new self(
            self::SRC_WEBAPP_INIT_DATA,
            $initData->platform->value, // Get platform string from the Enum
            $userPayload,
            null, // The raw payload is encapsulated within WebAppInitData
            $initData // The entire object is the proof.
        );
    }

    /** Creates an identity from a standard web session. */
    public static function fromWebSession(Authenticatable $user): self
    {
        return new self(self::SRC_WEB_SESSION, 'web', $user, ['session_id' => session()->getId()], null);
    }

    /*
     * Returns the unique identifier of the user, regardless of its type.
     * A safe way to get the user's ID.
    */
    #[\ReturnTypeWillChange]
    public function id(): int|string|null
    {
        if ($this->user instanceof Authenticatable) {
            return $this->user->getAuthIdentifier();
        }
        return isset($this->user['id']) ? (int)$this->user['id'] : null;
        /// return $this->genesis?->user->id ?? 0;
    }

    /** Checks if the identity originates from a MiniApp/WebApp context. */
    public function isFromWebApp(): bool
    {
        return $this->source === self::SRC_WEBAPP_INIT_DATA;
    }

    /**
     * Retrieves the original, validated WebAppInitData object, used to create this identity.
     * This is the "Genesis Block" && "Evidentiary Provenance" of the identity.
     *
     * Returns null if the identity was NOT forged from a source that provides
     * a genesis-proof object (e.g., a standard web session).
     *
     * @return WebAppInitData|null Returns null if the identity was not created from a source that provides a proof object.
    */
    public function getData(): ?WebAppInitData
    {
        return $this->genesis;
    }
    public function getInitData(): ?WebAppInitData
    {
        return $this->genesis;
    }

    /** A safe, dot-notation accessor for the raw payload. */
    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->payload ?? [], $key, $default);
    }

    /** Magically proxies property access to the underlying user entity. */
    public function __get(string $name): mixed
    {
        if (is_array($this->user)) {
            return $this->user[$name] ?? null;
        }
        return $this->user?->{$name};
    }
}
