<?php

namespace KrubiK\Drivers\Arcane;
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

use KrubiK\Arcane\InteractsWithApi;
use KrubiK\Arcane\InteractsWithLockedProperties;
// use KrubiK\Arcane\SummonsCodeSpyz;
use KrubiK\Arcane\InteractsWithContext; // ⚡ Import Context

/**
 * Trait NeonVitality
 * 
 * The "Soul" of the driver. 
 * Instead of a base class, we inject this DNA into any driver 
 * to give it Context, Spying capabilities, and API handling.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
trait NeonVitality // PowerCell / NeonSoul / NeonCore / FusionCore / FusionSoul
{
    use HasDriverIdentity; // Activate Listening to Nemesis Commands

    // 1. Context Management (Data like ->get()/set())
    use InteractsWithContext;

    // 2. API & Networking (Curl helpers)
    use InteractsWithApi;

    // 3. Reflection & Spying (Accessing parent's private props)
    use InteractsWithLockedProperties;

    // use SummonsCodeSpyz;

    /**
     * Driver specific configuration.
     */
    protected array $driverConfig = [];

    /**
     * The Neon Bootstrap.
     * Must be called in the constructor of the concrete driver.
     */
    protected function igniteNeon(?array $config = null): void
    {
        if($config)
            $this->driverConfig = $config;
        
        // Initialize Context (reset builders)
        // $this->resetContext();
        
        // Initialize API Trait dependencies if needed
        // (Since we extend the Core, token is usually handled by parent, 
        // but we can sync things here if needed)
    }

    /**
     * Get value from driver config.
     */
    public function getConfig(string $key, mixed $default = null): mixed
    {
        return $this->driverConfig[$key] ?? $default;
    }
}
