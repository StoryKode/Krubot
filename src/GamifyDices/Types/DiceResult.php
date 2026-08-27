<?php

namespace KrubiK\GamifyDices\Types;
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

use KrubiK\DTOs\Message;

/**
 * A Readonly DTO to parse and carry Dice API responses safely.
 * Equipped with Game Logic Intelligence.
 * Compatible with PHP 8.2+
 * 
 * آبجکت انتقال داده (DTO)
 * برای مدیریت پاسخ API به صورت امن و ساختاریافته.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/

readonly class DiceResult 
{
    public function __construct(
        public int $value,
        public string $emoji,
        public string $messageId
    ) {}

    /**
     * Factory method to create an instance from raw API response.
     * Handles different potential structures of the Rubika API response.
     *
     * @param array $response The raw array returned by sendDice/makeRequest
    */
    public static function fromResponse(array $response): ?self 
    {
        // Rubika API response structure might vary (direct data or inside message_update)
        $data = $response['data']['message_update'] ?? $response['data'] ?? [];
        
        // Validation: Ensure strictly required fields exist
        if (!isset($data['dice']['value'], $data['dice']['emoji'], $data['message_id'])) {
            return null;
        }

        return new self(
            value: (int) $data['dice']['value'],
            emoji: (string) $data['dice']['emoji'],
            messageId: (string) $data['message_id']
        );
    }

    /**
     * 💎 PURE DX FACTORY:
     * Converts an incoming User Message (Object or Array) into a clean DiceResult DTO.
     * No more $raw['dice']['value'] spaghetti!
    */
    public static function fromIncoming(Message|array|null $message): ?self
    {
        if (!$message) return null;

        // Normalize to array data
        $data = ($message instanceof Message) ? $message->toArray() : $message;

        // Validation: Is this actually a dice roll?
        if (!isset($data['dice']['value'], $data['dice']['emoji'])) {
            return null;
        }

        return new self(
            value: (int) $data['dice']['value'],
            emoji: (string) $data['dice']['emoji'],
            messageId: (string) ($data['message_id'] ?? '')
        );
    }

    /**
     * 🧠 The Logic That Determines if the result is considered a "Win" based on the game type.
     */
    public function isWin(): bool
    {
        return match ($this->emoji) {
            // Football & Basketball (1-5): Goal is usually 4 or 5
            '⚽', '🏀' => $this->value >= 4,

            // Slot Machine (1-64): Jackpot is 64, Big Win > 40
            // Let's be strict: Only Jackpot is a "True Win" for triggers
            '🎰' => $this->value === 64,

            // Standard Dice, Dart, Bowling (1-6): 6 is the winner
            '🎲', '🎯', '🎳' => $this->value === 6,

            // Fallback for unknowns
            default => false,
        };
    }

    /**
     * Helper to get a human-readable score interpretation
     * @Todo: Normalize Results to Int or an Enum
    */
    public function getPerformanceLabel(): string
    {
        if ($this->isWin()) return 'Excellent';
        if ($this->emoji === '🎰' && $this->value > 40) return 'Good';
        if ($this->value === 1) return 'Critical Fail';
        return 'Normal';
    }
}
