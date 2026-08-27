<?php

namespace KrubiK\Drivers\Contracts;
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
 * The foundational contract for all drivers.
 *
 * This interface defines the essential methods a driver must implement to interact
 * with its specific platform's API. It ensures that Krubot can
 * communicate with any driver in a standardized way.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
 */
interface MultiverseEnforcer
{

    public function setDriverAlias(string $alias): static;
    public function getDriverAlias(): string;
    public function setName(string $name): static;
    public function getName(): string;

}
