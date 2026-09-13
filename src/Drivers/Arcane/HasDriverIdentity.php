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
    protected string $enforcerCodeName = 'common';

    /**
     * The operational regiment/unit scope where this enforcer operates.
     * Immutable: Can only be set once during initialization and cannot be null.
     * @var string
    */
    protected readonly string $regiment; // ?string

    /**
     * Set the driver's operational alias.
     * Used by KrubotManager to stamp the identity.
     *
     * @param string $alias
     * @return $this
    */
    public function assignCodeName(string $alias): static
    {
        $this->enforcerCodeName = $alias;
        return $this;
    }

    /**
     * Get the driver's identity.
    */
    public function getCodeName(): string
    {
        return $this->enforcerCodeName;
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
        return $this->enforcerCodeName;
    }

    /**
     * ⚡️ IMMUTABLE REGIMENT INITIALIZER (Team / Unit Assignment)
     * 
     * Sets the operational regiment/team for this enforcer. 
     * Designed to be called once during the constructor initialization phase.
     * 
     * @param string $regiment The target regiment or team name.
     * @return $this
    */
    public function setTeam(string $regiment): static
    {
        // If not initialized yet, set the readonly property
        if (!isset($this->regiment)) {
            $this->regiment = $regiment;
        }
        
        return $this;
    }

    /**
     * Alias for setTeam() for absolute linguistic flexibility.
     * 
     * @param string $regiment
     * @return $this
    */
    public function assignTo(string $regiment): static
    {
        return $this->setTeam($regiment);
    }

    /**
     * ⚡️ IMMUTABLE REGIMENT GETTER (Zero-Mutation Policy)
     * 
     * Returns the strict regiment scope assigned to this enforcer instance.
     * Since `$regimentScope` is readonly, this acts purely as a getter.
     *
     * @return string
    */
    public function regiment(): string
    {
        return $this->regiment;
    }

    /**
     * Alias for regiment() for absolute linguistic flexibility.
     *
     * @return string
    */
    public function operative(): string
    {
        return $this->regiment;
    }

    /**
     * Alias for regiment() for absolute linguistic flexibility.
     *
     * @return string
    */
    public function team(): string
    {
        return $this->regiment;
    }
}
