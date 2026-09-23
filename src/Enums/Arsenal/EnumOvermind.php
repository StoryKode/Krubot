<?php

declare(strict_types=1);

namespace KrubiK\Enums;
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

use KrubiK\Antimatter\OverMind;

/**
 * 👽 EnumOvermind: The Enum-Specific Symbiote Variant
 *
 * A specialized variant of the Overmind symbiote, pre-configured to exclusively
 * target and bond with PHP Enums. All bonding operations performed via this class
 * will default to the 'Enums' host strain, ensuring a clean, isolated ecosystem
 * for Enum augmentations without affecting other entity types.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.9×
 * @license MIT
*/
final class EnumOvermind extends OverMind
{
    /**
    * 🧬 ACTIVE HOST STRAIN — Hardcoded to target only Enums.
    *
    * @var string
    */
    protected static string $activeStrain = 'Enums';
}