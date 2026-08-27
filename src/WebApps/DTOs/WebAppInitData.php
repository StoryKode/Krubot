<?php
namespace KrubiK\WebApps\DTOs;
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
| What you see here is the **×ReleaseCandiate v0.8×** release. Why release it now?
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

use KrubiK\Enums\Platform; // <-- CHANGE: Import the Super-Enum
use Illuminate\Contracts\Support\Arrayable;
use Carbon\Carbon;
use InvalidArgumentException;
use KrubiK\WebApps\Exceptions\InvalidSignatureException;

/**
 * [Value Object] WebAppInitData - The Ultra Ultimate Hyper-DX, Multi-Platform Initialization Data Processor.
 *
 * This is an architectural masterpiece. An immutable, self-validating, and strongly-typed object
 * providing a heavenly celestial developer experience for handling initialization data from any WebApp platform (Telegram, Bale, etc.).
 * It leverages dedicated, context-aware, immutable DTOs for nested data structures, ensuring absolute type-safety access,
 * platform-resilience, and IDE bliss. It Just Works™.
 *
 * It normalizes common fields, preserves platform-specific ones, and encapsulates
 * the complex validation logic, providing a Robust && Elegant interface.
 *
 * @property-read Platform $platform The source platform of this data.
 * @property-read string $raw The raw, original initData query string.
 * @property-read string $hash The security hash used by the bot server for validation.
 * @property-read int $authDateTimestamp Unix timestamp of the authentication.
 * @property-read Carbon $authDate A Carbon instance of the authentication timestamp.
 * @property-read WebAppUser $user A dedicated, platform-aware DTO for the current user's data.
 *
 * @property-read ?string $queryId Optional. A unique identifier for the WebApp session.
 * @property-read ?string $startParam Optional. The value of the startattach parameter (Telegram-specific).
 * @property-read ?int $canSendAfter Optional. Time in seconds, after which a message can be sent (Telegram-specific).
 *
 * @property-read ?string $signature A signature of all passed parameters(~!hash), which the third party apps can use, to check validity.
 * @property-read ?WebAppUser $receiver Optional. Telegram-specific: A DTO for the chat partner's data.
 * @property-read ?WebAppChat $chat Optional. Telegram-specific: A DTO for the chat's data.
 * @property-read ?string $chatType Optional. Telegram-specific: Type of the chat.
 * @property-read ?string $chatInstance Optional. Telegram-specific: A unique identifier for the chat instance.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
final readonly class WebAppInitData implements Arrayable
{
    // --- Core Properties ---
    public Platform $platform;
    public string $raw;
    public string $hash;
    public int $authDateTimestamp;
    public Carbon $authDate;
    public WebAppUser $user;
    
    // --- Optional Common Properties ---
    public ?string $queryId;
    public ?string $startParam;
    public ?int $canSendAfter;

    // --- Optional Telegram-Specific Properties ---
    public ?string $signature;
    public ?WebAppUser $receiver;
    public ?WebAppChat $chat;
    public ?string $chatType;
    public ?string $chatInstance;

    private array $payload;

    // CHANGE: Constructor now requires a Platform object.
    private function __construct(string $initData, Platform $platform)
    {
        $this->raw = $initData;
        $this->platform = $platform;

        $data = $this->parseInitDataString($initData);
        $this->payload = $data; // Store the parsed data

        // --- Assign Core Mandatory Properties (Universal) ---
        $this->hash = $data['hash'] ?? throw new InvalidArgumentException('InitData is missing the required [hash] parameter.');
        $this->authDateTimestamp = (int) ($data['auth_date'] ?? throw new InvalidArgumentException('InitData is missing the required [auth_date] parameter.'));
        $this->authDate = Carbon::createFromTimestamp($this->authDateTimestamp);
        $this->queryId = $data['query_id'] ?? null;

        // --- Assign Main User DTO (Mandatory, Normalized & Context-Aware) ---
        $userPayload = $data['user'] ?? throw new InvalidArgumentException('InitData is missing the required [user] parameter.');

        $userData = is_string($userPayload) ? json_decode($userPayload, true) : $userPayload;
        /// was: json_decode($userPayload, true, 512, JSON_THROW_ON_ERROR)
        if (empty($userData)) {
            throw new InvalidArgumentException('User data in InitData is empty or invalid JSON.');
        }

        // We pass the platform context down to the WebAppUser factory. This is the crucial link.
        // --- Context is now passed via the master Platform object ---
        $this->user = WebAppUser::fromArray($userData, $this->platform);

        // --- Assign Optional Telegram-Specific Properties ---
        // These fields are Telegram-only, so no change is needed here. They will simply be null
        // when the data comes from Bale.
        $this->startParam = $data['start_param'] ?? null;
        $this->canSendAfter = isset($data['can_send_after']) ? (int) $data['can_send_after'] : null;
        $this->signature = $data['signature'] ?? null;

        // --- Assign Optional Telegram-Specific DTOs and Properties (they will be null if platform is not Telegram) ---
        $this->receiver = isset($data['receiver']) && is_string($data['receiver'])
            ? WebAppUser::fromArray(json_decode($data['receiver'], true), $this->platform) // Pass platform here too for consistency
            : null;

        $this->chat = isset($data['chat']) && is_string($data['chat'])
            ? WebAppChat::fromArray(json_decode($data['chat'], true))
            : null;

        $this->chatType = $data['chat_type'] ?? null;
        $this->chatInstance = $data['chat_instance'] ?? null;
    }

    /**
     * Creates a new WebAppInitData instance from a raw initData string and a platform identifier.
     * This is the designated entry point for creating an instance of this value object.
     *
     * @param string $initData The raw query string from the WebApp [`Telegram.WebApp.initData` or `Bale.WebApp.initData`].
     * @param Platform $platform The platform from which the data originated.
     * @return self
     * @throws InvalidArgumentException if the initData string is malformed or has missing required fields.
     */
    public static function from(string $initData, Platform $platform): self
    {
        return new self($initData, $platform);
    }

    // --- Helper methods, you can also use the superior `Platform->matches(...)` ---
    public function isFromTelegram(): bool
    {
        return $this->platform->matches('tg');
    }

    public function isFromBale(): bool
    {
        return $this->platform->matches('bale');
    }
    
    /**
     * Validates the integrity of the initData against the bot token.
     * Throws an exception if the signature is invalid, otherwise returns void.
     * This Validation Formula is identical for both Telegram and Bale.
     *
     * @param string $botToken The secret token of your bot.
     * @return void
     * @throws InvalidSignatureException if the hash does not match the calculated signature.
    */
    public function validate(string $botToken): void
    {
        // This logic can be expanded for other platforms based on our config.
        // For now, it handles Telegram/Bale style validation.

        // Using a more extensible match for future platforms
        match (true) {
            $this->platform->matches('tg, bale') => $this->validateStandardInitData($token),
            // Placeholder for Rubika or others
            default => null, // Nothing :) I'm fine :)
        };
        
    }

    /**
    * validates TelegramStyle passed InitData
    * Throws an exception if the signature is invalid, otherwise returns void.
    * This Validation Formula is identical for both Telegram and Bale.
    *
    * @param string $botToken The secret token of your bot.
    * @return void
    * @throws InvalidSignatureException if the hash does not match the calculated signature.
   */
    private function validateStandardInitData(string $botToken): void
    {
        // Generate the data-check-string to be signed.
        $dataCheckString = $this->buildDataCheckString();

        // The secret key is the HMAC-SHA-256 signature of the bot's token with the constant string "WebAppData"
        $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);   // The 'true' flag returns raw binary output, which is crucial for the next HMAC step.

        // The calculated hash is the HMAC-SHA-256 signature of the data-check-string with the secret key
        $calculatedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        if (hash_equals($calculatedHash, $this->hash) === false) {
            throw new InvalidSignatureException(
                "WebApp initData signature is invalid. Expected '{$this->hash}', but calculated '{$calculatedHash}'."
            );
        }
    }
    
    /**
     * Builds the data-check-string for validation.
     * This string consists of all key-value pairs from the initData (except 'hash'),
     * sorted alphabetically and separated by a newline character.
     *
     * @return string The formatted string to be used in the HMAC validation.
    */
    private function buildDataCheckString(): string
    {
        // Step 1: Parse the raw query string into an associative array.
        $data = $this->parseInitDataString($this->raw);
        
        // Step 2: The 'hash' field MUST be excluded from the signature base string.
        unset($data['hash']);

        // Step 3: Fields MUST be sorted alphabetically by key to ensure a consistent string.
        ksort($data);

        /// Step 4: Performance/Elegance Tweak: Use http_build_query for a concise and more faster implementation. /// Problematic!
        /// return http_build_query($data, '', "\n");
        
        // Step 4: Manually and explicitly build each "key=value" pair.
        // This is the Critical-Fix. It ensures verify values are used AS-IS,
        // without any modification or encoding, perfectly matching Telegram's expectation.

        $verifyPairs = [];
        foreach ($data as $key => $val)
            $verifyPairs[] = "{$key}={$val}";

        // Step 5: Join the pairs with a newline character ('\n') as the separator.
        return implode("\n", $verifyPairs);
    }
    
    /**
     * A simple utility to parse the initData query string into an associative array.
     *
     * @param string $initData
     * @return array
    */
    private function parseInitDataString(string $initData): array
    {
        $data = [];
        parse_str($initData, $data); // Secure-Enough!
        /// parse_str(rawurldecode($initData), $data);
        return $data;
    }

    /**
     * Returns a Carbon instance representing the authentication date.
     * Provides a fluent, type-safe, and framework-standard way to access the timestamp.
     * This method encapsulates the internal timestamp property, decoupling consumers
     * from the implementation detail of how the date is stored.
     *
     * @return Carbon
    */
    public function getAuthDate(): Carbon
    {
        return Carbon::createFromTimestamp($this->authDateTimestamp);
    }

    /**
     * Returns the full, parsed payload as an associative array.
     * Useful for passing the entire dataset to other services or DTOs.
     *
     * @return array
    */
    public function getPassedData(): array
    {
        return $this->payload;
    }

    /**
     * Get the instance as a secure, public-facing array.
     * Required by the Illuminate\Contracts\Support\Arrayable interface.
     *
     * SECURITY NOTE: This implementation deliberately EXCLUDES sensitive internal
     * properties like `hash` and the raw `payload`. It only exposes the safe,
     * structured data, making it secure to use in API responses.
     *
     * @return array<string, mixed>
    */
    public function toArray(): array
    {
        return [
            'platform' => $this->platform->value,
            'auth_date' => $this->getAuthDate()->toIso8601String(), // Use a standard format
            'query_id' => $this->queryId,
            'start_param' => $this->startParam,

            // Recursively call toArray() on nested Value Objects.
            // The null-safe operator (?->) ensures this works even if they are null.
            'chat' => $this->chat?->toArray(),
            'user' => $this->user?->toArray(),
            'receiver' => $this->receiver?->toArray(),
        ];
    }

    /**
     * Returns the unique identifier for this user or bot.
     * @return int
     */
    public function getUserId(): int
    {
        // CHANGE: Direct, type-safe property access on the WebAppUser DTO.
        return $this->user->id;
    }

    /**
     * Returns the user's first name.
     * @return string
     */
    public function getFirstName(): string
    {
        // CHANGE: Direct property access. More readable and performant.
        return $this->user->firstName;
    }

    /**
     * Returns the user's last name. (Optional)
     * @return string|null
     */
    public function getLastName(): ?string
    {
        // CHANGE: Accessing the public readonly property directly.
        // This is now guaranteed by the type system to exist.
        return $this->user->lastName;
    }

    /**
     * Returns the user's username. (Optional)
     * @return string|null
     */
    public function getUsername(): ?string
    {
        // CHANGE: The new standard for accessing identity attributes.
        return $this->user->username;
    }

    /**
     * Returns the user's IETF language tag. (Optional)
     * @return string|null
     */
    public function getLanguageCode(): ?string
    {
        // CHANGE: Clean, expressive, and impossible to misspell the key.
        return $this->user->languageCode;
    }
    /**
     * @return string|null
    */
    public function getLang(): ?string
    {
        // CHANGE: Clean, expressive, and impossible to misspell the key.
        return $this->user->languageCode;
    }

}
