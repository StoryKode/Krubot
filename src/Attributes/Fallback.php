<?php

namespace KrubiK\Attributes;
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

use Attribute;

/**
 * Marks a method as the global fallback handler.
 * This method will be executed if NO other route or type-specific fallback matches the incoming message.
 * There should only be one active #[Fallback] attribute across all integrated Nexuses.
*/
#[Attribute(Attribute::TARGET_METHOD)]
class Fallback
{
    // This attribute is a simple marker; its presence is its purpose.
    // It requires no constructor parameters.
}
