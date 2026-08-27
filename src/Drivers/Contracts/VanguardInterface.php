<?php

namespace KrubiK\Drivers\Contracts;
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

/**
 * Layer 2: Vanguard'z Driver Interface
 * 
 * Shared methods in UniversalDriverInterface & FluentDriverInterface
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
*/
interface VanguardInterface
{
    // -------------------------------------------------------------------------
    // Utility & Info Methods
    // -------------------------------------------------------------------------    

    /**
     * Get information about the bot user.
     */
    public function getMe(): array;

    /**
     * Get information about a chat.
     */
    public function getChat(array $data): array;

    /**
     * Get file download URL.
     */
    public function getFile(string $file_id): string;

    /**
     * Send the built poll.
     */
    public function sendPoll(): array;

    /**
     * Send the built location.
     */
    public function sendLocation(): array;

    /**
     * Send the built contact.
     */
    public function sendContact(): array;   

    // -------------------------------------------------------------------------
    // Spam Management Methods
    // -------------------------------------------------------------------------

    /**
     * Check if the user is currently performing actions considered as spam.
     */
    public function isUserSpamming(string $userId): bool;

    /**
     * Check if the system has historically flagged the user as a spammer.
     */
    public function isUserSpamDetected(string $userId): bool;

    /**
     * Clear the spam status or counters for a specific user.
     */
    public function resetUserSpamState(string $userId): void;
}
