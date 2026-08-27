<?php

namespace KrubiK\Conversations;
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

/**
 * Trait managing per-request activation stop_skip_pattern_states of stop/skip patterns,
 * plus MrYesMan default callable for condition-less patterns.
 * 
 * This trait manages runtime-level (non‑cached) state of conversation flow,
 * such as emergency controls and temporary pattern enabling/disabling.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
trait ManageConversations
{
    /**
     * Patterns that will automatically stop or skip conversation when matched.
     * Each pattern can be a plain string or a regex.
     */

    protected array $stopPatterns = [];
    protected array $skipPatterns = [];

    // ============================================================
    // 🧩 NEW DYNAMIC PATTERN MANAGEMENT (Stop / Skip control)
    // ============================================================

    // === S_S-Pattern Management helper ===========================================

    public function setStopPatterns(array $patterns): static
    {
        $ref = $this->getMrYesMan();
        $out = [];
        foreach ($patterns as $key => $val) {
            if (is_int($key)) {
                $out[$val] = $ref;
            } else {
                $out[$key] = $val ?? $ref;
            }
        }
        // initialize activation states
        $this->enableStopPatterns($patterns);
        $this->stopPatterns = $out;
        return $this;
    }

    public function setSkipPatterns(array $patterns): static
    {
        $ref = $this->getMrYesMan();
        $out = [];
        foreach ($patterns as $key => $val) {
            if (is_int($key)) {
                $out[$val] = $ref;
            } else {
                $out[$key] = $val ?? $ref;
            }
        }
        // initialize activation states
        $this->enableStopPatterns($patterns);
        $this->skipPatterns = $out;
        return $this;
    }

    public function getStopPatterns(): array
    {
        return $this->stopPatterns;
    }

    public function getSkipPatterns(): array
    {
        return $this->skipPatterns;
    }

    public function removeStopPatterns(mixed ...$patterns): static
    {
        if (count($patterns) === 1 && is_array($patterns[0])) {
            $patterns = $patterns[0];
        }

        // Wildcard or E_ALL means: clear everything
        if (in_array('*', $patterns, true) || in_array(E_ALL, $patterns, true)) {
            foreach ($this->stopPatterns as $p1)
                unset($this->stop_skip_pattern_states['stop'][$p1]);
            $this->stopPatterns = [];
            return $this;
        }

        $this->stopPatterns = array_diff_key($this->stopPatterns, array_flip($patterns));
        foreach ($patterns as $p2)
            unset($this->stop_skip_pattern_states['stop'][$p2]);
        return $this;
    }

    public function removeSkipPatterns(mixed ...$patterns): static
    {
        if (count($patterns) === 1 && is_array($patterns[0])) {
            $patterns = $patterns[0];
        }

        // Wildcard or E_ALL means: clear everything
        if (in_array('*', $patterns, true) || in_array(E_ALL, $patterns, true)) {
            foreach ($this->skipPatterns as $p1)
                unset($this->stop_skip_pattern_states['skip'][$p1]);
            $this->skipPatterns = [];
            return $this;
        }

        $this->skipPatterns = array_diff_key($this->skipPatterns, array_flip($patterns));
        foreach ($patterns as $p2)
            unset($this->stop_skip_pattern_states['skip'][$p2]);
        return $this;
    }

    public function addStopPattern(string $pattern, mixed $checker = null): static
    {
        $this->stopPatterns[$pattern] = $checker ? $checker : $this->getMrYesMan();
        $this->enableStopPatterns($pattern);
        return $this;
    }

    public function addSkipPattern(string $pattern, mixed $checker = null): static
    {
        $this->skipPatterns[$pattern] = $checker ? $checker : $this->getMrYesMan();
        $this->enableSkipPatterns($pattern);
        return $this;
    }

    /**
     * Per‑request memory for pattern activation status
     * Activation stop_skip_pattern_states memory (non-persistent, per-request only).
     * Example:
     *   $stop_skip_pattern_states['stop']['لغو'] = true;
    */

    protected array $stop_skip_pattern_states = [
        'stop' => [],
        'skip' => [],
    ];

    /** Shared by-ref true-returning callable for all unconditional patterns */
    protected static ?\Closure $mrYesMan = null;

    /**
     * Returns the singleton true-return callable.
     */
    protected function getMrYesMan(): \Closure
    {
        if (!self::$mrYesMan) {
            self::$mrYesMan = function (Krubot $bot) { return true; };
        }
        return self::$mrYesMan;
    }

    // ============================================================
    // Getter / Reader / Check methods (single pattern only)
    // ============================================================

    public function checkStopPattern(string $pattern): bool
    {
        return $this->stop_skip_pattern_states['stop'][$pattern] ?? false;
    }

    public function checkSkipPattern(string $pattern): bool
    {
        return $this->stop_skip_pattern_states['skip'][$pattern] ?? false;
    }

    // ============================================================
    // Setter / Writer / Toggle / Enable / Disable methods (multi-handling)
    // ============================================================

    /**
     * Toggle one or more stop patterns.
     * Accepts string (single) or array (multiple).
     */
    public function toggleStopPatterns(string|array $patterns, ?bool $force = null): static
    {
        foreach ((array)$patterns as $pattern) {
            $current = $this->stop_skip_pattern_states['stop'][$pattern] ?? true;
            $this->stop_skip_pattern_states['stop'][$pattern] = $force !== null ? (bool)$force : !$current;
        }
        return $this;
    }

    /**
     * Explicitly enable one or more stop patterns.
     */
    public function enableStopPatterns(string|array $patterns): static
    {
        foreach ((array)$patterns as $pattern) {
            $this->stop_skip_pattern_states['stop'][$pattern] = true;
        }
        return $this;
    }

    /**
     * Explicitly disable one or more stop patterns.
     */
    public function disableStopPatterns(string|array $patterns): static
    {
        foreach ((array)$patterns as $pattern) {
            $this->stop_skip_pattern_states['stop'][$pattern] = false;
        }
        return $this;
    }

    /**
     * Toggle one or more skip patterns.
     */
    public function toggleSkipPatterns(string|array $patterns, ?bool $force = null): static
    {
        foreach ((array)$patterns as $pattern) {
            $current = $this->stop_skip_pattern_states['skip'][$pattern] ?? true;
            $this->stop_skip_pattern_states['skip'][$pattern] = $force !== null ? (bool)$force : !$current;
        }
        return $this;
    }

    /**
     * Explicitly enable one or more skip patterns.
     */
    public function enableSkipPatterns(string|array $patterns): static
    {
        foreach ((array)$patterns as $pattern) {
            $this->stop_skip_pattern_states['skip'][$pattern] = true;
        }
        return $this;
    }

    /**
     * Explicitly disable one or more skip patterns.
     */
    public function disableSkipPatterns(string|array $patterns): static
    {
        foreach ((array)$patterns as $pattern) {
            $this->stop_skip_pattern_states['skip'][$pattern] = false;
        }
        return $this;
    }


    // Newer But/Hut Commented for Now :
    /******
     * Remove one or more stop patterns dynamically.
     *
     * Accepts:
     *  - multiple arguments (strings or regexes)
     *  - a single array of patterns
     *  - '*' or E_ALL to clear all
     *
     * @param mixed ...$patterns
     * @return static
     * /
    public function removeStopPatterns(mixed ...$patterns): static
    {
        // If the first argument is an array, flatten it
        if (count($patterns) === 1 && is_array($patterns[0])) {
            $patterns = $patterns[0];
        }

        // Wildcard or E_ALL means: clear everything
        if (in_array('*', $patterns, true) || in_array(E_ALL, $patterns, true)) {
            $this->stopPatterns = [];
            return $this;
        }

        // Filter out the ones to remove
        $this->stopPatterns = array_values(array_filter(
            $this->stopPatterns,
            fn($pattern) => !in_array($pattern, $patterns, true)
        ));

        return $this;
    }

    /*******
     * Remove one or more skip patterns dynamically.
     *
     * Accepts:
     *  - multiple arguments (strings or regexes)
     *  - a single array of patterns
     *  - '*' or E_ALL to clear all
     *
     * @param mixed ...$patterns
     * @return static
     * /
    public function removeSkipPatterns(mixed ...$patterns): static
    {
        // If the first argument is an array, flatten it
        if (count($patterns) === 1 && is_array($patterns[0])) {
            $patterns = $patterns[0];
        }

        // Wildcard or E_ALL means: clear everything
        if (in_array('*', $patterns, true) || in_array(E_ALL, $patterns, true)) {
            $this->skipPatterns = [];
            return $this;
        }

        // Filter out selected ones
        $this->skipPatterns = array_values(array_filter(
            $this->skipPatterns,
            fn($pattern) => !in_array($pattern, $patterns, true)
        ));

        return $this;
    } */
}
