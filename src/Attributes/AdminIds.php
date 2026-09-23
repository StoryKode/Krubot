<?php

declare(strict_types=1);

namespace KrubiK\Attributes;
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

use Attribute;

/**
 * Hyper-DX Admin Restriction Attribute
 *
 * Apply on Nexus classes or individual methods to whitelist admin access.
 * Fully repeatable — stack multiple instances or pass everything in one shot.
 *
 * Usage examples (DX heaven):
 *
 *   #[AdminIds(123456789, 987654321)]
 *   #[AdminIds(['tg_111', 'bale_222', 333])]
 *   #[AdminIds(123)] #[AdminIds('456')]          // repeatable stacking
 *   #[AdminIds(123, '456', ...$dynamicAdmins)]
 *
 * Design philosophy (zero-cost at runtime):
 * - Accepts int|string|array mixed freely
 * - Normalizes everything to string once (platform-agnostic — some platforms store IDs as string)
 * - Deduplicates with a pure hashset (O(n) single pass)
 * - No reflection, no heavy validation, no side effects in constructor
 * - Dispatcher later does O(1) lookup via isset($hash[$userId])
 *
 * @author DoKtor K.
 * @link https://StoryKo.de
 * @version Krubot: ×RC.9×
 * @license MIT
*/
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class AdminIds
{
    /** @var list<string> */
    public readonly array $ids;

    /**
     * @param int|string|array<int|string> ...$adminIds
    */
    public function __construct(int|string|array ...$adminIds)
    {
        $hash = [];

        foreach ($adminIds as $item) {
            if (is_array($item)) {
                foreach ($item as $id) {
                    $hash[(string) $id] = true;
                }
            } else {
                $hash[(string) $item] = true;
            }
        }

        // Preserve insertion order while guaranteeing uniqueness
        $this->ids = array_keys($hash);
    }

    /**
     * Ultra-fast membership check.
     * Call this from your Dispatcher — pure O(1) after the first construction.
    */
    public function allows(int|string $userId): bool
    {
        return in_array((string) $userId, $this->ids, true);
    }

    /**
     * Merge multiple AdminIds attributes into a single unique list.
     * Scanner / Dispatcher uses this when both class-level and method-level
     * attributes exist (method-level usually wins or is OR-merged).
     *
     * @param list<self> $attributes
     * @return list<string>
    */
    public static function merge(array $attributes): array
    {
        $hash = [];

        foreach ($attributes as $attr) {
            foreach ($attr->ids as $id) {
                $hash[$id] = true;
            }
        }

        return array_keys($hash);
    }
}