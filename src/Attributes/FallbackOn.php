<?php

namespace KrubiK\Attributes;
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

use Attribute;

/**
 * Marks a method as a type-specific fallback handler.
 * This method will be executed if a message of a specific type (e.g., 'video', 'sticker')
 * is received and NO specific #[OnType] route matches it.
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class FallbackOn
{
    /** @var string[] A clean, flat array of message types. */
    public readonly array $types;
    public readonly int $priority;

    /**
     * @param string|string[] ...$targetTypes Accepts multiple formats for maximum DX:
     *   - #[FallbackOn('video')]
     *   - #[FallbackOn('video', 'audio')]
     *   - #[FallbackOn(['sticker', 'location'])]
     * @param int $priority An integer to determine execution priority. Higher numbers run first.
     */
    public function __construct(string|array ...$targetTypes)
    {
        // Pop the priority off the end if it's the last argument and is an int
        $lastArg = end($targetTypes);
        if (is_int($lastArg) && (count($targetTypes) > 1 || is_array($targetTypes[0]))) {
            $this->priority = array_pop($targetTypes);
        } else {
            $this->priority = 0; // Default priority
        }

        $flattenedTypes = [];
        foreach ($targetTypes as $type) {
            if (is_array($type)) {
                // Handles #[FallbackOn(['sticker', 'location'])]
                $flattenedTypes = array_merge($flattenedTypes, $type);
            } else {
                // Handles #[FallbackOn('video')] and #[FallbackOn('video', 'audio')]
                $flattenedTypes[] = $type;
            }
        }
        // Ensure no duplicates and re-index the array for clean access.
        $this->types = array_values(array_unique($flattenedTypes));
    }
}

