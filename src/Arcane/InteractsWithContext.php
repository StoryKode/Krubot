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

trait InteractsWithContext
{
    /**
     * Shared data container for the current request lifecycle.
     */
    protected array $contextData = [];

    /**
     * Set a value in the context (Shared between middlewares/handlers).
     */
    public function setData(string $key, mixed $value): self
    {
        $this->contextData[$key] = $value;
        return $this;
    }

    /**
     * Retrieve a value from the context.
     */
    public function getData(string $key, mixed $default = null): mixed
    {
        return $this->contextData[$key] ?? $default;
    }

    /**
     * Alias for setData (UniChatKit 'set' compatibility).
     */
    public function set(string $key, mixed $value): self
    {
        return $this->setData($key, $value);
    }

    /**
     * Alias for getData (UniChatKit 'get' compatibility).
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->getData($key, $default);
    }

    /**
     * Checks if a key exists in the current request's context.
     *
     * @param string $key The key to check.
     * @return bool True if the key exists, false otherwise.
     */
    public function hasData(string $key): bool
    {
        return array_key_exists($key, $this->contextData);
    }
    
    /**
     * Retrieves all data from the current request's context.
     * Useful for debugging or logging the entire request state.
     *
     * @return array A copy of the context data array.
     */
    public function allData(): array
    {
        return $this->contextData;
    }

    /**
     * [THE ASYNC SAVIOR] Resets the context data to an empty state.
     * This method is the cornerstone of async safety. It MUST be called at the
     * beginning of every `processUpdate` call to prevent data from one request
     * leaking into the next in a long-running application server.
     *
     * @return $this
     */
    public function resetContextData(): self
    {
        $this->contextData = [];
        return $this;
    }

    /**
     * [POWER-UP] Adds a key/value pair only if the key does not already exist.
     * Prevents downstream middleware from overwriting critical data set by an earlier one.
    */
    public function addData(string $key, mixed $value): self
    {
        if (!$this->hasData($key)) {
            $this->setData($key, $value);
        }
        return $this;
    }

    /**
     * [POWER-UP] Merges an array of data into the context.
     * Perfect for injecting an entire user model or config array in one go.
     * Existing keys will be overwritten.
    */
    public function mergeData(array $data): self
    {
        $this->contextData = array_merge($this->contextData, $data);
        return $this;
    }

    /**
     * [POWER-UP] Retrieves a value from the context and then removes it.
     * The ultimate tool for "flash messages" or single-use tokens.
    */
    public function pullData(string $key, mixed $default = null): mixed
    {
        $value = $this->getData($key, $default);
        $this->forgetData($key);
        return $value;
    }
    public function pull(string $key, mixed $default = null): mixed { return $this->pullData($key, $default); }

    /**
     * [POWER-UP] Removes one or more items from the context by key.
    */
    public function forgetData(string|array $keys): self
    {
        foreach ((array) $keys as $key) {
            unset($this->contextData[$key]);
        }
        return $this;
    }
    public function forget(string|array $keys): self { return $this->forgetData($keys); }
}
