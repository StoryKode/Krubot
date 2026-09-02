<?php
namespace KrubiK\Render\RichElements\Blocks;

use KrubiK\Render\RichElements\Blocks\RichBlockEntity;

/**
 * Represents a text-based separator block entity.
 * Unlike a divider (<hr/>), this is a semantic block of repeating characters
 * that acts as a visual, textual separator within the content flow.
 * It is immutable by design.
 */
class RichBlockSeparator extends RichBlockEntity
{
    /**
     * Constructs a new RichBlockSeparator instance.
     *
     * @param string $char The character used to build the separator line.
     * @param int $length The number of times the character is repeated or length of the separator line.
    */
    public function __construct(public string $char = '—', public int $length = 20)
    {
        /*
        // Basic validation can be added here if needed, e.g., length > 0.
        $this->char = $char;
        $this->length = $length;
        */
    }

    /**
     * Static factory to create a new RichBlockSeparator instance with fluent creation syntax.
     * e.g., RichBlockSeparator::make('-', 30)
     *
     * Represents a visual separator made of repeating characters.
     *
     * @param string $char The character to repeat for the separator.
     * @param int $length The number of times to repeat the character.
     * @return self Returns a new instance of the class.
    */
    public static function make(string $char = '—', int $length = 20): self
    {
        return new self($char, $length);
    }

    /**
     * Serializes the entity into a structured array for API consumption.
     * This representation is clean and machine-readable.
     */
    public function toArray(): array
    {
        return [
            'type' => 'plain', 
            // 'type' => 'separator', // @Todo: A more clear, descriptive type for this block.
            'char' => $this->char,
            'length' => $this->length,
            'text' => str_repeat($this->char, $this->length)
        ];
    }

    /**
     * Renders the separator as an HTML paragraph element.
     * Using <p> is more semantically correct for a line of text than a <div>.
     * The content is properly escaped to prevent any potential XSS issues.
     */
    public function toHtml(): string
    {
        // Generate the separator text.
        $separatorText = str_repeat($this->char, $this->length);

        // Wrap it in a paragraph and ensure HTML safety.
        return '<p>' . htmlspecialchars($separatorText, ENT_QUOTES, 'UTF-8') . '</p>';
    }

    /**
     * Renders the separator for Markdown formats.
     * It simply returns the text line followed by two newlines to ensure it's
     * treated as a distinct block (paragraph) by Markdown parsers.
     */
    public function toMd(): string
    {
        // The raw repeated text, followed by newlines to create a paragraph break.
        return str_repeat($this->char, $this->length) . "\n\n";
    }
}
