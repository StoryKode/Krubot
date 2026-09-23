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
use BackedEnum; // Leverage PHP's native BackedEnum interface for robust, type-safe role definitions.
use UnitEnum;
use KrubiK\Routing\Arcane\TokenClassifierTurbine;

/**
 * The Access attribute provides a declarative way to control access to classes and methods
 * based on user roles. It's repeatable, allowing for flexible permission stacking.
 *
 * @example #[Access(Role::Admin)]
 * @example #[Access('admin', 'editor')]
 * @example #[Access(Role::Admin, ['editor', 'viewer'])]
 *
 * @author DoKtor K.
 * @link https://StoryKo.de
 * @version Krubot: ×RC.9×
 * @license MIT
*/
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Access
{
    use TokenClassifierTurbine;

    /**
     * @var string[] A de-duplicated, flattened list of required role names.
     */
    public readonly array $roles;

    /**
     * @var string[] A de-duplicated, flattened list of required USER IDs.
     *
     * Stored as strings for uniform comparison across platforms
     * (Telegram/Bale/Rubika may expose IDs as either int or string).
    */
    public readonly array $userIds;

    /**
     * Access constructor.
     *
     * This is where the magic happens! It accepts a flexible, dynamic mix of roles.
     * You can pass roles as individual strings, Enums, or even nested arrays.
     *
     * @param string|BackedEnum|UnitEnum|array<int, string|BackedEnum|UnitEnum> ...$roles The roles to grant access.
    */
    public function __construct(string|BackedEnum|UnitEnum|array ...$roles)
    {
        $flattened = [];

        // The powerhouse: Recursively flattens any input structure (arrays, spreads, etc.).
        array_walk_recursive($roles, function ($role) use (&$flattened) {

            // Got a BackedEnum? Sweet! Let's extract its value.
            if ($role instanceof BackedEnum) {
                // We cast to string to seamlessly handle both string and int-backed enums.
                $flattened[] = (string) $role->value;
            }
            // Handle UnitEnums (which don't have a ->value) by using their case name.
            elseif ($role instanceof UnitEnum) {
                $flattened[] = $role->name;
            }
            // Just a plain old string? Add it to the list.
            elseif (is_string($role)) {
                $flattened[] = $role;
            }
        });

        [$this->roles, $this->userIds] = self::bifurcateTokens($flattened);

        // Sanitize and finalize the list: remove duplicates and re-index the array for clean, predictable access.
        // $this->roles = array_values(array_unique($flattened));
    }
}
