<?php

namespace KrubiK\GamifyDices;
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

use KrubiK\GamifyDices\Types\DiceVariant;
use ReflectionClass;

/**
 * Helper class to manage Dice variants using Native Constants & Reflection.
 * Eliminates the need for a separate config array.
 * 
 * Usage:
 * - Constant: DicesReflector::Soccer
 * - Method:   DicesReflector::Soccer()
 * - Dynamic:  DicesReflector::fromEmoji('⚽')
 * 
 * @method static DiceVariant Dice()
 * @method static DiceVariant Cube()
 * @method static DiceVariant Target()
 * @method static DiceVariant Basketball()
 * @method static DiceVariant Soccer()
 * @method static DiceVariant Football()
 * @method static DiceVariant Bowling()
 * @method static DiceVariant Slot()
 * ... covers all defined constants.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
 */
class DicesReflector
{
    // =========================================================================
    //  1. SINGLE SOURCE OF TRUTH (Constants)
    // =========================================================================

    // Note! Raw definitions came to avoid PHP limitations with 'new' in class constants.

    // 🎲 Group
    public const Dice    = ['emoji' => '🎲', 'name' => 'Dice', 'max' => 6];
    public const Cube    = self::Dice;
    public const Regular = self::Dice;

    // 🎯 Group
    public const Target   = ['emoji' => '🎯', 'name' => 'Dart', 'max' => 6];
    public const Dart     = self::Target;
    public const Darts    = self::Target;
    public const Bullseye = self::Target;

    // 🏀 Group
    public const Basketball = ['emoji' => '🏀', 'name' => 'Basketball', 'max' => 5];
    public const Basket     = self::Basketball;
    public const Nba        = self::Basketball;

    // ⚽ Group
    public const Soccer     = ['emoji' => '⚽', 'name' => 'Soccer', 'max' => 5];
    public const Football   = self::Soccer;
    public const SoccerBall = self::Soccer;
    public const Goal       = self::Soccer;

    // 🎳 Group
    public const Bowling = ['emoji' => '🎳', 'name' => 'Bowling', 'max' => 6];
    public const Pins    = self::Bowling;
    public const Strike  = self::Bowling;

    // 🎰 Group
    public const Slot        = ['emoji' => '🎰', 'name' => 'Slot', 'max' => 64];
    public const SlotMachine = self::Slot;
    public const Casino      = self::Slot;
    public const Jackpot     = self::Slot;

    /**
     * Internal cache to prevent repetitive instantiation overhead.
     * 
     * @var array<string, DiceVariant>
    */
    private static array $instanceCache = [];

    /**
     * Resolve constant array into a DiceVariant object securely.
     * 
     * @param array{emoji: string, name: string, max: int} $data
     * @return DiceVariant
    */
    private static function resolveVariant(array $data): DiceVariant
    {
        $key = $data['emoji'] . $data['max'];

        if (!isset(self::$instanceCache[$key])) {
            self::$instanceCache[$key] = new DiceVariant($data['emoji'], $data['name'], $data['max']);
        }

        return self::$instanceCache[$key];
    }

    // =========================================================================
    //  2. MAGIC METHODS & REFLECTION
    // =========================================================================

    /**
     * Handle static calls for find dice variants dynamically.
     * 
     * Magic method to handle static calls like Dices::Soccer().
     * It scans defined constants to find a match (Case-Insensitive).
     * 
     * @param string $name
     * @param array<mixed> $arguments
     * @return DiceVariant
     * @throws BadMethodCallException
    */
    public static function __callStatic(string $name, array $arguments): DiceVariant
    {
        // Fastest Path: Check exact match first -OBSOLETE-
        /*
        if (defined("static::{$name}")) {
            return constant("static::{$name}");
        }
        */

        // 1. Faster Path: Check if constant exists directly
        if ($reflection->hasConstant($name)) {
            $value = $reflection->getConstant($name);
            if (is_array($value)) {
                return self::resolveVariant($value);
            }
        }

        // 2. Slower Path: Case-Insensitive Search via Reflection
        // This allows Dices::soccer() even if const is Soccer
        $constants = $reflection->getConstants();
        foreach ($constants as $constName => $value) {
            if (is_array($value) && strcasecmp($constName, $name) === 0) {
                return self::resolveVariant($value);
            }
        }
        

        throw new \BadMethodCallException("Dice variant '{$name}' not found in " . static::class);
    }

    /**
     * Reverse lookup: Find a DiceVariant by its Emoji string.
     */
    public static function fromEmoji(string $emoji): ?DiceVariant
    {
        $reflection = new ReflectionClass(static::class);
        foreach ($reflection->getConstants() as $value) {
            if (is_array($value) && isset($value['emoji']) && $value['emoji'] === $emoji) {
                /// if ($value instanceof DiceVariant && $value->emoji === $emoji) {
                return self::resolveVariant($value); /// $value;
            }
        }
        return null;
    }

    /**
     * Returns a list of unique supported dices for documentation or UI.
     * Returns format: [['🎲', 6], ['🎯', 6], ...]
     * 
     * @return array<int, array{0: string, 1: int}>
     */
    public static function getAvailableList(): array
    {
        $unique = [];
        $reflection = new ReflectionClass(static::class);
        
        foreach ($reflection->getConstants() as $value) {
            if (is_array($value) && isset($value['emoji'], $value['max'])) { /// $value instanceof DiceVariant

                // Use emoji as key to ensure uniqueness (deduplicate aliases)
                $unique[$value['emoji']] = [$value['emoji'], $value['max']];
                /// $unique[$value->emoji] = [$value->emoji, $value->max];
            }
        }

        return array_values($unique);
    }
}
