<?php
// File: src/KrubiK/Render/DTOs/LocationAddress.php

declare(strict_types=1);

namespace KrubiK\Render\DTOs;

/**
 * Describes the physical address of a location.
 *
 * Source: https://core.telegram.org/bots/api#locationaddress
 */
class LocationAddress extends BaseObject
{
    /**
     * @param string $country_code The two-letter ISO 3166-1 alpha-2 country code of the country where the location is located.
     * @param string|null $state Optional. State of the location.
     * @param string|null $city Optional. City of the location.
     * @param string|null $street Optional. Street address of the location.
     */
    public function __construct(
        protected string $country_code,
        protected ?string $state = null,
        protected ?string $city = null,
        protected ?string $street = null
    ) {}

    // [Hi-DX] Auto-Generated Methods

    /**
     * [Hi-DX] Fluent getter/setter for the 'country_code' property.
     *
     * @param string|null $value If provided, sets the 'country_code'. Otherwise, returns the current value.
     * @return static|string The instance for chaining (setter) or the value (getter).
     */
    public function countryCode(?string $value = null): static|string
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->country_code = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->country_code;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'state' property.
     *
     * @param string|null $value If provided, sets the 'state'. Otherwise, returns the current value.
     * @return static|string|null The instance for chaining (setter) or the value (getter).
     */
    public function state(?string $value = null): static|?string
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->state = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->state;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'city' property.
     *
     * @param string|null $value If provided, sets the 'city'. Otherwise, returns the current value.
     * @return static|string|null The instance for chaining (setter) or the value (getter).
     */
    public function city(?string $value = null): static|?string
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->city = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->city;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'street' property.
     *
     * @param string|null $value If provided, sets the 'street'. Otherwise, returns the current value.
     * @return static|string|null The instance for chaining (setter) or the value (getter).
     */
    public function street(?string $value = null): static|?string
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->street = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->street;
    }

    // End [Hi-DX] Auto-Generated Methods

    public function toArray(): array
    {
        return $this->filterEmpty([
            'country_code' => $this->country_code,
            'state' => $this->state,
            'city' => $this->city,
            'street' => $this->street,
        ]);
    }
}
