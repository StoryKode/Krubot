<?php

namespace KrubiK\Drivers\Arcane;
/*
| Krubot BotEngine: The Architect's Lexicon [×RC.8×] 🚀📜
|--------------------------------------------------------------------------
| This is **a Playground For Mastery**, a laboratory of ***Software Dev Artistry***;
| not a weapon for production's final battles.
|
| Our Bond: ***"Rebuilding The Rebellion"*** Within S.N.P. (The Foundation of Pure Power & Revel).
| Your Mandate [MIT]: Deconstruct Krubot. Command it. Master it. You are The Architect Now!
|
| *Go build something revolutionary!* 💜⚡️
*/

trait HasDriverIdentity
{
    /**
     * The identity of this driver instance (e.g., 'rubika', 'web', 'telegram').
     * @var string
    */
    protected string $driverCodeName = 'common';

    /**
     * Set the driver's operational alias.
     * Used by KrubotManager to stamp the identity.
     *
     * @param string $alias
     * @return $this
    */
    public function assignCodeName(string $alias): static
    {
        $this->driverCodeName = $alias;
        return $this;
    }

    /**
     * Get the driver's identity.
    */
    public function getCodeName(): string
    {
        return $this->driverCodeName;
    }

    /**
     * Legacy Alias for UniChatKit compatibility or alternate naming.
    */
    public function setName(string $name): static
    {
        return $this->assignCodeName($name);
    }

    /**
     * UniChatKit compatible getter.
    */
    public function getName(): string
    {
        return $this->driverCodeName;
    }
}
