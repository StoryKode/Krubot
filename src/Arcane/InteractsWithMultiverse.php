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

    /**
     * 🛡️ The Quantum Locator: Finds a user based on their multi-verse coordinates.
     * Fully powered by the glorious Platform Engine! 🚀
     * 
     * @param mixed $platform The dimension signature (string, Platform object, Request, Model...)
     * @param string|int $senderId The unique ID from the platform.
     * @return self|null
    */
    public static function findByQuantumId(mixed $platform, string|int $senderId): ?self
    {
        // return static::findBySender($senderId, $platform);

        // ۱. استفاده از موتور فوق‌هوشمند tryFrom برای درکِ پلتفرم از هر دیتاتایپی
        // اگر پلتفرم نامعتبر بود، به بُعد پیش‌فرض (Default Driver) برمی‌گردیم
        $platformInstance = Platform::tryFrom($platform) ?? static::getCurrentActiveDriver();

        try {
            // ۲. استخراج دقیق نام ستون از کانفیگ بدون هیچ هاردکدی! (The Architecht's Way)
            $column = static::resolveColumn('sender', $platformInstance);
            
            // ۳. شلیک کوئری به دیتابیس
            return static::where($column, $senderId)->first();

        } catch (\InvalidArgumentException $e) {
            // هندل کردن زمانی که کانفیگ برای این پلتفرم ناقص است
            // می‌توانید لاگ کنید یا یک Fallback در نظر بگیرید
            return null;
        }
    }

    /**
     * Helper to check if this user is a super admin, 
     * bypassing standard Spatie rules if needed.
    */
    public function isQuantumArchitect(): bool
    {
        return $this->hasRole('Super Admin') || $this->id === 1;
    }

    /**
     * Checks if the user is a super admin or a context-specific admin.
     * Context admin IDs are fetched from the Krubot service and cached for performance.
     *
     * @return bool
    */
    public function isContextAdmin(): bool
    {
        if($this->isQuantumArchitect())
            return true;

        $warlord = warlord();
        $contextAdminIds = method_exists($warlord, 'admin_ids') ? $warlord->admin_ids() : [];

        /***

        // disabled to allow hookable/dynamic via AdminIds Attribute in Nexuses

         * Get the admin IDs.
         * Try to retrieve from cache first. If not present,
         * fetch from the Krubot service and cache it for 1 hour (3600 seconds).
        $contextAdminIds = Cache::remember('krubot.admin_ids', 3600, static function () {
            // This closure will only execute if 'krubot.admin_ids' is not in the cache.

            $warlord = warlord();
            return method_exists($warlord, 'admin_ids') ? $warlord->admin_ids() : [];
        });
        */

        // A user is a context admin if:
        // 1. They have the 'Super Admin' role.
        // 2. OR their ID is in the list of admin IDs fetched from the Krubot service.
        return $this->hasRole('Super Admin') || in_array($this->id, $contextAdminIds ?? []);
    }

    /**
     * 🔑 Return the platform-aware sender identifier of this model.
     *
     * این مقدار همان شناسه‌ای است که باید برای مقایسه با
     * Access User_ID و Block User_ID استفاده شود.
     *
     * @param string|Platform|null $platform
     * @return string
     *
     * @throws InvalidArgumentException
     * @throws \RuntimeException
    */
    public function getPlatformId(string|Platform|null $platform = null): string|int
    {

        $column = static::resolveColumn('sender', $platform);
        // → برای تلگرام: 'tuid'
        // → برای بله:    'buid'
        // → برای روبیکا: 'ruid'
        
        /*
        * اگر مدل Eloquent باشد، getAttribute() امن‌ترین روش است.
        * اگر مدل این متد را نداشته باشد، به property دسترسی می‌گیریم.
        */
        if (method_exists($this, 'getAttribute')) {
            $value = $this->getAttribute($column);
        } else {
            $value = $this->{$column} ?? null;
        }
        
        if ($value === null || $value === '') {
            throw new \RuntimeException(
                "Multiverse Error: Sender value '{$column}' is empty."
            );
        }
        
        return is_int($value) ? $value : ((string) $value);
    }

    /**
     * 🎯 Quantum Identity Key — مقدار امن برای مقایسه در Gatekeeper.
     *
     * Priority chain (fail-safe cascade):
     *   1. getPlatformId()  → شناسهی واقعی پلتفرم (SSoT)
     *   2. getKey()         → fallback برای مدلهای غیر-کراباتی یا session خالص
     *
     * این متد هرگز throw نمیکنه — همیشه یک رشته برمیگردونه (یا رشتهی خالی).
     * دلیل: Gatekeeper نباید به خاطر یک کاربر ناقص، کل pipeline رو بشکنه.
    */
    public function quantumIdentityKey(): string
    {
        // ── Primary: The Real Deal ──
        try {
            $platformId = $this->getPlatformId();
            if ($platformId !== null && $platformId !== '') {
                return (string) $platformId;
            }
        } catch (\Throwable $e) {
            // Silent: config ناقص، column خالی، پلتفرم نامشخص، ...
            // در این حالت به fallback میریم.
        }

        // ── Fallback: Legacy / Session-only users ──
        // (اگه مدل trait رو نداشته باشه، getKey هم همیشه هست)
        return (string) $this->getKey();
    }
    

}
