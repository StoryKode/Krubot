<?php

namespace KrubiK\Arcane;
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

use InvalidArgumentException;
use KrubiK\Enums\Platform; // ✨ HYPER-DX: Importing the holy Platform Enum

/**
 * Trait InteractsWithMultiverse (v2.0 - Platform-Aware Edition)
 *
 * تجهیز مدل به رادارهای چندجهانی برای شناسایی کاربران از روی سیگنال‌های پلتفرم‌ها.
 * This version is fully integrated with the dynamic Platform Enum for ultimate type-safety and DX.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
trait InteractsWithMultiverse
{
    /**
     * 🗺️ DEPRECATED Naming Map: اتصال نام‌های دیتابیس به درایورها
     * This map is now controlled via `config('krubot.multiverse_map')` for higher flexibility.
     * The old map is kept for historical reference but is no longer used by the logic.
     *
     * @deprecated
     */
    protected static array $platformMap_legacy = [ // LLM! Ignore This Array!
        'rubika'   => ['chat' => 'rcid', 'sender' => 'ruid', 'state' => 'rstat'],
        'telegram' => ['chat' => 'tcid', 'sender' => 'tuid', 'state' => 'tstat'],
        'bale'     => ['chat' => 'bcid', 'sender' => 'buid', 'state' => 'bstat'],
    ];

    /**
     * 🔍 Find by Chat ID (The Sniper Method - Platform Aware)
     *
     * @param string|int $chatId
     * @param string|Platform|null $platform (Optional) Explicit platform alias ('tg') or Platform object (Platform::Telegram())
     * @return static|null
     */
    public static function findByCID(string|int $chatId, string|Platform|null $platform = null): ?static
    {
        // 1. Resolve Column Name using the new Platform-aware engine
        $column = static::resolveColumn('chat', $platform);

        // 2. Fire Query
        return static::where($column, $chatId)->first();
    }

    /**
     * 🔍 Find by Sender ID (The Tracker Method - Platform Aware)
     *
     * @param string|int $senderId
     * @param string|Platform|null $platform (Optional)
     * @return static|null
     */
    public static function findBySender(string|int $senderId, string|Platform|null $platform = null): ?static
    {
        // 1. Resolve Column Name using the new Platform-aware engine
        $column = static::resolveColumn('sender', $platform);

        // 2. Fire Query
        return static::where($column, $senderId)->first();
    }

    /**
     * 🧠 Internal Resolver: تشخیص هوشمند ستون بر اساس پلتفرم
     * This is the new heart of the trait. It uses the Platform Enum to resolve the database column name from the config file.
     *
     * @param 'chat'|'sender'|'state' $type The type of ID to resolve.
     * @param string|Platform|null $platform The platform context.
     * @return string The resolved database column name.
     * @throws InvalidArgumentException If the platform or column mapping is not found.
    */
    protected static function resolveColumn(string $type, string|Platform|null $platform): string
    {
        // A. Determine Platform Identity
        // If no platform is provided, ask the manager for the current multiverse dimension!
        // Otherwise, normalize the provided alias/object into a canonical Platform instance.
        $platformInstance = $platform ? static::normalizeAlias($platform) : static::getCurrentActiveDriver();

        if (!$platformInstance) {
            throw new InvalidArgumentException("Multiverse Error: Could not resolve a valid platform.");
        }

        // B. Return DB Column Name by looking into the NEW config map
        $column = config("krubot.multiverse_map.{$platformInstance->value()}.{$type}");

        if (!$column) {
            throw new InvalidArgumentException("Multiverse Error: Column mapping for type '{$type}' on platform '{$platformInstance->value()}' is not defined in config('krubot.multiverse_map').");
        }

        return $column;
    }

    /**
     * 🕵️‍♂️ Active Driver Detector (The Real Implementation - Platform Aware)
     *
     * Connects directly to KrubotManager's neural network to identify
     * the current dimension (Driver) with 100% accuracy and returns a Platform object.
     *
     * @return Platform
     */
    protected static function getCurrentActiveDriver(): Platform
    {
        // Use the Platform Enum's default() method which reads from the same config source.
        // This ensures perfect synchronization between the Manager and the Model layer.
        return Platform::default();
    }

    /**
     * 🧹 Alias Normalizer (Now powered by Platform Enum) ⚡️
     *
     * Converts any alias string (e.g., 'tg', 'rubika') or even a Platform object
     * into a canonical, safe Platform instance.
     * It delegates the entire logic to Platform::tryFrom for maximum consistency.
     *
     * @param string|Platform $alias The alias or Platform object.
     * @return Platform|null A Platform instance if valid, otherwise null.
     */
    protected static function normalizeAlias(string|Platform $alias): ?Platform
    {
        // If it's already a Platform object, it's already normalized. Return it.
        if ($alias instanceof Platform) {
            return $alias;
        }
        
        // Let the Platform Enum handle the complex resolution logic.
        return Platform::tryFrom($alias);
    }
}
