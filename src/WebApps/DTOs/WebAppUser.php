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

use KrubiK\Enums\Platform; // <-⚡️- Import the Super-Enum
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

/**
 * [Value Object] Represents a user within a WebApp context, engineered for true multi-platform resilience.
 * This immutable object is context-aware; it intelligently adapts to the data structure of the source
 * platform (e.g., Telegram's richness vs. Bale's simplicity) while providing an unwavering, unified, and
 * type-safe interface.
 *
 * @property-read int $id Unique identifier for the user or bot. Universal.
 * @property-read string $firstName First name of the user or bot. Universal.
 * @property-read ?string $username Optional. Username of the user or bot. Universal.
 * @property-read ?string $photoUrl Optional. URL of the user’s profile photo. Universal.
 * @property-read bool $allowsWriteToPm Optional. True, if this user allowed the bot to message them. Universal, defaults to false.
 *
 * @property-read ?string $lastName Telegram-specific. Will be `null` for other platforms.
 * @property-read ?string $languageCode Telegram-specific. Will be `null` for other platforms.
 * @property-read bool $isBot Telegram-specific. Will be `false` for other platforms.
 * @property-read bool $isPremium Telegram-specific. Will be `false` for other platforms.
 * @property-read bool $addedToAttachmentMenu Telegram-specific. Will be `false` for other platforms.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
final readonly class WebAppUser implements Arrayable
{
    // --- Universal Properties (Available on all platforms) ---
    public int $id;
    public string $firstName;
    public ?string $username;
    public ?string $photoUrl;
    public bool $allowsWriteToPm;

    // --- Telegram-Exclusive Properties (Gracefully defaulted for others) ---
    public ?string $lastName;
    public ?string $languageCode;
    public bool $isBot;
    public bool $isPremium;
    public bool $addedToAttachmentMenu;

    // CHANGE: Type-hint changed from WebAppPlatform to the superior KrubiK\Enums\Platform.
    private function __construct(array $data, Platform $platform)
    {
        // --- Universal Mandatory Fields ---
        $this->id = (int) ($data['id'] ?? throw new InvalidArgumentException('WebAppUser data requires an [id].'));
        $this->firstName = $data['first_name'] ?? throw new InvalidArgumentException('WebAppUser data requires a [first_name].');

        // --- Universal Optional Fields ---
        $this->username = $data['username'] ?? null;
        $this->photoUrl = $data['photo_url'] ?? null;
        $this->allowsWriteToPm = (bool) ($data['allows_write_to_pm'] ?? false);

        // --- Context-Aware Initialization for Telegram-Specific Fields ---
        // This is the core of our multi-platform strategy. We only attempt to read
        // these fields if the platform is Telegram. Otherwise, we provide sane, safe defaults.
        // This prevents errors and provides a consistent API for the developer.

        
        if ($platform->matches('telegram', 'tg')) { // You can use multiple aliases, It's not just more readable; It's now more Robust; if you plan to add more aliases for 'telegram'.

            $this->lastName = $data['last_name'] ?? null;
            $this->languageCode = $data['language_code'] ?? null;
            $this->isBot = (bool) ($data['is_bot'] ?? false);
            $this->isPremium = (bool) ($data['is_premium'] ?? false);
            $this->addedToAttachmentMenu = (bool) ($data['added_to_attachment_menu'] ?? false);

        } else {

            // For Bale or any other platform, these fields simply don't exist.
            // We set them to non-breaking default values.
            $this->lastName = null;
            $this->languageCode = null;
            $this->isBot = false;
            $this->isPremium = false;
            $this->addedToAttachmentMenu = false;
        }
    }

    /**
     * Creates a new WebAppUser instance from a raw data array and its source platform.
     *
     * @param array $data The associative array of user data, typically from a JSON decode.
     * @param Platform $platform The platform from which the data originated.
     * @return self
     */
    public static function fromArray(array $data, Platform $platform): self
    {
        return new self($data, $platform);
    }

    /**
     * Converts the Value Object into a plain associative array.
     * This is useful for serialization, logging, or interoperability with
     * systems that expect array data. The keys are kept in snake_case
     * to mirror the original format from the web-app init data.
     *
     * @return array<string, mixed>
    */
    public function toArray(): array
    {
        return [
            // Universal Properties
            'id' => $this->id,
            'first_name' => $this->firstName,
            'username' => $this->username,
            'photo_url' => $this->photoUrl,
            'allows_write_to_pm' => $this->allowsWriteToPm,

            // Telegram-Specific Properties
            'last_name' => $this->lastName,
            'language_code' => $this->languageCode,
            'is_bot' => $this->isBot,
            'is_premium' => $this->isPremium,
            'added_to_attachment_menu' => $this->addedToAttachmentMenu,
        ];
    }
}
