<?php
// File: src/KrubiK/Render/DTOs/Location.php

declare(strict_types=1);

namespace KrubiK\Render\DTOs;

/**
 * This object represents a point on the map.
 *
 * Source: https://core.telegram.org/bots/api#location
 */
class Location extends BaseObject
{
    /**
     * @param float $latitude Latitude as defined by the sender.
     * @param float $longitude Longitude as defined by the sender.
     * @param float|null $horizontal_accuracy Optional. The radius of uncertainty for the location, measured in meters; 0-1500.
     * @param int|null $live_period Optional. Time relative to the message sending date, during which the location can be updated; in seconds. For active live locations only.
     * @param int|null $heading Optional. The direction in which user is moving, in degrees; 1-360. For active live locations only.
     * @param int|null $proximity_alert_radius Optional. The maximum distance for proximity alerts about approaching another chat member, in meters. For sent live locations only.
     */
    public function __construct(
        protected float $latitude,
        protected float $longitude,
        protected ?float $horizontal_accuracy = null,
        protected ?int $live_period = null,
        protected ?int $heading = null,
        protected ?int $proximity_alert_radius = null
    ) {}

    // [Hi-DX] Auto-Generated Methods

    /**
     * [Hi-DX] Fluent getter/setter for the 'latitude' property.
     *
     * @param float|null $value If provided, sets the 'latitude'. Otherwise, returns the current value.
     * @return static|float The instance for chaining (setter) or the value (getter).
     */
    public function latitude(?float $value = null): static|float
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->latitude = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->latitude;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'longitude' property.
     *
     * @param float|null $value If provided, sets the 'longitude'. Otherwise, returns the current value.
     * @return static|float The instance for chaining (setter) or the value (getter).
     */
    public function longitude(?float $value = null): static|float
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->longitude = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->longitude;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'horizontal_accuracy' property.
     *
     * @param float|null $value If provided, sets the 'horizontal_accuracy'. Otherwise, returns the current value.
     * @return static|float|null The instance for chaining (setter) or the value (getter).
     */
    public function horizontalAccuracy(?float $value = null): static|?float
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->horizontal_accuracy = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->horizontal_accuracy;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'live_period' property.
     *
     * @param int|null $value If provided, sets the 'live_period'. Otherwise, returns the current value.
     * @return static|int|null The instance for chaining (setter) or the value (getter).
     */
    public function livePeriod(?int $value = null): static|?int
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->live_period = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->live_period;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'heading' property.
     *
     * @param int|null $value If provided, sets the 'heading'. Otherwise, returns the current value.
     * @return static|int|null The instance for chaining (setter) or the value (getter).
     */
    public function heading(?int $value = null): static|?int
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->heading = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->heading;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'proximity_alert_radius' property.
     *
     * @param int|null $value If provided, sets the 'proximity_alert_radius'. Otherwise, returns the current value.
     * @return static|int|null The instance for chaining (setter) or the value (getter).
     */
    public function proximityAlertRadius(?int $value = null): static|?int
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->proximity_alert_radius = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->proximity_alert_radius;
    }

    // End [Hi-DX] Auto-Generated Methods
   
    /**
     * Factory method to create an instance from a raw array.
     *
     * @param array{latitude: float, longitude: float} $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static(
            latitude: $data['latitude'],
            longitude: $data['longitude']
        );
    }

    public function toArray(): array
    {
        return $this->filterEmpty([
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'horizontal_accuracy' => $this->horizontal_accuracy,
            'live_period' => $this->live_period,
            'heading' => $this->heading,
            'proximity_alert_radius' => $this->proximity_alert_radius,
        ]);
    }
}
