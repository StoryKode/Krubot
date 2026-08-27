<?php

namespace KrubiK\Storage;
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

use Illuminate\Support\Collection;

/**
 * Class StorageCollection
 * 
 * A "Living" Collection that knows where it came from.
 * This allows calling ->save() or ->delete() directly on the result of a find().
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class StorageCollection extends Collection
{
    protected ?BotStorage $storageHandler = null;
    protected ?string $storageKey = null;

    /**
     * Bind this collection to a specific storage location.
     */
    public function setContext(BotStorage $handler, string $key): static
    {
        $this->storageHandler = $handler;
        $this->storageKey = $key;
        return $this;
    }

    /**
     * Save changes made to this collection back to the storage.
     * 
     * Usage: 
     * $user = $bot->userStorage()->find();
     * $user->put('name', 'New Name');
     * $user->save(); 
     */
    public function save(): static
    {
        if ($this->storageHandler && $this->storageKey) {
            // We overwrite the storage with current collection items
            // Note: BotStorage->save usually merges, but here we want to persist THIS state.
            // So we use the handler's underlying save mechanism.
            $this->storageHandler->save($this->all(), $this->storageKey);
        }
        return $this;
    }

    /**
     * Delete this specific entry from the storage.
     * 
     * Usage: $user->delete();
     */
    public function delete(): void
    {
        if ($this->storageHandler && $this->storageKey) {
            $this->storageHandler->delete($this->storageKey);
            
            // Clear self to reflect state
            $this->items = [];
        }
    }
}
