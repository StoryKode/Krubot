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
use KrubiK\Drivers\Contracts\MultiverseEnforcer;

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
     * Keys are now composite: "scope:regiment:instance"
     * (or "scope:instance" in legacy mode, e.g., "user:rubika", "chat:telegram")
     * @var array<string, BotStorage>
    */
    protected array $_storageInstances = [];

    /**
     * Property to hold the identity (as a string, injected by Nemesis - The KrubotManager)
     *
     * ⚠️ Now nullable to avoid PHP 8.2 typed-property initialization errors
     * when the trait is used before setCurrentDriver() is invoked.
    */
    protected ?string $currentDriverCodeName = null;

    /**
     * Lazily-cached regiment name (invalidated when the driver context changes).
    */
    protected ?string $_currentRegimentCache = null;

    /**
     * The active "Working Verse" (Global Context Override).
     * If set, all storage calls default to this driver.
     *
     * setWorkingVerse() call fully determines the storage destination.
     *
     * @var array{regiment:?string, instance:string}|null
    */
    protected ?array $_workingVerse = null;

    /**
     * 🔮 SET WORKING VERSE (Omniscience Multi-Bot Native Context Switcher)
     *
     * Sets the default driver scope for all subsequent storage calls.
     *
     * Fully resolves the target's (bot, instance) pair so that subsequent
     * storage calls land in the right namespace.
     *
     * Usage:
     *   $bot->setWorkingVerse('telegram');              // current regiment's telegram
     *   $bot->setWorkingVerse('telegram_support');      // explicit instance
     *   $bot->setWorkingVerse('tg', regiment: 'support');    // explicit both
     *
     *   $bot->setWorkingVerse(Platform::Bale());        // ✨ HYPER-DX
     *   $bot->userStorage()->get('foo');                // Reads from Bale storage
     *   $bot->setWorkingVerse(null);                    // Reset to current actual driver
    */
    public function setWorkingVerse(string|Platform|null $platform, ?string $regiment = null): static
    {
        if ($platform === null) {
            $this->_workingVerse = null;
            return $this;
        }

        $nemesis = $this->nemesis();
        $isMulti = $nemesis->isMultiBotMode();

        $logicalBot = $regiment ?? $nemesis->currentRegiment();
        $instance   = $nemesis->resolveDriverName(
            $platform instanceof Platform ? (string) $platform : $platform,
            $logicalBot,
        );

        $this->_workingVerse = [
            'regiment' => $isMulti ? $logicalBot : null,
            'instance' => $instance,
        ];

        return $this;
    }

    public function setCurrentDriver(string|MultiverseEnforcer $alias): void
    {
        $this->currentDriverCodeName = $alias instanceof MultiverseEnforcer
            ? $alias->getCodeName()
            : (string) $alias;

        // Invalidate the bot cache — the context may have shifted mid-request.
        $this->_currentRegimentCache = null;
    }

    /**
     * Helper to get the current ACTUAL driver's alias as a string.
     * Relies on the Manager injecting it via setCurrentDriver.
    */
    public function getDriverCodeName(): string
    {
        // Null-coalescing is safe now that the property is `?string`.
        return $this->currentDriverCodeName
            ?? (string) config('krubot.default_driver', 'rubika'); // اگر ست نشده بود، فرض را بر پیش‌فرض می‌گذاریم
    }

    /**
     * Lazily resolve and cache the current bot name.
    */
    protected function currentRegimentName(): ?string
    {
        if ($this->_currentRegimentCache === null && app()->bound('nemesis')) {
            $this->_currentRegimentCache = $this->nemesis()->currentRegiment();
        }
        return $this->_currentRegimentCache;
    }

    /**
     * 🧠 INTELLIGENT RESOLVER (v2 - Platform-Powered)
     * Determines which driver to target based on the Hierarchy of Command.
     * It now returns a canonical Platform object for ultimate certainty.
     *
     * Priority 1: Inline Argument (Explicit override) -> userStorage(Platform::Bale())
     * Priority 2: Working Verse (Context switch)      -> setWorkingVerse('bale')
     * Priority 3: Natural State (Current Driver)      -> getDriverCodeName()
     *
     * @param string|Platform|null $explicitPlatform
     * @return Platform
     * @throws RuntimeException If no valid platform can be resolved.
    */
    protected function resolveTargetDriverLegacy(string|Platform|null $explicitPlatform = null): Platform
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
        $platform = Platform::tryFrom($this->getDriverCodeName());
        if ($platform) return $platform;
        
        // This should theoretically never be reached if the manager works correctly.
        throw new RuntimeException("KrubiK Storage Error: Could not resolve a target driver.");
    }    

    /**
     * @deprecated Retained as a compatibility shim for subclasses that
     *             override it. New code should use resolveStorageContext().
     */
    protected function resolveTargetDriver(string|Platform|null $explicitPlatform = null): Platform
    {
        [, $platform] = $this->resolveStorageContext($explicitPlatform);
        return Platform::tryFrom($platform) ?? Platform::default();
    }

    /**
     * 🧠 STORAGE CONTEXT RESOLVER (v3 - Multi-Bot Core)
     *
     * Returns a triple describing the storage destination:
     *
     *   [0] keyBot   : ?string  — the bot segment for the cache key
     *                             (null in legacy mode → key omits bot)
     *   [1] platform : string   — canonical platform ('telegram', ...)
     *   [2] instance : string   — driver instance name ('telegram_main', ...)
     *
     * Priority for target selection:
     *   1. Inline $driver argument        userStorage(Platform::Telegram())
     *   2. Working Verse override         setWorkingVerse('tg', bot: 'support')
     *   3. Natural state (current driver) (from setCurrentDriver)
     *
     * Priority for bot context:
     *   1. Explicit $bot argument
     *   2. Working Verse bot (if set)
     *   3. Current bot from Nemesis
     *   4. null (legacy mode fallback)
     *
     * @return array{0:?string, 1:string, 2:string}
    */
    protected function resolveStorageContext(
        string|Platform|null $driver = null,
        ?string $regiment = null,
    ): array {
        $nemesis = $this->nemesis();
        $isMulti = $nemesis->isMultiBotMode();

        // ── 1. Determine the logical regiment for resolution ──
        // Always populated (Nemesis defaults to 'default' in legacy mode);
        // the KEY-segment bot is separately nullified below for compat.
        $logicalBot = $regiment
            ?? $this->_workingVerse['regiment']
            ?? $this->currentRegimentName()
            ?? $nemesis->currentRegiment();

        // ── 2. Determine the target instance ──
        if ($driver !== null) {
            $target = $driver instanceof Platform ? (string) $driver : $driver;
            $instance = $nemesis->resolveDriverName($target, $logicalBot);
        } elseif ($this->_workingVerse !== null) {
            $instance = $this->_workingVerse['instance'];
        } else {
            // Natural state: normalize the current driver's code name.
            $instance = $nemesis->resolveDriverName(
                $this->getDriverCodeName(),
                $logicalBot,
            );
        }

        // ── 3. Determine the canonical platform from the instance config ──
        $platform = (string) $nemesis->platformFor($instance, $logicalBot);

        // ── 4. Compute the KEY-segment bot ──
        // Legacy deployments MUST omit the bot segment to preserve existing
        // cache keys. Multi-bot deployments MUST include it.
        $keyBot = $isMulti ? $logicalBot : null;

        return [$keyBot, $platform, $instance];
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
    public function userStorage(string|Platform|null $driver = null, ?string $userId = null, ?string $regiment = null): BotStorage
    {
        [$keyBot, $platform, $instance] = $this->resolveStorageContext($driver, $regiment);

        // Cache key includes BOTH dimensions so cross-bot lookups don't collide.
        //   legacy:    "user:telegram"
        //   multi-bot: "user:main:telegram"
        $instanceKey = $keyBot !== null
        ?
            "user:{$keyBot}:{$instance}"
        :
            "user:{$instance}"; // Cache Key: "user:telegram" vs "user:rubika"

        if (!isset($this->_storageInstances[$instanceKey])) {
            $storage = new BotStorage($platform, 'user', null, $keyBot);

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
    public function chatStorage(?string $chatId = null, string|Platform|null $driver = null, ?string $regiment = null): BotStorage
    {
        [$keyBot, $platform, $instance] = $this->resolveStorageContext($driver, $regiment);

        $instanceKey = $keyBot !== null
        ?
            "chat:{$keyBot}:{$instance}"
        :
            "chat:{$instance}";

        if (!isset($this->_storageInstances[$instanceKey])) {
            $storage = new BotStorage($platform, 'chat', null, $keyBot); // or 'channel'

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
    public function channelStorage(?string $chatId = null, string|Platform|null $driver = null, ?string $regiment = null): BotStorage
    {
        return $this->chatStorage($chatId, $driver, $regiment);
    }

    /**
     * Access Driver-Scoped Storage (System Configs).
     * (Global Configs) ForExample: Scoped to 'Rubika' or generic driver.
     * @param string|Platform|null $driver
     * @return BotStorage
     */
    public function driverStorage(string|Platform|null $driver = null, ?string $regiment = null): BotStorage
    {
        [$keyBot, $platform, $instance] = $this->resolveStorageContext($driver, $regiment);
        $instanceKey = $keyBot !== null
        ?
            "driver:{$keyBot}:{$instance}"
        :
            "driver:{$instance}";

        if (!isset($this->_storageInstances[$instanceKey])) {
            $storage = new BotStorage($platform, 'driver', null, $keyBot);

            // Default key namespaces by INSTANCE, so two bots of the same
            // platform never share system config keys.
            $storage->setDefaultKey("{$instance}_system");

            $this->_storageInstances[$instanceKey] = $storage;
        }

        return $this->_storageInstances[$instanceKey];
    }

    /**
     * Access Context Storage (User inside a specific Chat).
     * @param string|Platform|null $driver
     * @return BotStorage
     */
    public function contextStorage(string|Platform|null $driver = null, ?string $regiment = null): BotStorage
    {
        [$keyBot, $platform, $instance] = $this->resolveStorageContext($driver, $regiment);

        $instanceKey = $keyBot !== null
        ?
            "ctx:{$keyBot}:{$instance}"
        :
            "ctx:{$instance}";

        if (!isset($this->_storageInstances[$instanceKey])) {
            $storage = new BotStorage($platform, 'ctx', null, $keyBot);

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
        $storage = $this->userStorage();
        
        // 3. We call `all()` to fetch everything associated with this user ID
        $storageData  = $storage->all();

        // 4. Pass the storage context down and Return the Combined Entity ✅
        // So the UserEntity can carry its origin and re-resolve the same storage if needed.
        return new UserEntity(
            platformInfo:   $platformInfo,
            storageData:    $storageData,
            platform:       $storage->platform(),
            operative:      $storage->operative(),
        );
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
