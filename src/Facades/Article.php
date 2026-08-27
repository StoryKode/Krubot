<?php

namespace KrubiK\Facades;
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

use Illuminate\Support\Facades\Facade;
use KrubiK\Render\RichMan; // Assuming this is the correct namespace

/**
 * Provides a static-like interface for the RichMan content builder.
 *
 * --- EXPLICIT CREATION ---
 * @method static RichMan create(?string $initialText = null, ?bool $isRtl = null) Creates a new, clean builder instance. This is the fastest way to get a new instance.
 * @method static RichMan make(?string $initialText = null, ?bool $isRtl = null) Alias for the create() method.
 *
 * --- FLUENT BUILDER METHODS (on the resolved instance) ---
 * @method static RichMan add(string|\KrubiK\Render\RichElements\RichEntity|array|callable|\Closure|self $content)
 * @method static RichMan heading(string|callable|array $text, int $size)
 * @method static RichMan paragraph(string|callable|array $text)
 * @method static RichMan bold(string|callable|array $text)
 * @method static RichMan italic(string|callable|array $text)
 * @method static RichMan href(string|callable|array $text, string $url)
 * @method static RichMan bulletList(\KrubiK\Render\RichElements\Blocks\RichBlockListItem ...$items)
 * @method static RichMan orderedList(\KrubiK\Render\RichElements\Blocks\RichBlockListItem ...$items)
 * @method static RichMan preBlock(string $code, ?string $language = null)
 * @method static RichMan table(array|\Illuminate\Contracts\Support\Arrayable $cells, ?bool $isBordered = null, ?bool $isStriped = null, string|callable|array|null $caption = null)
 * @method static RichMan divider()
 * @method static RichMan takeOver(self|string $input, ?string $parserType = null)
 * @method static \KrubiK\Render\Kernel\SoulHarvestor build()
 * @method static string toHtml()
 * @method static string toText()
 *
 * @see \KrubiK\Render\RichMan
*/
class Article extends Facade
{
    /**
     * Get the registered name of the component in the container.
     * This is used for all "magic" method calls like Article::heading(), etc.
     *
     * @return string
    */
    protected static function getFacadeAccessor(): string
    {
        return 'richman';
    }

    /**
     * Creates a new, clean instance of the RichMan builder directly.
     * This method bypasses the container for maximum performance and provides an explicit entry point.
     *
     * @param string|null $initialText
     * @param bool|null $isRtl
     * @return RichMan A new, clean instance of the builder.
    */
    public static function create(?string $initialText = null, ?bool $isRtl = null): RichMan
    {
        // Direct, brainless, and ultra-fast delegation to the static factory method.
        return RichMan::summon($initialText, $isRtl);
    }

    /**
     * An alias for the create() method, following Laravel's conventions.
     *
     * @param string|null $initialText
     * @param bool|null $isRtl
     * @return RichMan A new, clean instance of the builder.
    */
    public static function make(?string $initialText = null, ?bool $isRtl = null): RichMan
    {
        // Same direct delegation for consistency and speed.
        return RichMan::summon($initialText, $isRtl);
    }

    /**
     * An alias for the RichMan::parse() method, following Laravel's conventions.
     *
     * @param string $input The raw, chaotic string matter.
     * @param string $parserType The identity of the matter ('MarkdownV2', 'HTML', etc.).
     * @return RichMan A new, filled instance of the builder, born from the parsed entities..
    */
    public static function scan(RichMan|string $input, ?string $parserType = null): RichMan
    {
        // Same direct delegation for consistency and speed.
        return RichMan::parse($input, $parserType);
    }

    /**
     * An alias for the RichMan::parse() method, following Laravel's conventions.
     *
     * @param string $input The raw, chaotic string matter.
     * @param string $parserType The identity of the matter ('MarkdownV2', 'HTML', etc.).
     * @return RichMan A new, filled instance of the builder, born from the parsed entities..
    */
    public static function from(RichMan|string $input, ?string $parserType = null): RichMan
    {
        // Same direct delegation for consistency and speed.
        return RichMan::parse($input, $parserType);
    }
}
