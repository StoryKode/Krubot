<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;
use InvalidArgumentException;

class RichTextHashtag extends RichTextSymbolLink
{
    public string $hashtag;

    /**
     * HyperDX Constructor for RichTextHashtag.
     *
     * - `make('Laravel')`: Automatically becomes text="#Laravel", hashtag="#Laravel".
     * - `make('#Laravel')`: Correctly parsed as text="#Laravel", hashtag="#Laravel".
     * - `make('My Tag', '#laravel')`: Uses provided values directly.
     *
     * @param RichEntity|string|array|null $text The display text.
     * @param string|null $hashtag The functional hashtag value.
     */
    public function __construct(
        RichEntity|string|array|null $text = null,
        ?string $hashtag = null
    ) {
        if ($text === null && $hashtag === null) {
            throw new InvalidArgumentException('RichTextHashtag requires at least one argument.');
        }

        $isSingleArgMode = ($text === null || $hashtag === null);
        
        // Determine the final values based on the input.
        $finalHashtag = $hashtag ?? $text;
        $finalText = $text ?? $finalHashtag;

        // Apply auto-prepending logic only in single-argument mode and if the feature is enabled.
        if ($isSingleArgMode && self::$autoPrependSymbol && is_string($finalHashtag) && !str_starts_with($finalHashtag, $this->getSymbol())) {
            $finalHashtag = $this->getSymbol() . $finalHashtag;
            
            // If the text was derived from the hashtag, update it as well to stay in sync.
            if ($text === null) {
                $finalText = $finalHashtag;
            }
        }
        
        // --- Final Property Assignment ---
        $this->hashtag = $finalHashtag;
        
        // Manually initialize parent properties, bypassing the parent constructor.
        $this->text = $finalText;
        $this->value = ltrim($this->hashtag, $this->getSymbol());
    }

    /**
     * Static factory for creating a new RichTextHashtag instance with full type-hinting and closure support.
     * This is the primary, public-facing way to create instances.
     *
     * - `make('Laravel')` -> text: '#Laravel', hashtag: '#Laravel'
     * - `make('#Laravel')` -> text: '#Laravel', hashtag: '#Laravel'
     * - `make('My Tag', 'laravel')` -> text: 'My Tag', hashtag: 'laravel'
     * - `make(fn($r) => $r->bold('PHP'))` -> Resolves closure to a RichEntity.
     *
     * @param RichEntity|callable|string|array|null $text The display text, or the hashtag value if used as a single argument.
     * @param string|null $hashtag The functional hashtag value (without symbol).
     * @return self
     */
    public static function make(RichEntity|callable|string|array|null $text = null, ?string $hashtag = null): self 
    {
        // Resolve the text content first, enabling fluent creation with closures.
        $resolvedText = self::resolveContent($text);

        // Delegate the complex logic to the HyperDX constructor.
        return new self($resolvedText, $hashtag);
    }

    // --- Implementation of abstract methods ---
    protected function getSymbol(): string { return '#'; }
    protected function getDataType(): string { return 'hashtag'; }
    protected function getDisplayType(): string { return 'Hashtag'; }
    protected function buildHref(string $value): string
    {
        $escapedValue = rawurlencode($value);
        if($this->targetsTelegram())
            return 'tg://search_hashtag?hashtag=' . $escapedValue;

        return $this->getPrefixByPlatform() . 'search?hashtag=' . $escapedValue;
    }
    
}
