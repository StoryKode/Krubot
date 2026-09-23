<?php

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

use stdClass;
use InvalidArgumentException;

/**
 *      HasDataGateway Trait 🔌 The Universal Memory JackIn
 * 
 * Installs a single, stable access point (`data()`) onto a host, allowing it
 * to connect to the Noxia mainframe for dynamic state management.
 * It's the ultimate backdoor for data expansion.
 *
 * Supercharged to emulate and exceed the legendary polymorphism of jQuery's .data()
 * It serves as a ultra-performant Getter, Multi-Getter, Setter, Mass-Setter, Inline-Syntax Parser,
 * and Null-Eraser—all running through a single, devastatingly fast polymorphic gateway
 * with zero memory leaks diretcly onto Noxia's virtual-vault.
 * 
 * It can be safely installed even on immutable stone constructs like Enums;
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.9×
 * @license MIT
*/
trait DataPort
{
    /**
     * ⚡ THE OMNI-PORT: Read, Write, Erase, and Bind.
     *
     * This method acts as a multiplexer, translating intuitive high-level calls
     * into precise, atomic operations dispatched to the Noxia mainframe. It retains
     * full backward compatibility with the jQuery-style DX.
     *
     * Polymorphic DX Cheat Sheet:
     * ---------------------------
     * #1. VAULT ACCESS:        $obj->data()                      => stdClass (The entire Virtual-Vault / GET ALL) : Returns the raw stdClass virtual-vault
     * #2. MASS ERASE:          $obj->data(null)                  => $this (Chainable) : Erases The entire Virtual-Vault belongs to $this and returns $this.
     * #3. SINGLE READ:         $obj->data('key')                 => mixed|null : Returns the value of 'key' (or null if not set).
     * #4. MASS READ:           $obj->data(['k1', 'k2'])          => ['k1' => v1, 'k2' => v2|null] // returns an assoc array, If you send him a non-assoc array!
     * #5. SINGLE WRITE:        $obj->data('key', 'value')        => $this (Chainable) : Sets 'key' to 'value' and returns $this.
     * #6. MASS WRITE:          $obj->data(['k' => 'v', 'x' => 1])=> $this (Chainable) : Mass-assigns data and returns $this.
     * #7. ERASE (Null):        $obj->data('key', null)           => $this (Deletes property named 'key')
     * #8. INLINE SYNTAX:       $obj->data('power: 100')          => $this (Parses & Stores "key: value")
     * #9. MASS-INLINE:        $obj->data('hp: 50; mp: 20')       => $this (Parses multiple pairs and engraves(saves) them)
     * 10. INLINE ERASE:      $obj->data('hp: ; mp: null')        => $this (Deletes both properties)
     * 11. MIXED-SYNTAX:     $obj->data('k1:v1; k2:v2; k3:; k4:null')=> Dispatches engrave (k1, k2) & evaporate (k3, k4) commands
     * 12. FAST-SYNTAX:     $obj->data(null, ['k' => 'v'])      => $this (Purge then mass-write like #6)
     * 13. FASTER-SYNTAX:  $obj->data(null, 'k:v; k2:v2')      => $this (Purge data, parse data-string and mass-store it like #9)
     *
     * @param string|array|null $payload The key, array of keys/values, or inline string syntax.
     * @param mixed             $value   The value to set (omit to GET, pass null to ERASE).
     *
     * @return stdClass|mixed|array|$this Depending on the invocation signature.
     * @throws InvalidArgumentException If protocol rules are violated (e.g., inline syntax is mixed with a second argument)
    */
    public function data(string|array|null $payload = null, mixed $value = null): mixed
    {
        $argsCount = func_num_args();

        // 1. RAW VAULT ACCESS (GET ALL) 🧠
        // The only operation that still uses `entangle` to get a raw handle
        // to the entire shadow-construct for direct, low-level inspection.
        if ($argsCount === 0) {
            // Bootstraps Noxia's quantum link and returns the raw shadow-construct.
            return Noxia::entangle($this);
        }

        // 2. PURGE ALL: Explicit null as the only argument 🧹
        // Erases every trace of data for this object and returns $this for chaining.
        if ($argsCount === 1 && $payload === null) {
            Noxia::obliterate($this);
            return $this;
        }

        // ─────────────────────────────────────────────────────────────
        // 🔒 One-time initialization of the two anonymous static handlers.
        // These are kept as function-local static variables: they persist
        // across calls (per class using this trait) but are only built the
        // very first time `data()` runs. Everything after is a raw $var() call.
        // ─────────────────────────────────────────────────────────────
        static $applyDataAssoc = null;
        static $applyDataString = null;

        if ($applyDataAssoc === null) {
            /** @var Closure(object, array): void $applyDataAssoc */
            $applyDataAssoc = static function (object $subject, array $payload): void {
                foreach ($payload as $k => $v) {
                    if ($v === null) {
                        Noxia::evaporate($subject, (string) $k);
                    } else {
                        Noxia::engrave($subject, (string) $k, $v);
                    }
                }
            };

            /** @var Closure(object, string): void $applyDataString */
            $applyDataString = static function (object $subject, string $payload): void {
                // Nothing to parse without a colon.
                if (!str_contains($payload, ':')) {
                    return;
                }

                // Split by semicolons, trim each part, skip empty parts
                $declarations = array_filter(array_map('trim', explode(';', $payload)));

                foreach ($declarations as $declaration) {

                    // Split into property and value at the first colon
                    $parts = explode(':', $declaration, 2);
                    $key = trim($parts[0] ?? '');
                    if ($key === '') {
                        continue; // ignore malformed declarations
                    }

                    $val = isset($parts[1]) ? trim($parts[1]) : '';

                    if ($val === '' || strtolower($val) === 'null') {
                        // Multi-Setter :=> remover
                        Noxia::evaporate($subject, $key); // Command: Evaporate
                    } else {
                        Noxia::engrave($subject, $key, $val); // Command: Engrave
                    }
                }
            };
        }

        // 3. PURGE & REPOPULATE ♻️ — null first argument, with non-null second argument.
        // First severs every prior bond, then writes the new payload (array or inline string)
        // onto the freshly cleansed connection.
        if ($payload === null) {
            Noxia::obliterate($this);

            // data(null, null) → just purge, nothing to repopulate.
            if($value === null)
                return $this;

            if (is_array($value)) {
                $applyDataAssoc($this, $value);
                return $this;
            }

            if (is_string($value)) {
                $applyDataString($this, $value);
                return $this;
            }

            // Unsupported payload type — purge is done, nothing to write.
            return $this;
        }

        // --- ARRAY PAYLOADS (DISPATCHING BATCH OPERATIONS) ---
        if (is_array($payload)) {
            // 34. MULTI-EXCAVATE (MULTI-GET) 📡
            // Executes a batch of `excavate` commands for a list of keys (Array is strictly sequential/indexed).
            if (array_is_list($payload)) {
                $result = [];
                foreach ($payload as $k) {
                    $result[$k] = Noxia::excavate($this, $k);
                }
                return $result;
            }

            // 5. MASS-ENGRAVE / MASS-EVAPORATE (MASS-SET+ERASE) 💥
            // Atomically dispatches `engrave` or `evaporate` for each item, if Array is associative.
            $applyDataAssoc($this, $payload);
            return $this; // Fluent chaining
        }

        // --- STRING PAYLOADS (DISPATCHING SINGLE OR PARSED OPERATIONS) ---

        // 6. INLINE SYNTAX PARSER & DISPATCHER 🧵
        // Parses 'key:value' syntax and dispatches commands to Noxia's protocol.
        if (str_contains($payload, ':')) {
            if ($argsCount > 1) {
                throw new InvalidArgumentException(
                    "Protocol Violation: Cannot pass a second argument when using inline-data syntax."
                );
            }

            $applyDataString($this, $payload);
            return $this; // Fluent chaining
        }

        // 7. SINGLE EXCAVATE (SINGLE GET) 🔍
        // A direct `excavate` command. More performant than probe+excavate.
        if ($argsCount === 1) {
            return Noxia::excavate($this, $payload);
        }

        // 8. SINGLE ENGRAVE / EVAPORATE (SINGLE SET) 🎯
        // Dispatches a single, precise command based on the value.
        if ($value === null) {
            Noxia::evaporate($this, $payload);
        } else {
            Noxia::engrave($this, $payload, $value);
        }

        return $this; // Fluent chaining
    }
}
