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
 * The foundational contract for all bot drivers.
 *
 * This interface defines the essential methods a driver must implement to interact
 * with its specific messaging platform's API. It ensures that Krubot can
 * communicate with any driver in a standardized way.
 */
interface FluentDriverInterface
{
    // -------------------------------------------------------------------------
    // Fluent Builder Methods
    // -------------------------------------------------------------------------

    /**
     * Set the chat target for the message.
     */
    public function chat(string $chat_id): static;

    /**
     * Set the message text content.
     */
    public function message(string $text, ?string $parse_mode = null): static;

    /**
     * Reply to a specific message.
     */
    public function replyTo(string $message_id): static;

    /**
     * Attach a file from a local path.
     */
    public function file(string $path): static;

    /**
     * Attach a file using its platform-specific ID.
     */
    public function file_id(string $file_id): static;

    /**
     * Specify the type of the file being sent via file_id.
     */
    public function file_type(string $file_type): static;

    /**
     * Set the caption for a file.
     */
    public function caption(string $caption, ?string $parse_mode = null): static;

    /**
     * Create a poll.
     */
    public function poll(string $question, array $options): static;

    /**
     * Send a location.
     */
    public function location(float $lat, float $lng): static;

    /**
     * Send a contact.
     */
    public function contact(string $first_name, string $phone_number): static;

    /**
     * Attach an inline keyboard.
     */
    public function inlineKeypad(array $keypad): static;

    /**
     * Attach a reply keyboard (chat keypad).
     */
    public function chatKeypad(array $keypad, ?string $keypad_type = 'New'): static;

    /**
     * Set the source for forwarding a message.
     */
    public function forwardFrom(string $from_chat_id): static;

    /**
     * Set the destination for forwarding a message.
     */
    public function forwardTo(string $to_chat_id): static;

    /**
     * Set the ID of the message to be acted upon (e.g., edit, delete, forward).
     */
    public function messageId(string $message_id): static;

    // -------------------------------------------------------------------------
    // Action Methods
    // -------------------------------------------------------------------------

    /**
     * Send the built message.
     */
    public function send(): array;

    /**
     * Send the built file.
     */
    public function sendFile(): array;

    /**
     * Forward the specified message.
     */
    public function forward(): array;

    /**
     * Edit the specified message (text, keypad, or both).
     */
    public function editMessage(): array;

    /**
     * Delete the specified message.
     */
    public function sendDelete(): array;

    // -------------------------------------------------------------------------
    // Utility & Info Methods
    // -------------------------------------------------------------------------

    /**
     * Download a file to a specific path.
     */
    public function downloadFile(string $file_id, string $to): void;

    /**
     * Get the last raw API response from the driver.
     */
    public function getLastResponse(): array;
}
