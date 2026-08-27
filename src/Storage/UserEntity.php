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
 * Class UserEntity
 * 
 * Represents a User in the conversation context.
 * It holds both the "Platform Info" (ID, Username) and the "Storage Info" (Extra data you saved).
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class UserEntity
{
    protected string $id;
    protected ?string $firstName;
    protected ?string $lastName;
    protected ?string $username;
    protected Collection $storageInfo;

    /**
     * @param array $platformInfo Data coming from Rubika API (sender_id, etc.)
     * @param array $storageData Data coming from your Cache/DB
     */
    public function __construct(array $platformInfo, array $storageData = [])
    {
        $this->id = $platformInfo['id'] ?? '';
        $this->firstName = $platformInfo['first_name'] ?? null;
        $this->lastName = $platformInfo['last_name'] ?? null;
        $this->username = $platformInfo['username'] ?? null;
        
        // Convert storage array to Collection for easy access
        $this->storageInfo = collect($storageData);
    }

    // --- Standard Getters ---

    public function getId(): string
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Get the full name (First + Last).
     */
    public function getName(): string
    {
        return trim("{$this->firstName} {$this->lastName}");
    }

    // --- Storage Accessors ---

    /**
     * Get a value from the user's custom storage.
     * 
     * Example: $user->get('age');
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->storageInfo->get($key, $default);
    }

    /**
     * Get all custom storage data as an array.
     */
    public function getInfo(): array
    {
        return $this->storageInfo->all();
    }

    /**
     * Check if specific data exists in storage.
     */
    public function has(string $key): bool
    {
        return $this->storageInfo->has($key);
    }
}
