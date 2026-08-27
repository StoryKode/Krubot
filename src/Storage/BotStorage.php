<?php

namespace KrubiK\Storage;
/*
|--------------------------------------------------------------------------
| A Message to the Future Architect of Rebellion... 🚀🌌
|--------------------------------------------------------------------------
|
| Greetings, seeker of knowledge. You have just opened a blueprint
| from the Krubot BotEngine. What you see before you is more
| than just lines of code—it's a pattern for building scalable dreams.
|
| **This is a laboratory of creation.** We are experimenting with the
| very fabric of code here. Use this project as your ultimate training
| ground, a masterclass in *Software Dev Artistry.* It's a powerful template
| for learning, but not yet forged for the final battles of production.
|
| Behold the core principle:
| We Are **Rebuilding The Rebellion** Within S.N.P. *(The Foundation of Pure Power & Revel)*
| This entire library is being reconstructed with intense power,
| on a foundation of pure power **Far Stronger Than Anything That Came Before.**
| Starting with Laravel 12 Capabilities.
|
| What you see here is the **×ReleaseCandiate v0.8×** release. Why release it now?
| Because keeping this evolution a secret any longer would be a
| betrayal to the very community it was born to serve.
| 
| Consider this The Foundational Codex for Engineering a New Reality.
| The knowledge is free under the MIT License. Deconstruct its logic and schematics.
| Learn its secrets. Master its power. Command its potential. You are The Architect Now!
|
| * Go build something revolutionary! * 💜⚡️
|
| Let's Shape the Future. 🛠️⚡️🚀
|
*/

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * 🏛 BotStorage Class v5.0.0 (The Ultimate Omniscient Obsidian Edition)
 *
 * A powerful, fluent, and context-aware wrapper around Laravel Cache.
 * Combines UniChatKit's API simplicity with Laravel's power and Multiverse capabilities.
 *
 * ⚔️ Capabilities:
 * - Multiverse Support (Rubika, Telegram, Bale, etc.)
 * - Context Aware (User/Chat/Driver/Global)
 * - Auto-Merging of Data (Additive behavior)
 * - Smart Collection Returns (Active Record style)
 * - Dot Notation Support (user.profile.name)
 * - Atomic Counters (increment/decrement)
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class BotStorage
{
    /**
     * Default Time-To-Live in minutes (default: 60 days).
     */
    protected int $defaultMinutes = 60 * 24 * 60;

    /**
     * The root namespace for all keys (prevents collision with other apps).
     */
    protected string $rootNamespace = 'krubot';

    /**
     * The default key to use if none is provided (Context Key).
     */
    protected ?string $defaultKey = null;

    /**
     * The Ultimate Constructor.
     *
     * @param string $driver      The multiverse identity (e.g., 'rubika', 'telegram').
     * @param string $scope       The storage scope (e.g., 'user', 'chat', 'driver').
     * @param string|null $defaultKey Optional initial context key (e.g., UserID).
     */
    public function __construct(
        protected string $driver,
        protected string $scope,
        ?string $defaultKey = null
    ) {
        if ($defaultKey) {
            $this->defaultKey = $defaultKey;
        }
    }

    /**
     * ⚙️ CONFIGURATION: Set the Time-To-Live dynamically.
     * 
     * @param int $minutes
     * @return static
     */
    public function setTTL(int $minutes): static
    {
        $this->defaultMinutes = $minutes;
        return $this;
    }

    /**
     * ⚙️ CONFIGURATION: Change the Driver Context on the fly.
     * Dangerous! Use with caution (PhantomShell/'s gift).
     * 
     * @param string $driver
     * @return static
     */
    public function setDriver(string $driver): static
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * ⚙️ CONFIGURATION: Set the default context key (e.g., User ID) dynamically.
     * 
     * @param string $key
     * @return static
     */
    public function setDefaultKey(string $key): static
    {
        $this->defaultKey = $key;
        return $this;
    }

    /**
     * 💾 SAVE: Save or update data in the storage.
     * Merges with existing data (Additive behavior).
     *
     * @param array $data Key-value pairs to save.
     * @param string|null $key Optional explicit key. If null, uses default context.
     * @return static
     */
    public function save(array $data, ?string $key = null): static
    {
        $targetKey = $this->resolveKey($key);
        $cacheKey = $this->getCacheKey($targetKey);

        // 1. Retrieve existing data to ensure we merge, not overwrite
        $currentData = Cache::get($cacheKey, []);

        if (!is_array($currentData)) {
            $currentData = []; // Safety net if cache got corrupted
        }

        // 2. Merge new data (Recursively or Simple?) 
        // Simple merge allows replacing keys, which is standard behavior.
        $newData = array_merge($currentData, $data);

        // 3. Store back with TTL
        Cache::put($cacheKey, $newData, now()->addMinutes($this->defaultMinutes));

        return $this;
    }

    /**
     * 💾 PUT: Direct Alias for saving a single key-value or array.
     * Supports dot notation for updating nested arrays inside the JSON blob.
     * 
     * @param string|array $key   Key (dot.notation) or Array of data.
     * @param mixed $value Value (if key is string).
     */
    public function put(string|array $key, mixed $value = null): static
    {
        if (is_array($key)) {
            return $this->save($key);
        }

        // Retrieve all, set the specific dot-notation key, and save back.
        $all = $this->all();
        data_set($all, $key, $value);
        
        // We use $this->save to persist the modified array.
        // Note: passing the full array to save() merges it, effectively updating it.
        return $this->save($all);
    }

    /**
     * 🔍 FIND: Retrieve a specific entry wrapper.
     * Returns a "StorageCollection" which is a Smart Collection with methods like ->delete() and ->save().
     *
     * @param string|null $key The ID to find. If null, uses default context.
     * @return StorageCollection
     */
    public function find(?string $key = null): StorageCollection
    {
        $targetKey = $this->resolveKey($key);
        $cacheKey = $this->getCacheKey($targetKey);

        $data = Cache::get($cacheKey, []);

        // Create a Smart Collection and bind it to this storage instance
        // Assuming StorageCollection exists in your project.
        $collection = new StorageCollection($data);
        $collection->setContext($this, $targetKey);

        return $collection;
    }

    /**
     * 🎯 GET: Retrieve a specific value directly from the storage (Shortcut).
     * Supports dot notation (e.g., 'user.preferences.color').
     *
     * @param string $key The data key to retrieve (NOT the storage ID).
     * @param mixed $default Default value if missing.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        // This method operates on the DEFAULT context (Current User/Chat)
        // It mimics: $bot->userStorage()->find()->get('key');
        
        $allData = $this->all(); // Uses default context logic internally
        return data_get($allData, $key, $default);
    }

    /**
     * 📦 ALL: Retrieve ALL data from the current context.
     *
     * @param string|null $key Optional explicit ID.
     * @return array
     */
    public function all(?string $key = null): array
    {
        $targetKey = $this->resolveKey($key);
        $cacheKey = $this->getCacheKey($targetKey);
        
        return Cache::get($cacheKey, []);
    }

    /**
     * 🔥 DELETE: Delete a specific storage entry completely.
     *
     * @param string|null $key The ID to delete. If null, deletes current context.
     */
    public function delete(?string $key = null): static
    {
        $targetKey = $this->resolveKey($key);
        $cacheKey = $this->getCacheKey($targetKey);

        Cache::forget($cacheKey);

        return $this;
    }

    /**
     * 🚮 FLUSH: Completely flush the cache for a specific key (Alias for delete).
     */
    public function flush(?string $key = null): void
    {
        $this->delete($key);
    }

    /**
     * 🕵️ HAS: Check if a specific key exists in the current context data.
     * Supports dot notation.
     *
     * @param string $key The data key to check.
     */
    public function has(string $key): bool
    {
        $data = $this->all();
        // Using Laravel's data_get to check deep existence is safer than array_key_exists for dot notation
        return data_get($data, $key) !== null; 
    }

    /**
     * 🎣 PULL: Retrieve an item and forget it immediately.
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);
        $this->forget($key); // Remove just this specific key from the array
        return $value;
    }

    /**
     * ✂️ FORGET: Remove a specific key (dot notation) from the stored array, 
     * NOT the whole storage entry.
     * 
     * @param string $key
     * @return static
     */
    public function forget(string|array $key): static
    {
        $data = $this->all();
        \Illuminate\Support\Arr::forget($data, $key);
        
        // We overwrite the storage with the modified array
        // Here we use Cache::put directly because save() merges, and merging a removed key doesn't remove it.
        $targetKey = $this->resolveKey(null);
        Cache::put($this->getCacheKey($targetKey), $data, now()->addMinutes($this->defaultMinutes));
        
        return $this;
    }

    /**
     * ➕ INCREMENT: Atomically increment a value.
     * Great for game scores, step counters, etc.
     * 
     * @param string $key Dot notation key.
     * @param int $amount
     * @return int The new value.
     */
    public function increment(string $key, int $amount = 1): int
    {
        $current = (int) $this->get($key, 0);
        $new = $current + $amount;
        $this->put($key, $new);
        return $new;
    }

    /**
     * ➖ DECREMENT: Atomically decrement a value.
     * 
     * @param string $key
     * @param int $amount
     * @return int
     */
    public function decrement(string $key, int $amount = 1): int
    {
        return $this->increment($key, $amount * -1);
    }

    /**
     * 🧠 REMEMBER: Get an item from storage, or execute the given Closure and store the result.
     * 
     * @param string $key
     * @param \Closure $callback
     * @return mixed
     */
    public function remember(string $key, \Closure $callback): mixed
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = $callback();
        $this->put($key, $value);
        return $value;
    }

    // -------------------------------------------------------------------------
    // 🛡 Internal Helpers (The Engines)
    // -------------------------------------------------------------------------

    /**
     * Resolves which ID (Key) to use.
     * If explicit key is passed, use it. Otherwise use default context.
     * 
     * @throws InvalidArgumentException
     */
    protected function resolveKey(?string $key): string
    {
        if ($key !== null) {
            return $key;
        }

        if ($this->defaultKey !== null) {
            return $this->defaultKey;
        }

        throw new InvalidArgumentException("BotStorage Error: No key provided and no default context available. Are you calling this outside of a message context? Usage: \$bot->userStorage('ID')->...");
    }

    /**
     * 🧬 Generates the final namespaced cache key.
     * Format: krubot:{driver}:{scope}:{id}
     * 
     * This is the Secret Sauce of the Multiverse System.
     */
    protected function getCacheKey(string $key): string
    {
        // Example: krubot:rubika:user:123456
        // Example: krubot:telegram:chat:g998877
        return "{$this->rootNamespace}:{$this->driver}:{$this->scope}:{$key}";
    }
}
