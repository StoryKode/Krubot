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

    // Renders a custom <{tg-}? time> tag with unix timestamp and format, so JS can reformat to local timezone.
    public function toHtml(): string
    {
        $tagString = $this->targetsTelegram() ? 'tg-time' : 'time';

        // Define attributes declaratively for our helper.
        $attributes = [
            'class'   => 'richy-datetime',
            'datetime'=> date('c', $this->unixTime),
            'unix'    => ((int)$this->unixTime),
            'format'  => $this->dateTimeFormat
        ];

        // Let the central helper handle escaping and string conversion.
        $attrString = $this->attributesToString($attributes);

        // Assemble the final tag.
        return "<{$tagString} {$attrString}>" . $this->renderHtml($this->text) . "</{$tagString}>";
    }

    // TG Extended Markdown — Renders as interactive date widget in TG clients.
    public function toMd(): string
    {
        $label  = $this->renderText($this->text);
        $url    = 'tg://time?unix=' . (int)$this->unixTime . '&format=' . urlencode($this->dateTimeFormat);
        $safeUrl = str_replace(['\\', ')'], ['\\\\', '\\)'], $url);
        return '![' . $label . '](' . $safeUrl . ')';
    }
}
