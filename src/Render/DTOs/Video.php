<?php
// File: src/KrubiK/Render/DTOs/Video.php

declare(strict_types=1);

namespace KrubiK\Render\DTOs;

use DateTimeInterface;

/**
 * This object represents a video file.
 *
 * Source: https://core.telegram.org/bots/api#video
 */
class Video extends BaseObject
{
    /**
     * @param string $file_id Identifier for this file, which can be used to download or reuse the file.
     * @param string $file_unique_id Unique identifier for this file, which is supposed to be the same over time and for different bots. Can't be used to download or reuse the file.
     * @param int $width Video width as defined by the sender.
     * @param int $height Video height as defined by the sender.
     * @param int $duration Duration of the video in seconds as defined by the sender.
     * @param PhotoSize|null $thumbnail Optional. Video thumbnail.
     * @param PhotoSize[]|null $cover Optional. Available sizes of the cover of the video in the message.
     * @param DateTimeInterface|null $start_timestamp Optional. Timestamp in seconds from which the video will play in the message.
     * @param VideoQuality[]|null $qualities Optional. List of available qualities of the video.
     * @param string|null $file_name Optional. Original filename as defined by the sender.
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
        protected ?array $cover = null,
        protected ?DateTimeInterface $start_timestamp = null,
        protected ?array $qualities = null,
        protected ?string $file_name = null,
        protected ?string $mime_type = null,
        protected ?int $file_size = null,
        protected ?string $file_url = null // custom url for rendering on web
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
    public function thumbnail(?PhotoSize $value = null): static|PhotoSize|null
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
     * [Hi-DX] Fluent getter/setter for the 'cover' property.
     *
     * @param array|null $value If provided, sets the 'cover'. Otherwise, returns the current value.
     * @return static|array|null The instance for chaining (setter) or the value (getter).
     */
    public function cover(?array $value = null): static|array|null
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->cover = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->cover;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'start_timestamp' property.
     *
     * @param DateTimeInterface|null $value If provided, sets the 'start_timestamp'. Otherwise, returns the current value.
     * @return static|DateTimeInterface|null The instance for chaining (setter) or the value (getter).
     */
    public function startTimestamp(?DateTimeInterface $value = null): static|DateTimeInterface|null
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->start_timestamp = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->start_timestamp;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'qualities' property.
     *
     * @param array|null $value If provided, sets the 'qualities'. Otherwise, returns the current value.
     * @return static|array|null The instance for chaining (setter) or the value (getter).
     */
    public function qualities(?array $value = null): static|array|null
    {
        // Check if the method was called with an argument.
        if (func_num_args() > 0) {
            // This is a 'setter' call.
            $this->qualities = $value;
            // Return the instance to allow method chaining.
            return $this;
        }
        // This is a 'getter' call.
        return $this->qualities;
    }

    /**
     * [Hi-DX] Fluent getter/setter for the 'file_name' property.
     *
     * @param string|null $value If provided, sets the 'file_name'. Otherwise, returns the current value.
     * @return static|string|null The instance for chaining (setter) or the value (getter).
     */
    public function fileName(?string $value = null): static|string|null
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
    public function mimeType(?string $value = null): static|string|null
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
    public function fileSize(?int $value = null): static|int|null
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
            'cover' => $this->normalize($this->cover),
            'start_timestamp' => $this->normalize($this->start_timestamp),
            'qualities' => $this->normalize($this->qualities),
            'file_name' => $this->file_name,
            'mime_type' => $this->mime_type,
            'file_size' => $this->file_size,
        ]);
    }
}
