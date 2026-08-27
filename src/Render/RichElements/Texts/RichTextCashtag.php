<?php
namespace KrubiK\Render\RichElements\Texts;

use KrubiK\Render\RichElements\RichEntity;
use InvalidArgumentException;

readonly class RichTextCashtag extends RichTextSymbolLink
{
    public string $cashtag;

    /**
     * HyperDX Constructor for RichTextCashtag.
     *
     * - `make('BTC')`: Automatically becomes text="$BTC", cashtag="$BTC".
     * - `make('$BTC')`: Correctly parsed as text="$BTC", cashtag="$BTC".
     * - `make('My Tag', '$BTC')`: Uses provided values directly.
     *
     * @param RichEntity|string|array|null $text The display text.
     * @param string|null $cashtag The functional cashtag value.
     */
    public function __construct(
        RichEntity|string|array|null $text = null,
        ?string $cashtag = null
    ) {
        if ($text === null && $cashtag === null) {
            throw new InvalidArgumentException('RichTextCashtag requires at least one argument.');
        }

        $isSingleArgMode = ($text === null || $cashtag === null);
        
        // Determine the final values based on the input.
        $finalHashtag = $cashtag ?? $text;
        $finalText = $text ?? $finalHashtag;

        // Apply auto-prepending logic only in single-argument mode and if the feature is enabled.
        if ($isSingleArgMode && self::$autoPrependSymbol && is_string($finalHashtag) && !str_starts_with($finalHashtag, $this->getSymbol())) {
            $finalHashtag = $this->getSymbol() . $finalHashtag;
            
            // If the text was derived from the cashtag, update it as well to stay in sync.
            if ($text === null) {
                $finalText = $finalHashtag;
            }
        }
        
        // --- Final Property Assignment ---
        $this->cashtag = $finalHashtag;
        
        // Manually initialize parent properties, bypassing the parent constructor.
        $this->text = $finalText;
        $this->value = ltrim($this->cashtag, $this->getSymbol());
    }

    /**
     * Static factory for creating a new RichTextCashtag instance with full type-hinting and closure support.
     * This is the primary, public-facing way to create instances.
     *
     * - `make('BTC')` -> text: '$BTC', cashtag: '$BTC'
     * - `make('$BTC')` -> text: '$BTC', cashtag: '$BTC'
     * - `make('Bitcoin', 'btc')` -> text: 'Bitcoin', cashtag: 'btc'
     * - `make(fn($r) => $r->bold('DOGE'))` -> Resolves closure to a RichEntity.
     *
     * @param RichEntity|callable|string|array|null $text The display text, or the cashtag value if used as a single argument.
     * @param string|null $cashtag The functional cashtag value (without symbol).
     * @return self
     */
    public static function make(RichEntity|callable|string|array|null $text = null, ?string $cashtag = null): self
    {
        // Resolve the text content first, enabling fluent creation with closures.
        $resolvedText = self::resolveContent($text);

        // Delegate the complex logic to the HyperDX constructor.
        return new self($resolvedText, $cashtag);
    }

    // --- Implementation of abstract methods ---
    protected function getSymbol(): string { return '$'; }
    protected function getDataType(): string { return 'cashtag'; }
    protected function buildHref(string $value): string
    {
        // Telegram treats cashtag search like hashtag, just with a leading '$'
        return 'tg://search_hashtag?hashtag=' . rawurlencode('$' . $value);
    }
    
}
