<?php

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
use BackedEnum;
use UnitEnum;
use KrubiK\Routing\Arcane\TokenClassifierTurbine;

/**
 * The Block attribute is the ABSOLUTE VETO of Krubot's Access Control System.
 *
 * Where #[Access] grants entry, #[Block] slams the door.
 * The two are NOT symetrical opposites:
 *
 *   ┌──────────────────────────────────────────────────────────┐
 *   │  #[Access('member')]  → "You MUST have member to enter." │
 *   │  #[Block('banned')]  → "If you have banned, you're OUT." │
 *   │                                                          │
 *   │  Block ALWAYS wins over Access. Veto > Grant.            │
 *   └──────────────────────────────────────────────────────────┘
 *
 * It is repeatable, so you can stack multiple Block declarations.
 *
 * @example #[Block(Role::Banned)]
 * @example #[Block('banned', 'suspended')]
 * @example #[Block(Role::Banned, ['shadow_banned', 'rate_limited'])]
 *
 * @author DoKtor K.
 * @link https://StoryKo.de
 * @version Krubot: ×RC.9×
 * @license MIT
*/
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Block
{
    use TokenClassifierTurbine;

   /**
     * @var string[] A de-duplicated, flattened list of forbidden role names.
     */
    public readonly array $roles;

    /**
     * @var string[] A de-duplicated, flattened list of forbidden USER IDs.
    */
    public readonly array $userIds;

    /**
     * Block constructor.
     *
     * Mirrors the Access attribute's signature exactly — same flexibility,
     * same recursion, same sanitization. This is intentional: the two
     * attributes should feel like two sides of the same coin.
     *
     * @param string|BackedEnum|UnitEnum|array<int, string|BackedEnum|UnitEnum> ...$roles The roles to forbid.
     */
    public function __construct(string|BackedEnum|UnitEnum|array ...$roles)
    {
        $flattened = [];

        array_walk_recursive($roles, function ($role) use (&$flattened) {

            // BackedEnum → extract its scalar value (works for int- & string-backed).
            if ($role instanceof BackedEnum) {
                $flattened[] = (string) $role->value;
            }
            // UnitEnum → use the case name (no ->value exists).
            elseif ($role instanceof UnitEnum) {
                $flattened[] = $role->name;
            }
            // Plain string → pass-through.
            elseif (is_string($role)) {
                $flattened[] = $role;
            }
        });

        // 🧬 The Bifurcation: split tokens into [roles, userIds].
        [$this->roles, $this->userIds] = self::bifurcateTokens($flattened);

        // De-duplicate & re-index for predictable, deterministic iteration.
        // $this->roles = array_values(array_unique($flattened));
    }
}
