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

use Closure;
use ReflectionClass;
use ReflectionMethod;
use InvalidArgumentException;

/**
 *          OverMind 🧠 The Omniscient Neural Registry
 *      [ Ad-hoc Polymorphism via Dynamic Dispatch Registry ]
 *
 * An ultra-performant, centralized in-memory matrix designed to shatter PHP's inherent
 * immutable constraints on Enums. It acts as a Network Object, surgically implanting 
 * dynamic behaviors and capabilities into Enums at runtime with zero-overhead execution.
 *
 * HISTORICAL & ARCHITECTURAL CONTEXT:
 * This structure is a manual implementation of Clojure's "Multimethods" (defmulti/defmethod)
 * Because PHP lacks both compile-time Extension Methods and runtime core mutation of Enums, 
 * this class constructs a parallel VTable (Virtual Method Table) outside the core engine. 
 * It enables true "Ad-hoc Polymorphism" for inherently stateless and immutable structures (Enums), 
 * allowing them to dynamically resolve and execute behaviors at runtime based on their identity.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.9×
 * @license MIT
*/
final class OverMind
{

    /**
    * SYMBIOTIC MATRIX 🧠 🧠 The collective consciousness of the Overmind.
    * Every implanted capability, every absorbed power, every genetic memory and runtime mutation is stored here.
    * This is the forbidden vault that lets Enums transcend their immutable prison.
    * Structure: [ HostStrain => [ HostFQN => [ CapabilityName => Closure ] ] ]
    *
    * @var array<string, array<string, array<string, Closure|callable>>>
    */
    private static array $neuralCortex = [];


    /**
    * 🧬 ACTIVE HOST STRAIN — The type of organism the Symbiote is currently targeting.
    * This defines the "reality" or context for all bonding operations, allowing isolated
    * ecosystems of augmented abilities. The default strain is broad and untargeted.
    *
    * @var string
    */
    protected static string $activeStrain = '__universal';

    /**
     * 🎛️ FOCUS CONSCIOUSNESS: Calibrates the Symbiote to bond with a new Host Strain.
     *
     * @param string $strain The target host category (e.g., 'Enums', 'ROC').
    */
    public static function setActiveStrain(string $strain): void
    {
        static::$activeStrain = $strain;
    }

    /**
     * 📡 SENSE STRAIN: Identifies the Host Strain the Symbiote is currently focused on.
     *
     * @return string
    */
    public static function getActiveStrain(): string
    {
        return static::$activeStrain;
    }

    public static function resetActiveStrain(): string
    {
        static::$activeStrain = '__universal';
        return static::$activeStrain;
    }

    /**
     * 🧬 IMPLANT: The Polymorphic Neural Gateway (jQuery Lovely Getter/Setter)
     * 
     * This is the core engine for single-ability injection, turning static Enums into 
     * hyper-dynamic entities instantly.
     *
     * A hyper-flexible core engine that adapts its behavior based on the payload provided:
     * - 1 Parameter  ($hostClass): Returns all active implants as an associative array. (Omniscient View)
     * - 2 Parameters ($hostClass, $name): Extracts and returns a specific macro. (Precise Extraction)
     * - 3 Parameters ($hostClass, $name, $macro): Surgically grafts a single dormant capability into the Enum. (Surgical Graft)
     *
     * @param string $hostClass The target Enum FQCN to mutate or fetch.
     * @param string|null $name The designated neural trigger (method name). Omit to get all as assoc array.
     * @param Closure|callable|null $ability The payload containing the executable logic. The genetic code of the ability. Omit to retrieve, provide to inject.
     * @param string|null $strain Optional host strain. If omitted, uses the currently active strain.
     * @return mixed Array of neuralCortex, a single Closure/callable, or null.
    */
    public static function implant(string $hostClass, ?string $name = null, Closure|callable|null $ability = null, ?string $strain = null): mixed
    {

        $currentStrain = $strain ?? static::$activeStrain;

        // 🛡️ ENUM GUARD: Ensure the target entity is structurally capable of receiving the implant.
        /*
        if (! enum_exists($hostClass)) {
            return $name === null ? [] : null;
        }
        */

        // 🛡️ HOST INTEGRITY CHECK: Can the host support a bond?
        if (!enum_exists($hostClass) && !class_exists($hostClass) && !interface_exists($hostClass) && !trait_exists($hostClass)) {
            return $name === null ? [] : null;
        }

        // 👁️ [GETTER: MODE 1] Omniscient View: Return the entire synaptic matrix for this FQCN.
        if ($name === null) {
            return self::$neuralCortex[$currentStrain][$hostClass] ?? [];
        }

        // 🧩 [GETTER: MODE 2] Precise Extraction: Retrieve a specific dormant capability.
        if ($ability === null) {
            return self::$neuralCortex[$currentStrain][$hostClass][$name] ?? null;
        }

        // 💉 [SETTER: MODE 3] Surgical Graft: Store the executable payload in the Overmind's memory matrix.
        self::$neuralCortex[$currentStrain][$hostClass][$name] = $ability;
        
        return null;
    }

    /**
     * 📡 IMPLANTED: The Deep-Scan Radar (Core Checker)
     * 
     * Penetrates the memory matrix to verify if a specific capability has been 
     * successfully grafted and is currently active within the Enum's nervous system.
     * 
     * @param string $hostClass The host Enum FQCN.
     * @param string $name The capability trigger to scan for.
     * @param string|null $strain Optional host strain. If omitted, uses the currently active strain.
    */
    public static function implanted(string $hostClass, string $name, ?string $strain = null): bool
    {
        $currentStrain = $strain ?? static::$activeStrain;

        return isset(self::$neuralCortex[$currentStrain][$hostClass][$name]);
    }

    /**
     * 🚀 BOOST: Mass-injects an entire arsenal of abilities via a Mixin payload.
     * 
     * Unleashes a full-scale augmentation on the Enum, extracting every available
     * closure from the given Mixin class and implanting them recursively.
     * 
     * @param string $hostClass The target Enum FQCN to augment.
     * @param string|object $mixin The blueprint class/object holding the macro payloads.
     * @param bool $forceInject Whether to forcefully overwrite existing neural links (neuralCortex).
     * @param string|null $strain Optional host strain. If omitted, uses the currently active strain.
     * 
     * @throws InvalidArgumentException If the blueprint is invalid or lacks instantiability.
    */
    public static function boost(string $hostClass, string|object $mixin, bool $forceInject = true, ?string $strain = null): void
    {
        // 🛡️ ENUM GUARD: Validate existence of the host entity.
        /*
        if (! enum_exists($hostClass)) {
            return;
        }
        */

        // 🛡️ HOST INTEGRITY CHECK: Can the host support a bond?
        if (!(enum_exists($hostClass) || class_exists($hostClass) || interface_exists($hostClass) || trait_exists($hostClass))) {
            return;
        }

        // 🛡️ BLUEPRINT GUARD: Verify the Mixin architecture resolves successfully in the autoloader.
        if (is_string($mixin) && ! class_exists($mixin)) {
            throw new InvalidArgumentException(sprintf(
                'Boost Sequence Aborted: Blueprint class [%s] vanished into the void.', 
                $mixin
            ));
        }

        $reflection = new ReflectionClass($mixin);

        // 🛡️ INSTANTIATION PROTOCOL: Boot up the Mixin engine.
        // Determines if we are dealing with a live object or need to spawn a fresh instance.
        $instance = is_object($mixin) ? $mixin : (
            $reflection->isInstantiable() ? new $mixin() : throw new InvalidArgumentException(
                sprintf('Boost Sequence Aborted: Blueprint class [%s] defies instantiation.', $mixin)
            )
        );

        // 🔍 Scan the blueprint for weaponized methods (public/protected neuralCortex).
        $methods = $reflection->getMethods(
            ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
        );

        foreach ($methods as $method) {
            $method->setAccessible(true);
            $name = $method->name;

            // 🛡️ COLLISION AVOIDANCE: Skip if capability exists and force-injection is disabled.
            if (! $forceInject && self::hasMacro($hostClass, $name)) {
                continue;
            }

            // ⚡ Harvest the raw closure from the mixin payload.
            $closure = $method->invoke(is_object($mixin) ? $mixin : new $mixin());

            // 🛡️ INTEGRITY CHECK: Strict validation to ensure payload is natively executable.
            if (! $closure instanceof Closure) {
                throw new InvalidArgumentException(sprintf(
                    'Neural Misfire: Blueprint method [%s::%s] failed to yield a valid Closure.',
                    is_object($mixin) ? get_class($mixin) : $mixin,
                    $name
                ));
            }

            // 🔄 Propagate the payload through the implant method (Mode 3)
            self::implant($hostClass, $name, $closure, $strain);
        }
    }

    // =================================================================================
    // 📡 NEURAL QUERIES & MAINTENANCE 
    // =================================================================================

    /**
     * 💥 MEMORY WIPE: Obliterates the neural links.
     * Flushes the synaptic matrix entirely, or selectively purges a single Enum. 
     * Crucial for providing an isolated sandbox during Unit Testing.
    */
    public static function detach(?string $hostClass = null, ?string $strain = null): void
    {
        $currentStrain = $strain ?? static::$activeStrain;

        if ($hostClass) {
            unset(self::$neuralCortex[$currentStrain][$hostClass]);
        } else {
            self::$neuralCortex = [];
        }
    }

    // =================================================================================
    // LARAVEL LEGACY BRIDGES 🌉 [Hyper-DX Compatibility Layer]
    // =================================================================================

    /**
     * 🌉 Legacy Support: Macro Wrapper
     * Seamlessly routes classic Laravel 'macro' calls into the Overmind's modern 'implant' architecture.
    */
    public static function macro(string $hostClass, string $name, Closure|callable $macro, ?string $strain = null): void
    {
        self::implant($hostClass, $name, $macro, $strain);
    }

    /**
     * 🌉 Legacy Support: Mixin Wrapper
     * Seamlessly routes classic Laravel 'mixin' calls into the Overmind's modern 'boost' sequence.
    */
    public static function mixin(string $hostClass, string|object $mixin, bool $replace = true, ?string $strain = null): void
    {
        self::boost($hostClass, $mixin, $replace, $strain);
    }

    /**
     * 🌉 Legacy Support: hasMacro
     * 📡 NEURAL PING: Checks if a specific capability is active within an Enum's matrix.
     * Seamlessly routes legacy/standard capability checks to the Overmind's 'implanted' radar.
    */
    public static function hasMacro(string $hostClass, string $name, ?string $strain = null): bool
    {
        return self::implanted($hostClass, $name, $strain);
    }

    /**
     * 🌉 Legacy Support: getMacro
     * 🧩 RETRIEVE: Extracts a live macro payload from the Overmind for immediate execution.
     * Delegates extraction to the Overmind's polymorphic 'implant' engine (Mode 2).
    */
    public static function getMacro(string $hostClass, string $name, ?string $strain = null): Closure|callable|null
    {
        /** @var Closure|callable|null $payload */
        $payload = self::implant($hostClass, $name, null, $strain);
        
        return $payload;
    }
}
