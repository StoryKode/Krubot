<?php
namespace KrubiK\Render\RichElements\Components;

use KrubiK\Render\RichElements\RichEntity;

/**
 * Represents a caption for media blocks like photos, videos, etc.
 * This is a component, not a top-level block.
 * It implements Htmlable to be rendered directly in Telegram, Blade or other contexts.
*/
class RichBlockCaption extends RichComponentEntity
{
    /**
     * @param RichEntity|string|array $text The main caption text.
     * @param RichEntity|string|array|null $credit Optional credit text, like a citation.
     */
    public function __construct(
        public RichEntity|string|array $text,
        public RichEntity|string|array|null $credit = null
    ) {}

    /**
     * HyperDX factory for creating a RichBlockCaption with full closure support.
     * This method acts as a smart gateway to the constructor, resolving any
     * callable content before instantiation, allowing for a fluent and expressive API.
     *
     * Example:
     * RichBlockCaption::make(
     *     fn($r) => $r->bold('Photo:')->plain(' The Alps'),
     *     fn($r) => $r->italic('John Doe')
     * );
     *
     * @param RichEntity|callable|string|array $text The main caption content.
     * @param RichEntity|callable|string|array|null $credit Optional credit content.
     * @return self
     */
    public static function make(
        RichEntity|callable|string|array $text,
        RichEntity|callable|string|array|null $credit = null
    ): self {
        // Resolve both text and credit in case they are closures.
        $resolvedText = self::resolveContent($text);
        $resolvedCredit = self::resolveContent($credit);

        // Instantiates the object with the finalized, resolved content.
        return new self($resolvedText, $resolvedCredit);
    }

    /**
     * Converts the caption object to its array representation.
     *
     * @return array
    */
    public function toArray(): array
    {
        return $this->filterEmpty([
            'type' => 'caption',
            'text' => $this->normalize($this->text),
            'credit' => $this->normalize($this->credit, true),
        ]);
    }

    /**
     * Converts the RichBlockCaption to its semantic HTML representation using <figcaption> and <cite>.
     *
     * This method renders the caption inside a <figcaption> tag. The main text is wrapped
     * in a generic <span>, while the credit text is wrapped in a <cite> tag. Using <cite> is
     * semantically correct for attributions and citations, improving accessibility and SEO.
     *
     * It recursively renders the main text and the optional credit, wrapping them in
     * separate <span> elements to allow for distinct styling via CSS.
     *
     * Example CSS:
     * .rich-block-caption .caption-credit {
     *     display: block;
     *     font-style: italic;
     *     opacity: 0.8;
     *     margin-top: 0.5em;
     * }
     *
     * Example CSS:
     * .rich-block-caption .caption-credit { /* or simply '.rich-block-caption cite' * /
     *     display: block; /* or inline-block * /
     *     font-style: italic; /* Default browser style for <cite> * /
     *     opacity: 0.8;
     *     margin-top: 0.5em;
     * }
     *
     * @return string The rendered HTML string.
    */
    public function toHtml(): string
    {
        if($this->targetsWeb()) {

            $creditHtml = '';
            if ($this->credit !== null) {
                $creditHtml = '<span class="richy-caption__credit">'
                    . $this->renderHtml($this->credit)
                    . '</span>';
            }
    
            return '<div class="richy-caption">'
                . $this->renderHtml($this->text)
                . $creditHtml
                . '</div>';

        }

        // Use an array to assemble the inner parts of the caption, similar to the toPlainText method.
        // This pattern is cleaner than conditional string concatenation.
        $parts = [];

        // Part 1: Render the main caption text inside its own span for styling.
        $parts[] = '<span class="caption-text">' . $this->renderHtml($this->text) . '</span>';

        // Part 2: If a credit exists, render it inside the semantically correct <cite> tag.
        if ($this->credit) {
            $parts[] = '<cite class="caption-credit">' . $this->renderHtml($this->credit) . '</cite>';
        }

        // Join the parts with a space and wrap them in the final <figcaption> tag.
        $innerHtml = implode(' ', $parts);

        return "<figcaption class=\"rich-block-caption\">{$innerHtml}</figcaption>";
    }

    public function toMd(): string
    {
        $creditMd = ($this->credit !== null) ?
            ("\n_" . $this->renderText($this->credit) . "_")
        :
            '';

        return $this->renderText($this->text) . $creditMd;
    }

    /**
     * Converts the RichBlockCaption object to a plain text string representation.
     *
     * This is extremely useful for contexts where HTML is not allowed, such as
     * generating `alt` attributes for images or creating content summaries.
     * It strips all formatting and combines the main text and credit into a single line.
     *
     * @return string The plain text representation of the caption.
    */
    public function toPlainText(): string
    {
        // Use an array to collect text parts to handle the optional credit cleanly.
        $parts = [];

        // Recursively render the main text to its plain string form.
        $parts[] = $this->renderPlainText($this->text);

        // If a credit exists, render it to its plain string form as well.
        if ($this->credit) {
            $parts[] = $this->renderPlainText($this->credit);
        }

        // Join the parts with a meaningful separator. An em dash is a good choice for attribution.
        // `array_filter` removes any potentially empty parts before joining.
        return implode(' — ', array_filter($parts));
    }
}
