<?php
// File: src/KrubiK/Render/DTOs/Animation.php

declare(strict_types=1);

namespace KrubiK\Render\DTOs;

/**
 * This object represents an animation file (GIF or H.264/MPEG-4 AVC video without sound).
 *
 * Source: https://core.telegram.org/bots/api#animation
 */
class Animation extends BaseObject
{
    /**
     * @param string $file_id Identifier for this file, which can be used to download or reuse the file.
     * @param string $file_unique_id Unique identifier for this file, which is supposed to be the same over time and for different bots. Can't be used to download or reuse the file.
     * @param int $width Video width as defined by the sender.
     * @param int $height Video height as defined by the sender.
     * @param int $duration Duration of the video in seconds as defined by the sender.
     * @param PhotoSize|null $thumbnail Optional. Animation thumbnail as defined by the sender.
     * @param string|null $file_name Optional. Original animation filename as defined by the sender.
     * @param string|null $mime_type Optional. MIME type of the file as defined by the sender.
     * @param int|null $file_size Optional. File size in bytes. It can be bigger than 2^31.
     */
    public function __construct(
        protected string $file_id,
        protected string $file_unique_id,
        protected int $width,
        protected int $height,
        protected int $duration,
        protected ?PhotoSize $thumbnail = null,
        protected ?string $file_name = null,
        protected ?string $mime_type = null,
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
     * [Hi-DX] Fluent getter/setter for the 'duration' property.
     *
     * @param int|null $value If provided, sets the 'duration'. Otherwise, returns the current value.
     * @return static|int The instance for chaining (setter) or the value (getter).
     */
    public function duration(?int $value = null): static|int
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->duration = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->duration;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'thumbnail' property.
     *
     * @param PhotoSize|null $value If provided, sets the 'thumbnail'. Otherwise, returns the current value.
     * @return static|PhotoSize|null The instance for chaining (setter) or the value (getter).
     */
    public function thumbnail(?PhotoSize $value = null): static|?PhotoSize
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->thumbnail = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->thumbnail;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'file_name' property.
     *
     * @param string|null $value If provided, sets the 'file_name'. Otherwise, returns the current value.
     * @return static|string|null The instance for chaining (setter) or the value (getter).
     */
    public function fileName(?string $value = null): static|?string
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->file_name = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->file_name;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'mime_type' property.
     *
     * @param string|null $value If provided, sets the 'mime_type'. Otherwise, returns the current value.
     * @return static|string|null The instance for chaining (setter) or the value (getter).
     */
    public function mimeType(?string $value = null): static|?string
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->mime_type = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->mime_type;
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
            'duration' => $this->duration,
            'thumbnail' => $this->normalize($this->thumbnail),
            'file_name' => $this->file_name,
            'mime_type' => $this->mime_type,
            'file_size' => $this->file_size,
        ]);
    }
}
