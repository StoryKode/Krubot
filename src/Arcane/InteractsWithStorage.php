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

use KrubiK\Storage\BotStorage;
use KrubiK\Enums\Platform; // ✨ AGTP-v1 UPGRADE: Importing the holy Platform Enum
use RuntimeException;

/**
 * Trait InteractsWithStorage (v3.2 Multiverse-Aware Edition)
 *
 * Brings the Ultimate UniChatKit storage capabilities to Krubot.
 * Optimized for Performance, DX, and Cross-Dimensional Operations.
 * This version is fully integrated with the Platform Enum for absolute type-safety.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
trait InteractsWithStorage
{
    /**
     * Instance cache to prevent object recreation within the same request.
     * Keys are now composite: "scope:driver_alias" (e.g., "user:rubika", "chat:telegram")
     * @var array<string, BotStorage>
     */
    protected array $_storageInstances = [];

    // Property to hold the identity (as a string, injected by KrubotManager)
    protected string $currentDriverAlias;

    /**
     * The active "Working Verse" (Global Context Override).
     * If set, all storage calls default to this driver.
     * @var Platform|null
     */
    protected ?Platform $_workingVerse = null;

    /**
     * 🔮 SET WORKING VERSE (Omniscience Context Switcher - Platform Aware)
     *
     * Sets the default driver scope for all subsequent storage calls.
     *
     * Usage:
     * $bot->setWorkingVerse('telegram'); // Still works!
     * $bot->setWorkingVerse(Platform::Bale()); // ✨ HYPER-DX
     * $bot->userStorage()->get('foo');   // Reads from Bale storage
     * $bot->setWorkingVerse(null);       // Reset to current actual driver
     *
     * @param string|Platform|null $platform The driver alias (e.g., 'bale'), a Platform object, or null to reset.
     * @return static
    */
    public function setWorkingVerse(string|Platform|null $platform): static
    {
        // ✨ AGTP-v1 UPGRADE: Normalize any input to a Platform object or null.
        $this->_workingVerse = $platform ? Platform::tryFrom($platform) : null;
        return $this;
    }

    public function setDriverAlias(string $alias): void
    {
        $this->currentDriverAlias = $alias;
    }

    /**
     * Helper to get the current ACTUAL driver's alias as a string.
     * Relies on the Manager injecting it via setDriverAlias.
     */
    public function getDriverAlias(): string
    {
        // اگر ست نشده بود، فرض را بر پیش‌فرض می‌گذاریم
        return $this->currentDriverAlias ?? config('krubot.default_driver');
    }

    /**
     * 🧠 INTELLIGENT RESOLVER (v2 - Platform-Powered)
     * Determines which driver to target based on the Hierarchy of Command.
     * It now returns a canonical Platform object for ultimate certainty.
     *
     * Priority 1: Inline Argument (Explicit override) -> userStorage(Platform::Bale())
     * Priority 2: Working Verse (Context switch)      -> setWorkingVerse('bale')
     * Priority 3: Natural State (Current Driver)      -> getDriverAlias()
     *
     * @param string|Platform|null $explicitPlatform
     * @return Platform
     * @throws RuntimeException If no valid platform can be resolved.
    */
    protected function resolveTargetDriver(string|Platform|null $explicitPlatform): Platform
    {
        // 1. Inline Override (High Priority "Raw" Access)
        if ($explicitPlatform !== null) {
            $platform = Platform::tryFrom($explicitPlatform);
            if ($platform) return $platform;
        }

        // 2. Working Verse (Context Mode)
        if ($this->_workingVerse !== null) {
            return $this->_workingVerse; // It's already a Platform object
        }

        // 3. Natural State (Default)
        $platform = Platform::tryFrom($this->getDriverAlias());
        if ($platform) return $platform;
        
        // This should theoretically never be reached if the manager works correctly.
        throw new RuntimeException("KrubiK Storage Error: Could not resolve a target driver.");
    }

    /**
     * Access the User-Scoped Storage Manager.
     * (Stores data specific to the user, across all chats, in this verse).
     *
     * Usage:
     * $bot->userStorage()->save(['foo' => 'bar']);
     * $bot->userStorage(Platform::Telegram())->all(); // ✨ HYPER-DX
     *
     * @param string|Platform|null $driver Explicit Driver (overrides WorkingVerse)
     * @param string|null $userId Explicit ID (or null for current sender)
     *
     * @return BotStorage
     * @throws RuntimeException If user ID is not available.
    */
    public function userStorage(string|Platform|null $driver = null, ?string $userId = null): BotStorage
    {
        $targetPlatform = $this->resolveTargetDriver($driver);
        $targetDriverAlias = $targetPlatform->value(); // Get the string alias for keys

        // Cache Key: "user:telegram" vs "user:rubika"
        $instanceKey = "user:{$targetDriverAlias}";

        if (!isset($this->_storageInstances[$instanceKey])) {
            $storage = new BotStorage($targetDriverAlias, 'user');

            // Lazy ID Injection:
            // If we are targeting the CURRENT driver, we can use the current senderId.
            // If targeting a DIFFERENT driver, user must provide ID or we assume ID matches (Cross-Platform ID).
            $resolvedId = $userId ?? $this->senderId();

            if ($resolvedId) {
                $storage->setDefaultKey($resolvedId);
            }

            $this->_storageInstances[$instanceKey] = $storage;
        }

        return $this->_storageInstances[$instanceKey];
    }

    /**
     * Access the Channel (Chat/Group) Scoped Storage.
     * (Stores data specific to the group/chat).
     * @param string|null $chatId
     * @param string|Platform|null $driver
     * @return BotStorage
     */
    public function chatStorage(?string $chatId = null, string|Platform|null $driver = null): BotStorage
    {
        $targetPlatform = $this->resolveTargetDriver($driver);
        $targetDriverAlias = $targetPlatform->value();
        $instanceKey = "chat:{$targetDriverAlias}";

        if (!isset($this->_storageInstances[$instanceKey])) {
            $storage = new BotStorage($targetDriverAlias, 'chat'); // or 'channel'

            $resolvedId = $this->resolveChatId($chatId);
            if ($resolvedId) {
                $storage->setDefaultKey($resolvedId);
            }

            $this->_storageInstances[$instanceKey] = $storage;
        }

        return $this->_storageInstances[$instanceKey];
    }

    /**
     * Alias for chatStorage (UniChatKit compatibility) - Now fully Smart!
     * @param string|null $chatId
     * @param string|Platform|null $driver
     * @return BotStorage
     */
    public function channelStorage(?string $chatId = null, string|Platform|null $driver = null): BotStorage
    {
        return $this->chatStorage($chatId, $driver);
    }

    /**
     * Access Driver-Scoped Storage (System Configs).
     * (Global Configs) ForExample: Scoped to 'Rubika' or generic driver.
     * @param string|Platform|null $driver
     * @return BotStorage
     */
    public function driverStorage(string|Platform|null $driver = null): BotStorage
    {
        $targetPlatform = $this->resolveTargetDriver($driver);
        $targetDriverAlias = $targetPlatform->value();
        $instanceKey = "driver:{$targetDriverAlias}";

        if (!isset($this->_storageInstances[$instanceKey])) {
            $storage = new BotStorage($targetDriverAlias, 'driver');
            $storage->setDefaultKey("{$targetDriverAlias}_system");
            $this->_storageInstances[$instanceKey] = $storage;
        }

        return $this->_storageInstances[$instanceKey];
    }

    /**
     * Access Context Storage (User inside a specific Chat).
     * @param string|Platform|null $driver
     * @return BotStorage
     */
    public function contextStorage(string|Platform|null $driver = null): BotStorage
    {
        $targetPlatform = $this->resolveTargetDriver($driver);
        $targetDriverAlias = $targetPlatform->value();
        $instanceKey = "ctx:{$targetDriverAlias}";

        if (!isset($this->_storageInstances[$instanceKey])) {
            $storage = new BotStorage($targetDriverAlias, 'ctx');

            $uId = $this->senderId();
            $cId = $this->chatId();

            if ($uId && $cId) {
                $storage->setDefaultKey("{$cId}_{$uId}");
            }

            $this->_storageInstances[$instanceKey] = $storage;
        }

        return $this->_storageInstances[$instanceKey];
    }

    /**
     * Access Global Storage (Shared across ALL drivers if designed so,
     * but usually we scope it to driver to avoid key collisions in Redis unless intended).
     * For true GLOBAL (driver-agnostic) storage, we can force a 'global' driver key.
     */
    public function globalStorage(): BotStorage
    {
        // Global storage usually doesn't care about the driver,
        // it's the "Registry of Truth" for the whole app.
        // We use a fixed phantom driver name 'universe'.
        if (!isset($this->_storageInstances['global'])) {
            $storage = new BotStorage('universe', 'global');
            $storage->setDefaultKey('system_registry');
            $this->_storageInstances['global'] = $storage;
        }
        return $this->_storageInstances['global'];
    }

    // =========================================================================
    //  ✅ NEW SHORTCUT METHODS
    // =========================================================================

    /**
     * Retrieve the UserEntity object combined with their stored data.
     * This mimics UniChatKit's getStoredUser().
     *
     * @return UserEntity
     */
    public function getStoredUser(): UserEntity
    {
        // 1. Get Basic Info from the update
        $platformInfo = $this->user(); // Returns ['id' => ..., 'first_name' => ...]

        // 2. Get Stored Info from Cache
        // We use all() to fetch everything associated with this user ID
        $storageData = $this->userStorage()->all();

        // 3. Return the Combined Entity
        return new UserEntity($platformInfo, $storageData);
    }

    /**
     * Quickly delete all stored data for the current user.
     * Useful for "Reset" commands.
     */
    public function flushUserStorage(): void
    {
        // Calling delete() without arguments on the manager deletes the default context key
        $this->userStorage()->delete();
    }
}
