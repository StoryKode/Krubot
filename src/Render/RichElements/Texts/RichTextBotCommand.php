<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

readonly class RichTextBotCommand extends RichTextEntity
{
    public function __construct(public RichEntity|string|array $text, public string $bot_command) {}
    /**
     * Static factory to create a new RichTextBotCommand instance.
     *
     * @param RichEntity|callable|string|array $text The visible text of the command.
     * @param string $botCommand The actual command string (e.g., "/help").
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text, string $botCommand): self
    {
        // Resolve the display text, as it might be a closure.
        return new self(self::resolveContent($text), $botCommand);
    }
    public function toArray(): array { return ['type' => 'bot_command', 'text' => $this->normalize($this->text), 'bot_command' => $this->bot_command]; }
}


<?php

namespace KrubiK\Render\RichElements\Texts;

use InvalidArgumentException;
use KrubiK\Render\RichElements\RichEntity;

/**
 * Represents a clickable bot command entity with optional targeting of a specific bot username.
 *
 * This class inherits all HyperDX features from RichTextSymbolLink and adds logic
 * to intelligently append a bot's username to the command, making it explicit.
 */
readonly class RichTextBotCommand extends RichTextSymbolLink
{
    /**
     * The functional command string, which MUST include the leading '/'.
     * After construction, this will include the @username if it was provided and applicable.
     * e.g., "/start" or "/start@jobs_bot"
     * @var string
     */
    public string $bot_command;

    /**
     * The optional username of the target bot, without the leading '@'.
     * @var string|null
     */
    public ?string $username;

    /**
     * HyperDX Constructor for RichTextBotCommand.
     * Intelligently handles various instantiation scenarios, including optional bot username targeting.
     *
     * - `make('start')` -> /start
     * - `make('start', username: 'jobs_bot')` -> /start@jobs_bot
     * - `make('/start@jobs_bot')` -> /start@jobs_bot (correctly avoids double-appending)
     * - `make('Begin', '/start', 'jobs_bot')` -> Creates a link with text "Begin" pointing to /start@jobs_bot
     *
     * @param RichEntity|string|array|null $text The display text.
     * @param string|null $bot_command The functional command value.
     * @param string|null $username The target bot's username (without '@').
     */
    public function __construct(
        RichEntity|string|array|null $text = null,
        ?string $bot_command = null,
        ?string $username = null
    ) {
        if ($text === null && $bot_command === null) {
            throw new InvalidArgumentException('RichTextBotCommand requires at least one argument.');
        }

        $isSingleArgMode = ($text === null || $bot_command === null);
        
        $finalCommand = $bot_command ?? $text;
        $finalText = $text ?? $finalCommand;

        // Auto-prepend the '/' symbol if needed (standard HyperDX feature).
        if ($isSingleArgMode && self::$autoPrependSymbol && is_string($finalCommand) && !str_starts_with($finalCommand, $this->getSymbol())) {
            $finalCommand = $this->getSymbol() . $finalCommand;
            if ($text === null) {
                $finalText = $finalCommand;
            }
        }
        
        // --- NEW: Smartly append bot username ---
        // This is a fast, performant check that runs only once at object creation.
        // It checks if a username is provided AND if the command doesn't already contain one.
        if (!empty($username) && !str_contains($finalCommand, '@')) {
            // Defensively remove '@' from the start of the username in case the user provides it.
            $clean_username = ltrim($username, '@');
            $finalCommand .= '@' . $clean_username;
        }

        // --- Final Property Assignment ---
        $this->bot_command = $finalCommand; // This now correctly contains the @username if applicable.
        $this->username = $username;
        
        // Manually initialize parent properties with the final, processed values.
        $this->text = $finalText;
        $this->value = ltrim($this->bot_command, $this->getSymbol()); // e.g., "start" or "start@jobs_bot"
    }

    public static function make(...$args): self
    {
        return new self(...$args);
    }

    // --- Implementation of Abstract Methods ---
    protected function getSymbol(): string { return '/'; }
    protected function getDataType(): string { return 'bot_command'; }
    
    /**
     * Builds the specific `tg://` protocol URL.
     * It now correctly includes the bot username in the command value.
     *
     * @param string $value The command value, e.g., "start" or "start@jobs_bot".
     * @return string The fully-formed URL.
     */
    protected function buildHref(string $value): string
    {
        // rawurlencode handles the '@' symbol correctly.
        return 'tg://bot_command?command=' . rawurlencode($value);
    }
}
