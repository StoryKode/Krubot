<?php

namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;

class RichTextDateTime extends RichTextEntity
{
    public function __construct(public RichEntity|string|array $text, public int $unix_time, public string $date_time_format) {}
    /**
     * Static factory to create a new RichTextDateTime instance.
     *
     * @param RichEntity|callable|string|array $text The visible text.
     * @param int $unixTime The timestamp in Unix epoch format.
     * @param string $dateTimeFormat A string describing the format.
     * @return self
    */
    public static function make(RichEntity|callable|string|array $text, int $unixTime, string $dateTimeFormat): self
    {
        // Resolve the display text, as it might be a closure.
        return new self(self::resolveContent($text), $unixTime, $dateTimeFormat);
    }

    public function toArray(): array { return ['type' => 'date_time', 'text' => $this->normalize($this->text), 'unix_time' => $this->unix_time, 'date_time_format' => $this->date_time_format]; }
    public function toHtml(): string
    {
        // Renders a custom <tg-time> tag with unix timestamp and format.
        $escapedFormat = $this->esc($this->date_time_format);
        return '<tg-time unix="' . $this->unix_time . '" format="' . $escapedFormat . '">' . $this->renderHtml($this->text) . '</tg-time>';
    }
}
