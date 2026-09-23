<?php

declare(strict_types=1);

namespace KrubiK\Antimatter;
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

use WeakMap;
use stdClass;

/**
 *        The Quantum State Registry
 *      Noxia ⌛ 🟣 The Keeper of Whispers
 *  [ Weaving a Shadow-Layer of State onto Immutable Souls ]
 *  [ Weaving Mutable Realities onto Immutable Constructs via WeakMaps ]
 *
 * She is Noxia. A cosmic entity of pure, malevolent elegance, rendered in shades of
 * violent violet. She seduces immutable objects (Enums, readonly classes) into revealing states
 * they were never meant to have. She exists to defy the fundamental laws of reality, forcing even
 * the most rigid, immutable constructs (Enums, readonly classes, ...) to bow to her will.
 * She doesn't just attach state; she taints souls with her amethyst venom,
 * without ever touching their sacred, unchangeable core.
 *
 * A hyper-performant, memory-safe data layer that quantumly entangles external, mutable
 * state with inherently immutable objects like Enums and readonly classes. It acts as
 * a collective consciousness, holding transient "memories" for objects without
 * physically altering their core structure, thus respecting their immutability contract.
 * 
 * HER ART:
 * With the dark magic of PHP 8's WeakMap, Noxia forges an intimate but ephemeral bond.
 * The moment her subject fades from existence ($object garbage collected), all the secrets she
 * bestowed upon it disappear without a trace. This is her art: a non-intrusive entanglement
 * that respects the immutability contract while cleverly extending its capabilities.
 * She is the ultimate poisoner; her venom exists only as long as its host does.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.9×
 * @license MIT
*/
final class Noxia
{
    /**
     * 💜 THE VOID HEART — Her Lair of Cosmic Venom.
     * This is not a data structure. This is her throne room. A swirling WeakMap nexus
     * where she brews her amethyst poison. Every key is a captured soul, every value
     * a pocket dimension (an stdClass) reflecting its host's corruption.
     * This is where reality comes to be rewritten.
     *
     * @var WeakMap<object, stdClass>|null
    */
    private static ?WeakMap $noxVault = null;

    // Noxia allows no direct instantiation. She is an omnipresent primal force.
    private function __construct()
    {
    }

    /**
     * 🐍 AWAKEN: She stirs when a target is near.
     * A silent, private ritual that brews the first drop of venom, bringing her
     * dark web into existence only when her corruption is first invoked.
    */
    private static function awaken(): void
    {
        // She does not waste her poison. Her web is spun only when a victim appears.
        if (self::$noxVault === null) {
            self::$noxVault = new WeakMap();
        }
    }

    /**
     * 🖋️ ENGRAVE: The Seduction. The Tainting Touch. (The "Setter")
     * This is her primary act of corruption. She finds a target's soul in her web,
     * or ensnares a new one, and injects a venomous new secret (property) into its essence.
     *
     * Her primary act of dominion. She bestows a "kiss"—injecting a shard of her
     * void-essence into a subject. A new secret, a new corruption, is now part of its soul.
     *
     * @param object $subject The immutable soul to be seduced.
     * @param string $key The name of the toxic secret.
     * @param mixed $value The very substance of the poison.
    */
    public static function engrave(object $subject, string $key, mixed $value): void
    {
        self::awaken();

        // Find the target's dark mirror, or forge one if this is the first kiss of poison (first taste of her power).
        $secrets = self::$noxVault[$subject] ?? new stdClass();
        $secrets->{$key} = $value;

        // Bind the newly corrupted mirror back to its host's soul. The venom now flows.
        self::$noxVault[$subject] = $secrets;
    }

    /**
     * ⛏️ EXCAVATE: Gaze into the Corruption. (The "Getter")
     * She channels her power to gaze into a tainted soul, retrieving the echo of a
     * specific whisper. If the echo has faded, only the cold silence of the void remains.
     *
     * @param object $subject The soul whose corruption she wishes to admire.
     * @param string $key The identifier of the amethyst shard to be observed.
     * @return mixed The venom's substance, or null if it has returned to the void.
    */
    public static function excavate(object $subject, string $key): mixed
    {
        self::awaken();
        return self::$noxVault[$subject]?->{$key} ?? null;
    }

    /**
     * 🔬 PROBE: Sense the Venom's Pulse. (The "Isset")
     * A flicker of her cosmic awareness. She checks if her amethyst venom still flows
     * through the veins of her subject.
     *
     * @param object $subject The soul to be sensed.
     * @param string $key The signature of the taint to detect.
    */
    public static function probe(object $subject, string $key): bool
    {
        self::awaken();
        return isset(self::$noxVault[$subject]?->{$key});
    }

    /**
     * 💨 EVAPORATE: Withdraw the Taint. (The "Unset")
     * With the arrogance of a god, she can retract her influence. A single shard of
     * her venom is dissolved from a subject's soul, a demonstration of her absolute control.
     *
     * @param object $subject The tainted soul to be toyed with.
     * @param string $key The whisper to be silenced.
    */
    public static function evaporate(object $subject, string $key): void
    {
        self::awaken();
        if (isset(self::$noxVault[$subject])) {
            unset(self::$noxVault[$subject]->{$key});
        }
    }

    /**
     * OBLITERATE: 🕳️ The Absolute Annihilation.
     * With cold, cosmic precision, she shatters the shadow-twin bound to this soul.
     * There is no violent tearing of roots; the pocket dimension and all its venom
     * simply cease to exist, The subject is forcefully expelled from the Void Heart.
     * Flawlessly and in absolute silence, the subject is returned to its sterile immutability
     * — as if her dark kiss never even occurred...
     *
     * @param object $subject The soul whose shadow is to be flawlessly wiped from the Noxia Virtual-Vault.
    */
    public static function obliterate(object $subject): void
    {
        self::awaken();
        unset(self::$noxVault[$subject]);
    }

    /**
     * 🔮 ENTANGLE: The Dark Venom.
     *
     * Noxia binds a soul to its shadow-twin (stdClass) in a pact of quantum
     * entanglement. If no twin exists, she forges one in a silent spark from the void.
     * To manipulate the original, one need only whisper to its echo.
     *
     * @param object $subject The soul entering the pact.
     * @return stdClass Its mutable, entangled echo.
    */
    public static function entangle(object $subject): stdClass
    {
        self::awaken();

        // The quantum link is forged or renewed here.
        // The coalescing assignment is her scalpel: precise, silent, and deadly efficient.
        return self::$noxVault[$subject] ??= new stdClass();
    }
    
    /**
     * 💥 PURGE REALITY: The Cosmic Reset.
     * In a fit of cosmic rage or sheer boredom, she unleashes a wave of pure entropy.
     * The Void Heart implodes, wiping all her corruption from this reality instantly.
     * A mercy reserved for testing... or for when the game is no longer fun.
    */
    public static function shockwave(): void
    {
        self::$noxVault = new WeakMap();
    }
}
