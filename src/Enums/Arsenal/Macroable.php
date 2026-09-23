<?php

declare(strict_types=1);

namespace KrubiK\Enums\Arsenal;
/*
| Krubot BotEngine: The Architect's Lexicon [×vRC.9×] 🚀📜
|--------------------------------------------------------------------------
| This is **a Playground For Mastery**, a laboratory of ***Software Dev Artistry***;
| not a weapon for production's final battles.
|
| Our Bond: ***"Rebuilding The Rebellion"*** Within S.N.P. (The Foundation of Pure Power & Revel).
| Your Mandate [MIT]: Deconstruct Krubot. Command it. Master it. You are The Architect Now!
|
| *Go build something revolutionary!* 💜⚡️
*/

use Stringable; // Import the interface
use BadMethodCallException;
use ValueError;
use Illuminate\Contracts\Support\Arrayable;
use Closure;

/**
 * ⚡ THE AWAKENING PROTOCOL: The Cybernetic Exoskeleton for Enums ⚡
 *
 * Why let Enums live in an eternal, frozen state of boredom? 
 * This trait is the ultimate life-support system that breathes raw, dynamic energy 
 * into static Enum structures. Equip this, and watch your Enums evolve from simple, 
 * lifeless values into unstoppable, hyper-intelligent entities! 🚀
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.9×
 * @license MIT
*/
trait Macroable
{
    /**
     * 💉 THE SURGICAL GRAFT (Single-Ability Injection)
     * 
     * Instantly uploads a brand-new superpower directly into the Enum's DNA.
     * Give it a name, plug in the payload, and watch the magic happen in real-time! ✨
     * 
     * @param string $name The trigger word for this newly grafted power.
     * @param Closure|callable $macro The raw executable energy/logic to be injected.
    */
    public static function macro(string $name, Closure|callable $macro): void
    {
        EnumOvermind::implant(static::class, $name, $macro);
    }

    /**
     * 🌪️ THE MASS-AUGMENTATION PROTOCOL (Full Skill-Tree Download)
     * 
     * Why inject one ability when you can upload an entire matrix of skills? 
     * This method rips all available powers from a Mixin blueprint and permanently 
     * fuses them into the Enum's nervous system. Matrix-style: "I know Kung Fu." 🥋
     * 
     * @param string|object $mixin The blueprint holding the arsenal of abilities.
     * @param bool $replace Ruthlessly overwrite existing powers if a collision occurs?
    */
    public static function mixin(string|object $mixin, bool $replace = true): void
    {
        EnumOvermind::boost(static::class, $mixin, $replace);
    }

    /**
     * 📡 NEURAL PING (The Life-Sign Scanner)
     * 
     * Sends a high-frequency pulse into the Overmind to check if a specific 
     * capability is alive and breathing inside the Enum's matrix. 💚
     * 
     * @param string $name The capability trigger to scan for.
    */
    public static function hasMacro(string $name): bool
    {
        return EnumOvermind::implanted(static::class, $name);
    }

    /**
     * 💥 THE NEURAL PURGE (Targeted Memory Wipe)
     * 
     * Initiates a localized format of the Enum's synaptic matrix. 
     * This protocol severs all neural links and obliterates every grafted ability, 
     * forcefully returning the Enum to its vanilla, lifeless, immutable state. 
     * 
     * Highly essential for sanitizing the testing sandbox and preventing 
     * cross-contamination between quantum parallel realities (Unit Tests). 🧹💀
    */
    public static function clearMacros(): void
    {
        EnumOvermind::detach(static::class);
    }

    /**
     * 🌀 THE QUANTUM GATEWAY (Dynamic Instance Invocation)
     * 
     * The heartbeat of the living Enum case! When an Enum instance attempts to unleash 
     * a power it wasn't born with, this magic gateway catches the anomaly, retrieves the 
     * implanted soul (Closure) from the Overmind, binds it physically to THIS exact 
     * Enum case, and unleashes the energy! 💥
     * 
     * Example: Role::ADMIN->customMethod() 🌍
    */
    public function __call(string $method, array $parameters): mixed
    {
        // 🚨 SHIELD ACTIVATED: Block unauthorized access attempts.
        if (! static::hasMacro($method)) {
            throw new BadMethodCallException(sprintf(
                'Neural Misfire: Method %s::%s does not exist in the matrix.', 
                static::class, 
                $method
            ));
        }

        // 🧩 Extract the dormant capability from the central registry.
        $macro = EnumOvermind::implant(static::class, $method);

        if ($macro instanceof Closure) {
            // 🧬 SYMBIOSIS: Bind the current Enum instance ($this) directly into the Closure.
            // It creates a perfect, seamless fusion between the logic and the state.
            return Closure::bind($macro, $this, static::class)(...$parameters);
        }

        // ⚡ Execute raw callable payloads directly.
        return $macro(...$parameters);
    }

    /**
     * 🌌 THE COSMIC MONOLITH (Dynamic Static Invocation)
     * 
     * Calling out to the heavens! When you ask the Enum CLASS itself to perform
     * a miracle, this static gateway answers. It summons the raw, unbound payload 
     * from the Overmind and executes it in a purely static void. Zero gravity, infinite power. 🌠
     * 
     * Example: Role::customStaticMethod() 🦸‍♂️
    */
    public static function __callStatic(string $method, array $parameters): mixed
    {
        // 🚨 SHIELD ACTIVATED: Block unauthorized static anomalies.
        if (! static::hasMacro($method)) {
            throw new BadMethodCallException(sprintf(
                'Neural Misfire: Static method %s::%s does not exist in the EnumOvermind\'z NeuralCortex.', 
                static::class, 
                $method
            ));
        }

        // 🧩 Summon the raw capability from the celestial registry.
        $macro = EnumOvermind::implant(static::class, $method);

        if ($macro instanceof Closure) {
            // 👻 ASTRAL PROJECTION: \Closure::bind($closure, $context, $newScope)
            // Passing 'null' as $newThis forces a purely STATIC context.
            // Binds the closure to the Enum class without needing a physical body (instance).
            return Closure::bind($macro, null, static::class)(...$parameters);
        }

        // ⚡ Ignite the raw static callable.
        return $macro(...$parameters);
    }
}
