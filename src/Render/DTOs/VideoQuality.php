<?php
// File: src/KrubiK/Render/DTOs/VideoQuality.php

declare(strict_types=1);

namespace KrubiK\Render\DTOs;

/**
 * This object represents a video file of a specific quality.
 *
 * Source: https://core.telegram.org/bots/api#videoquality
 */
class VideoQuality extends BaseObject
{
    /**
     * @param string $file_id Identifier for this file, which can be used to download or reuse the file.
     * @param string $file_unique_id Unique identifier for this file, which is supposed to be the same over time and for different bots. Can't be used to download or reuse the file.
     * @param int $width Video width.
     * @param int $height Video height.
     * @param string $codec Codec that was used to encode the video, for example, 'h264', 'h265', or 'av01'.
     * @param int|null $file_size Optional. File size in bytes. It can be bigger than 2^31.
     */
    public function __construct(
        protected string $file_id,
        protected string $file_unique_id,
        protected int $width,
        protected int $height,
        protected string $codec,
        protected ?int $file_size = null
    ) {}

    // [Hi-DX] Auto-Generated Methods

    /**
     * [Hi-DX] Fluent getter/setter for the 'file_id' property.
     *
     * @param string|null $value If provided, sets the 'file_id'. Otherwise, returns the current value.
     * @return static|string The instance for chaining (setter) or the value (getter).
     */
    public function fileId(?string $value = null): static|string
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->file_id = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->file_id;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'file_unique_id' property.
     *
     * @param string|null $value If provided, sets the 'file_unique_id'. Otherwise, returns the current value.
     * @return static|string The instance for chaining (setter) or the value (getter).
     */
    public function fileUniqueId(?string $value = null): static|string
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->file_unique_id = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->file_unique_id;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'width' property.
     *
     * @param int|null $value If provided, sets the 'width'. Otherwise, returns the current value.
     * @return static|int The instance for chaining (setter) or the value (getter).
     */
    public function width(?int $value = null): static|int
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->width = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->width;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'height' property.
     *
     * @param int|null $value If provided, sets the 'height'. Otherwise, returns the current value.
     * @return static|int The instance for chaining (setter) or the value (getter).
     */
    public function height(?int $value = null): static|int
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->height = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->height;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'codec' property.
     *
     * @param string|null $value If provided, sets the 'codec'. Otherwise, returns the current value.
     * @return static|string The instance for chaining (setter) or the value (getter).
     */
    public function codec(?string $value = null): static|string
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->codec = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->codec;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'file_size' property.
     *
     * @param int|null $value If provided, sets the 'file_size'. Otherwise, returns the current value.
     * @return static|int|null The instance for chaining (setter) or the value (getter).
     */
    public function fileSize(?int $value = null): static|?int
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->file_size = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->file_size;
    }

    // End [Hi-DX] Auto-Generated Methods

    public function toArray(): array
    {
        return $this->filterEmpty([
            'file_id' => $this->file_id,
            'file_unique_id' => $this->file_unique_id,
            'width' => $this->width,
            'height' => $this->height,
            'codec' => $this->codec,
            'file_size' => $this->file_size,
        ]);
    }
}
