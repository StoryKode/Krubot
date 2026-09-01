<?php
// File: src/KrubiK/Render/DTOs/BaseObject.php

declare(strict_types=1);

namespace KrubiK\Render\DTOs;

use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\Arcane\HasHyperIntrospection;

/**
 * The base class for all Telegram API model objects.
 * It extends RichEntity to inherit serialization helpers like
 * normalize() and filterEmpty(), and requires all concrete models
 * to implement their own toArray() method.
*/
abstract class BaseObject extends RichEntity
{
    /**
     * A simple factory method to create a new instance of the model.
     * This provides a consistent way to instantiate objects across the SDK.
     *
     * @param mixed ...$args The arguments to pass to the object's constructor.
     * @return static A new instance of the called class.
    */
    public static function make(...$args): static
    {
        return new static(...$args);
    }

    /**
     * Provides the array representation of this object.
     * This method must be implemented by any concrete class extending this abstract class.
     * It defines what data will be used by the toHtml() method for generating the beautiful Symfony dump.
     *
     * @return array<string, mixed>
    */
    abstract public function toArray(): array;

    /**
     * adds toHtml():string method that Dumps the object's array representation
     * Into an HTML string.
     * This method leverages Symfony's VarDumper component to generate the rich,
     * interactive output without halting php script execution.
    */
    use HasHyperIntrospection;
}
