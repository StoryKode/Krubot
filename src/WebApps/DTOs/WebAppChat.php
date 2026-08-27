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
use InvalidArgumentException;

/**
 * [Value Object] Represents a chat within the Telegram WebApp context.
 * This immutable object provides type-safe access to chat data.
 *
 * @property-read int $id Unique identifier for this chat.
 * @property-read string $type Type of chat, can be "group", "supergroup", or "channel".
 * @property-read string $title Title of the chat.
 * @property-read ?string $username Optional. Username of the chat.
 * @property-read ?string $photoUrl Optional. URL of the chat’s photo.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
final readonly class WebAppChat implements Arrayable
{
    public int $id;
    public string $type;
    public string $title;
    public ?string $username;
    public ?string $photoUrl;

    /**
     * Holds the pre-computed, standardized array, ready for instant serialization.
     * 
     * @var array<string, mixed>
    */
    private array $payload;

    private function __construct(array $data)
    {
        // --- Step 1: Hydrate and validate public properties for internal type safety ---
        // This ensures the object is always in a valid state within the application.
        $this->id = (int) ($data['id'] ?? throw new InvalidArgumentException('WebAppChat data requires an [id].'));
        $this->type = $data['type'] ?? throw new InvalidArgumentException('WebAppChat data requires a [type].');
        $this->title = $data['title'] ?? throw new InvalidArgumentException('WebAppChat data requires a [title].');
        $this->username = $data['username'] ?? null;
        $this->photoUrl = $data['photo_url'] ?? null;

        // --- Step 2: Build the standardized, clean array for serialization (as you commanded!) ---
        // This runs only once, at the end of construction.
        $payload = [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
        ];

        // To create the cleanest possible API output, we omit null optional fields.
        if ($this->username !== null) {
            $payload['username'] = $this->username;
        }

        if ($this->photoUrl !== null) {
            // Always output the tg-api standard snake_case key.
            $payload['photo_url'] = $this->photoUrl;
        }

        $this->payload = $payload;
    }

    /**
     * Creates a new WebAppChat instance from a raw data array.
     *
     * @param array $data The associative array of chat data, typically from a JSON decode.
     * @return self
    */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Returns the pre-computed, standardized data array instantly for perfect integration.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->payload;
    }
}
