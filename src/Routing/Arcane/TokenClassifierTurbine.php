<?php

namespace KrubiK\Routing\Arcane;
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
 * TokenClassifier Turbine 🧬
 *
 * The shared bifurcation engine for #[Access] and #[Block].
 * Splits a flat list of tokens into "Role Names" and "User IDs"
 * using a lightning-fast, allocation-light heuristic.
 *
 * Heuristic (The Bifurcation Law):
 *   count_digits > count_non_digits  →  USER_ID
 *   otherwise                        →  ROLE NAME
 *
 * Why not "is_numeric()"?
 *   Iranian messengers (Bale, Rubika) frequently expose string-typed IDs,
 *   and some legitimate Role names embed digits (e.g. "admin2", "vip_2024").
 *   A pure "is_numeric" test would misclassify both.
 *
 * Why not "ctype_digit()"?
 *   Same reason — plus it fails on mixed tokens like "-1001234567"
 *   (Telegram supergroup IDs) which are still, semantically, User IDs.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.9×
 * @license MIT
*/
trait TokenClassifierTurbine
{
    /**
     * 🚀 The Bifurcation Probe.
     *
     * Implementation notes for the performance-obsessed:
     *   - `str_replace()` with an array of single chars runs a SINGLE C-level
     *     pass over the token and strips every digit at once.
     *   - `strlen()` in PHP is O(1) — the length lives in the zend_string header.
     *   - No regex, no VM loop, no array allocation beyond the result string.
     *
     * @param string $token A flattened, sanitized access token.
     * @return bool TRUE if the token looks like a User ID, FALSE if it looks like a Role.
    */
    public static function looksLikeUserId(string $token): bool
    {
        // Strip every ASCII digit in ONE native pass.  ← the hot path
        $letters = str_replace(
            ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
            '',
            $token
        );

        $totalDigits  = strlen($token) - strlen($letters);
        $totalLetters = strlen($letters);

        // Tie goes to ROLE (fail-safe: false-positive on Role is safer).
        return $totalDigits > $totalLetters;
    }

    /**
     * 🧬 Bifurcate a flat list of tokens into [roles, userIds].
     *
     * De-duplicates each bucket independently and re-indexes for
     * predictable, deterministic iteration order.
     *
     * @param string[] $tokens A flat, already-flattened list of tokens.
     * @return array{0: string[], 1: string[]} [roles, userIds]
    */
    public static function bifurcateTokens(array $tokens): array
    {
        $roles   = [];
        $userIds = [];

        foreach ($tokens as $token) {
            if (self::looksLikeUserId($token)) {
                $userIds[] = $token;
            } else {
                $roles[] = $token;
            }
        }

        return [
            array_values(array_unique($roles)),
            array_values(array_unique($userIds)),
        ];
    }

    public static function splitTokens(array $tokens): array
    {
        return static::bifurcateTokens($tokens);
    }
}

