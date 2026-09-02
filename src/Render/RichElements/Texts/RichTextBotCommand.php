<?php

namespace KrubiK\Render\RichElements\Texts;

use InvalidArgumentException;
use KrubiK\Render\RichElements\RichEntity;

/**
 * Represents a HyperDX clickable RichTextBotCommand element with advanced capabilities:
 * - Supports plain text, RichEntity, or arrays as the visible text.
 * - Supports specifying the actual bot command string.
 * - Supports optional targeting of a specific bot '@username', intelligently appending it, if not already present..
 * - Auto-prepends the '/' symbol if missing (configurable).
 * - Inherits from RichTextSymbolLink to benefit from HyperDX link features.
 *
 * Usage examples:
 * - ::make('start')
 * - ::make('start', username: 'jobs_bot')
 * - ::make('/start@jobs_bot')
 * - ::make('Begin', '/start', 'jobs_bot')
 *
 * This class is readonly and immutable after construction.
*/
class RichTextBotCommand extends RichTextSymbolLink
{
    /**
     * The functional bot_command string, always starting with '/'.
     * After construction, this will include the @username suffix if provided and applicable.
     * e.g., "/start" or "/start@jobs_bot"
     *
     * @var string
    */
    public string $bot_command;

    /**
     * The optional username of the target bot, without the leading '@'.
     *
     * @var string|null
    */
    public ?string $username;

    /**
     * HyperDX Constructor for RichTextBotCommand.
     *
     * Examples:
     * - make('start')                          => text="/start", bot_command="/start"
     * - make('start', 'start')                 => text="start", bot_command="/start"
     * - make('start', 'start', 'jobs_bot')     => text="start", bot_command="/start@jobs_bot"
     * - make('/start@jobs_bot')                => text="/start@jobs_bot", bot_command="/start@jobs_bot"
     *
     * @param RichEntity|string|array|null $text The display text of the command.
     * @param string|null $bot_command The actual bot command string (with or without leading '/').
     * @param string|null $username The target bot's username without '@'. [optional]
     * 
     * @throws InvalidArgumentException if both $text and $bot_command are null.
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

        // Determine final command and final text based on provided arguments
        $finalCommand = $bot_command ?? $text;
        $finalText = $text ?? $finalCommand;

        // Auto-prepend '/' if missing and enabled [via `RichTextSymbolLink::$autoPrependSymbol`]
        if (
            $isSingleArgMode
            && self::$autoPrependSymbol
            && is_string($finalCommand)
            && !str_starts_with($finalCommand, $this->getSymbol())
        ) {
            $finalCommand = $this->getSymbol() . $finalCommand;
            // If text was derived from bot_command, keep it in sync
            if ($text === null) {
                $finalText = $finalCommand;
            }
        }

        // Smartly append bot username if provided and not already present
        if (!empty($username) && is_string($finalCommand) && !str_contains($finalCommand, '@')) {
            $cleanUsername = ltrim($username, '@');
            $finalCommand .= '@' . $cleanUsername;
        }

        // Assign final properties
        $this->bot_command = $finalCommand;
        $this->username = $username;

        // Initialize parent properties manually, bypass parent constructor
        $this->text = $finalText;
        // Store the command value without the leading symbol for internal use (e.g. "start" or "start@jobs_bot")
        $this->value = ltrim($this->bot_command, $this->getSymbol());
    }

    /**
     * Static factory method.
     *
     * @param RichEntity|callable|string|array|null $text The display text or command string.
     * @param string|null $bot_command The actual command string (with or without '/').
     * @param string|null $username Optional bot username without '@'.
     * @return self
    */
    public static function make(
        RichEntity|callable|string|array|null $text = null,
        ?string $bot_command = null,
        ?string $username = null
    ): self {
        // Resolve the display text, as it might be a closure.
        $resolvedText = self::resolveContent($text);
        return new self($resolvedText, $bot_command, $username);
    }

    /**
     * Return the symbol for the bot command (always '/').
     *
     * @return string
    */
    protected function getSymbol(): string
    {
        return '/';
    }

    /**
     * Return the data type for this entity (always 'bot_command').
     *
     * @return string
    */
    protected function getDataType(): string
    {
        return 'bot_command';
    }

    /**
     * Builds the tg:// protocol URL for the bot command.
     * It now correctly includes the bot username in the command value.
     *
     * @param string $value The command value, e.g., "start" or "start@jobs_bot".
     * @return string The fully-formed tg:// URL.
    */
    protected function buildHref(string $value): string
    {
        // rawurlencode handles the '@' symbol correctly.
        return 'tg://bot_command?command=' . rawurlencode($value);
    }
}
?>