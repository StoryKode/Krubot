<?php

declare(strict_types=1);

namespace KrubiK\Render\Parsers;

/*
|--------------------------------------------------------------------------
| A Message to the Future Architect of Rebellion... 🚀🌌
|--------------------------------------------------------------------------
|
| Greetings, seeker of knowledge. You have just opened a blueprint
| from the Krubot BotEngine. What you see before you is more
| than just lines of code—it's a pattern for building scalable dreams.
|
| **This is a laboratory of creation.** We are experimenting with the
| very fabric of code here. Use this project as your ultimate training
| ground, a masterclass in *Software Dev Artistry.* It's a powerful template
| for learning, but not yet forged for the final battles of production.
|
| Behold the core principle:
| We Are **Rebuilding The Rebellion** Within S.N.P. *(The Foundation of Pure Power & Revel)*
| This entire library is being reconstructed with intense power,
| on a foundation of pure power **Far Stronger Than Anything That Came Before.**
| Starting with Laravel 12 Capabilities.
|
| What you see here is the **×ReleaseCandiate v0.8×** release. Why release it now?
| Because keeping this evolution a secret any longer would be a
| betrayal to the very community it was born to serve.
| 
| Consider this The Foundational Codex for Engineering a New Reality.
| The knowledge is free under the MIT License. Deconstruct its logic and schematics.
| Learn its secrets. Master its power. Command its potential. You are The Architect Now!
|
| * Go build something revolutionary! * 💜⚡️
|
| Let's Shape the Future. 🛠️⚡️🚀
|
*/

//================================================================================
// KrubiK Hyper-DX Rich Markdown To Entity Converter Trait
// Author: K.
// Date: 1405/05/16 (2026-08-07)
// Version: 4.6 (Hyper-DX)
// Description: This is an ultra-comprehensive, high-performance, and highly
// flexible trait for converting extended Markdown syntax into a structured
// array of Rich Block Entities. It merges the best features of multiple
// versions, including a robust recursive parsing architecture to correctly
// handle nested structures (like footnotes in blockquotes) and introduces a
// delegated model for processing raw HTML, ensuring maximum power and stability.
//================================================================================

// It's crucial to ensure all these helper functions are correctly imported.
// These functions act as factories for the rich text entity objects.
// This list is a superset of both provided versions.
use function KrubiK\Render\Helpers\{
    anchor,
    anchorLink,
    animation,
    audio,
    blockQuotation,
    bold,
    botCommand,
    caption,
    cashtag,
    cell,
    code,
    collage,
    customEmoji,
    dateTime,
    details,
    divider,
    emailAddress,
    footnoteDefinition,
    footer,
    hashtag,
    heading,
    italic,
    listBlock,
    listItem,
    map,
    marked,
    mathematicalExpression,
    mention,
    paragraph,
    phoneNumber,
    photo,
    plain,
    pre,
    pullQuotation,
    reference,
    referenceLink,
    slideshow,
    spoiler,
    strikethrough,
    subscript,
    superscript,
    table,
    tableCell,
    textMention,
    thinking,
    underline,
    href, // Renamed to prevent conflicts with Laravel url().
    video,
    voiceNote
};

use KrubiK\Facades\Parsentinel;
use KrubiK\Render\RichElements\Blocks\RichBlockEntity;
use KrubiK\Render\RichElements\Blocks\RichBlockList;
use KrubiK\Render\RichElements\Blocks\RichBlockParagraph;
use KrubiK\Render\RichElements\Blocks\RichBlockTable;
use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\RichElements\Texts\RichTextPlain;

class RichMDParser implements SyntaxWarden
{
    /** @var list<string> A list of complex HTML-like block tags that require balanced parsing. */
    private const COMPLEX_BLOCK_TAGS = ['details', 'tg-collage', 'tg-slideshow'];

    /** @var string Regex to identify the start of a list item (ordered or unordered). */
    private const RE_LIST_ITEM = '/^(\s*)([*+-]|\d+\.)\s(.*)$/s';

    /** @var string Regex to check if a line looks like the beginning of a list. */
    private const RE_LIST_START = '/^\s*(?:[*+-]\s|\d+\.\s)/';

    /** @var string Regex to identify a block entity placeholder comment. */
    private const RE_BLOCK_PLACEHOLDER = '/^<!-- BLOCK_ENTITY_\d+ -->$/';

    // HDX: List of known block-level HTML tags for the scanner.
    private const KNOWN_HTML_BLOCK_TAGS = [
        'div', 'p', 'ul', 'ol', 'table', 'blockquote', 'section', 
        'article', 'header', 'footer', 'details', 'figure', 'pre',
    /// 'tg-map', 'tg-collage', 'tg-slideshow'
    ];

    /**
     * @var array<string, RichBlockEntity> Stores footnote definitions found in the markdown. The key is the footnote identifier.
     */
    private array $mdFootnotes = [];

    /**
     * @var array<string, RichBlockEntity> Stores complex block entities (like details, collage) that are replaced by placeholders. The key is the placeholder HTML comment.
     */
    private array $mdBlockEntities = [];

    /**
     * @var int A counter to ensure unique IDs for block entity placeholders.
     */
    private int $mdPlaceholderId = 0;

    /**
     * Main public entry point for converting Markdown to entities.
     * This method initializes the state, calls the internal processor, and handles final assembly (like appending footnotes).
     * Use this method for the initial, top-level conversion.
     *
     * @param string $markdown The raw Markdown input string.
     * @return array<RichEntity> An array of rich entities representing the document structure.
     */
    public function decipher(string $markdown): array
    {
        // Step 1: Initialize and reset the state for a fresh conversion.
        // This is CRITICAL to prevent state pollution between different calls.
        // Note! Reset state only at the top-level entry point.
        $this->mdFootnotes = [];
        $this->mdBlockEntities = [];
        $this->mdPlaceholderId = 0;

        // Step 2: Call the internal processor and Pass state arrays by reference, to do the main pars-processing work.
        $blocks = $this->processMarkdown($markdown, $this->mdBlockEntities, $this->mdFootnotes);

        // Step 3: Final Assembly. If any footnotes were defined during the process,
        // append them to the end of the document, preceded by a thematic break (divider).
        // This is a common rendering practice.
        if (!empty($this->mdFootnotes)) {
            $blocks[] = divider();
            $blocks = array_merge($blocks, array_values($this->mdFootnotes));
        }

        return $blocks;
    }

    /**
     * Internal recursive processor for converting Markdown.
     * This method DOES NOT reset the state, making it safe for recursive calls
     * (e.g., when parsing content inside a blockquote, list item, or a details tag).
     *
     * @param string $markdown The raw Markdown fragment to process.
     * @return array<RichBlockEntity> An array of rich block entities for the given fragment.
     */
    private function processMarkdown(string $markdown, ?array &$blockEntities = null, ?array &$footnotes = null): array
    {
        // Normalize line endings for consistent parsing.
        $markdown = str_replace(["\r\n", "\r"], "\n", $markdown);

        // If references are not provided (for backward compatibility or direct calls), use internal properties.
        // This ensures the references are always valid within the processing chain.
        $blockEntities = $blockEntities ?? $this->mdBlockEntities;
        $footnotes = $footnotes ?? $this->mdFootnotes;

        // First pass: extract complex, multi-line blocks like <details> and replace them with placeholders.
        // This prevents their content from being parsed by the main block parser.
        $markdown = $this->extractComplexBlocksAndPlaceholders($markdown, $blockEntities, $footnotes);

        // Second pass: find all footnote definitions [^id]: ... and store them, removing them from the main text.
        $markdown = $this->extractFootnoteDefinitions($markdown, $blockEntities, $footnotes);

        // Process the main content line by line to identify and parse block-level elements.
        $lines = explode("\n", $markdown);
        $blocks = $this->processBlocks($lines, $blockEntities, $footnotes);

        // After parsing, re-insert the complex block entities where their placeholders were.
        $blocks = $this->reinsertBlockEntities($blocks);

        return $blocks;
    }

    /* *
     * Recursively finds and processes custom HTML-like tags for complex blocks.
     * Replaces them with a placeholder and stores the generated RichBlockEntity.
     * @param string $text The input text to scan.
     * @return string The text with complex blocks replaced by placeholders.
     * /
    private function extractComplexBlocksAndPlaceholdersOld(string $text, array &$blockEntities, array &$footnotes): string
    {
        // Define the custom tags we're looking for.
        $tags = 'details|tg-collage|tg-slideshow';

        // The regex uses the 's' flag for dotall to match across newlines, and 'i' for case-insensitivity.
        return preg_replace_callback(
            '#<(' . $tags . ')(.*?)>(.*?)</\1>#mis',
            function ($matches) use (&$blockEntities, &$footnotes) {
                $id = $this->mdPlaceholderId++;
                $placeholder = "<!-- BLOCK_ENTITY_{$id} -->";
                $tag = strtolower($matches[1]);
                $attributes = $matches[2];
                $innerContent = $matches[3];

                // Use a match expression for clean and readable logic to handle different tags.
                $blockEntity = match ($tag) {
                    'tg-collage' => collage($this->processMarkdown($innerContent, $blockEntities, $footnotes)),
                    'tg-slideshow' => slideshow($this->processMarkdown($innerContent, $blockEntities, $footnotes)),
                    'details' => (function() use ($attributes, $innerContent) {
                        // The 'details' tag is special. It has a summary and content.
                        $isOpen = stripos($attributes, 'open') !== false;
                        $summaryText = 'Details'; // Default summary text
                        $detailsContent = $innerContent;

                        // Check for an explicit <summary> tag within the details content.
                        if (preg_match('{<summary>(.*?)</summary>}is', $innerContent, $summaryMatches)) {
                            $summaryText = $summaryMatches[1];
                            // Remove the summary tag from the content that will be parsed for the details body.
                            $detailsContent = str_replace($summaryMatches[0], '', $detailsContent);
                        }

                        // Parse the summary for inline entities and the body for block entities.
                        $summaryEntities = $this->parseInlines($summaryText);
                        // CRITICAL: Use the internal, state-safe processor for recursive parsing.
                        $innerEntities = $this->processMarkdown(trim($detailsContent, $blockEntities, $footnotes));

                        return details(text($summaryEntities), $innerEntities, $isOpen);
                    })(),
                    default => null, // Should not be reached with the current regex.
                };

                if ($blockEntity) {
                    // Modify the state array passed by reference.
                    $blockEntities[$placeholder] = $blockEntity;
                    return $placeholder; // Replace the tag with the placeholder.
                }

                return $matches[0]; // If something went wrong, return the original match.
            },
            $text
        ) ?? $text;
    }
    */
    /**
     * Extracts complex, nested HTML-like blocks (like <details>) using a robust, balanced tag matching algorithm.
     * This replaces the previous regex-based approach, allowing for infinite nesting levels.
     *
     * @param string $text The input markdown text.
     * @param array  &$blockEntities Reference to the array storing block entities.
     * @param array  &$footnotes Reference to the array storing footnote definitions.
     * @return string The text with complex blocks replaced by placeholders.
     */
    private function extractComplexBlocksAndPlaceholders(string $text, array &$blockEntities, array &$footnotes): string
    {
        $tagAlt = implode('|', self::COMPLEX_BLOCK_TAGS);
        $openRe = '/<(' . $tagAlt . ')(\s[^>]*)?>/i';

        $out = '';
        $offset = 0;
        $length = strlen($text);

        while ($offset < $length && preg_match($openRe, $text, $m, PREG_OFFSET_CAPTURE, $offset)) {
            $openFull = $m[0][0];
            $openPos = $m[0][1];
            $tag = strtolower($m[1][0]);
            $attributes = $m[2][0] ?? '';

            $out .= substr($text, $offset, $openPos - $offset);

            $contentStart = $openPos + strlen($openFull);
            $scan = $contentStart;
            $depth = 1;
            $endPos = null;

            $openOne = '/<' . preg_quote($tag, '/') . '(?:\s[^>]*)?>/i';
            $closeOne = '/<\/' . preg_quote($tag, '/') . '\s*>/i';

            while ($depth > 0 && $scan < $length) {
                $hasOpen = preg_match($openOne, $text, $om, PREG_OFFSET_CAPTURE, $scan);
                $hasClose = preg_match($closeOne, $text, $cm, PREG_OFFSET_CAPTURE, $scan);

                if (!$hasClose) {
                    break; // No closing tag found, exit loop.
                }

                $closeAt = $cm[0][1];
                $openAt = $hasOpen ? $om[0][1] : PHP_INT_MAX;

                if ($hasOpen && $openAt < $closeAt) {
                    // Found a nested opening tag before the next closing tag.
                    ++$depth;
                    $scan = $openAt + strlen($om[0][0]);
                    continue;
                }

                // Found a closing tag.
                --$depth;
                $closeLen = strlen($cm[0][0]);
                if ($depth === 0) {
                    // This is the matching closing tag for our initial opening tag.
                    $innerContent = substr($text, $contentStart, $closeAt - $contentStart);
                    $endPos = $closeAt + $closeLen;

                    // CRITICAL INTEGRATION: Pass state references to the builder.
                    $blockEntity = $this->buildComplexBlockEntity($tag, $attributes, $innerContent, $blockEntities, $footnotes);
                    if ($blockEntity !== null) {
                        $id = $this->mdPlaceholderId++;
                        $placeholder = "<!-- BLOCK_ENTITY_{{$id}} -->";
                        $blockEntities[$placeholder] = $blockEntity;
                        $out .= $placeholder;
                    } else {
                        // If building failed, output the original tag block.
                        $out .= substr($text, $openPos, $endPos - $openPos);
                    }
                    break;
                }
                
                // Continue scanning from after the found closing tag.
                $scan = $closeAt + $closeLen;
            }

            if ($endPos === null) {
                // Unclosed tag detected. To prevent data loss, output the rest of the string as is.
                $out .= substr($text, $openPos);
                return $out;
            }

            $offset = $endPos;
        }

        return $out . substr($text, $offset);
    }

    /**
     * Helper to construct a specific complex block entity from its parts.
     */
    private function buildComplexBlockEntity(string $tag, string $attributes, string $innerContent, array &$blockEntities, array &$footnotes): mixed
    {
        // CRITICAL INTEGRATION: The recursive call to processMarkdown MUST pass the state references.
        return match ($tag) {
            'tg-collage' => collage($this->processMarkdown($innerContent, $blockEntities, $footnotes)),
            'tg-slideshow' => slideshow($this->processMarkdown($innerContent, $blockEntities, $footnotes)),
            'details' => $this->buildDetailsBlockEntity($attributes, $innerContent, $blockEntities, $footnotes),
            default => null,
        };
    }
    
    /**
     * Helper to construct a 'details' block, extracting the summary.
     */
    private function buildDetailsBlockEntity(string $attributes, string $innerContent, array &$blockEntities, array &$footnotes): mixed
    {
        $isOpen = stripos($attributes, 'open') !== false;
        $summaryText = 'Details';
        $detailsContent = $innerContent;

        if (preg_match('/<summary\b[^>]*>(.*?)<\/summary>/is', $innerContent, $summaryMatches, PREG_OFFSET_CAPTURE)) {
            $summaryText = $summaryMatches[1][0];
            // Use position-based replacement to avoid issues with duplicate summary content.
            $detailsContent = substr_replace(
                $detailsContent,
                '',
                $summaryMatches[0][1],
                strlen($summaryMatches[0][0])
            );
        }
        
        $summaryEntities = $this->parseInlines($summaryText);
        // CRITICAL INTEGRATION: The recursive call for the details content also needs the state references.
        $innerEntities = $this->processMarkdown(trim($detailsContent), $blockEntities, $footnotes);
        
        // The RichMan::details helper expects the summary entities directly.
        return details($summaryEntities, $innerEntities, $isOpen);
    }

    /**
     * Finds and extracts footnote definitions, storing them for later use.
     * @param string $text The input text to scan.
     * @return string The text with footnote definitions removed.
     */
    private function extractFootnoteDefinitions(string $text, array &$blockEntities, array &$footnotes): string
    {
        return preg_replace_callback(
            '/^\[\^(.+?)\]:\s*(.*(?:\n(?!\[\^.+?\]:|\s*$).*)*)/m',
            function ($matches) use (&$blockEntities, &$footnotes) {
                $id = $matches[1];
                // The content of a footnote can itself contain multiple blocks.
                // CRITICAL: Use the internal, state-safe processor for recursive parsing.
                $contentBlocks = $this->processMarkdown(trim($matches[2]), $blockEntities, $footnotes);
                $footnotes[$id] = footnoteDefinition($id, $contentBlocks);
                return ''; // Remove the definition from the text.
            },
            $text
        ) ?? $text;
    }

    /**
     * Iterates through an array of lines, parsing them into block-level entities.
     * This is the core block-level parser.
     * @param array<string> &$lines An array of lines passed by reference.
     * @return array<RichBlockEntity> An array of parsed blocks.
     */
    private function processBlocks(array &$lines, array &$blockEntities, array &$footnotes): array
    {
        $blocks = [];
        $currentParagraphLines = [];

        // A closure to commit the current collected lines as a paragraph.
        // This avoids code duplication and centralizes paragraph creation logic.
        /* $commitParagraph = function() use (&$blocks, &$currentParagraphLines, &$blockEntities) {
            if (!empty($currentParagraphLines)) {
                $text = trim(implode("\n", $currentParagraphLines));
                if ($text !== '') {
                    // Check if the "paragraph" is actually one of our placeholders.
                    ///if (preg_match('/^<!-- BLOCK_ENTITY_\d+ -->$/', $text)) {
                    if (isset($blockEntities[$text])) { // Now There is a more performant and direct check than using regex.
                         // If it's a placeholder, create a simple RichTextPlain object.
                         // This is a lightweight way to carry the placeholder until re-insertion.
                         $blocks[] = plain($text);
                    } else {
                        // Otherwise, parse the paragraph content for inline entities.
                        $blocks[] = paragraph($this->parseInlines($text));
                    }
                }
                $currentParagraphLines = [];
            }
        };
        $commitParagraph = function () use (&$blocks, &$currentParagraphLines, &$blockEntities) {
            if (!empty($currentParagraphLines)) {
                $text = trim(implode("\n", $currentParagraphLines));
                if ($text !== '') {
                    // This is a more robust check and also cleans up the entity map.
                    if (preg_match(self::RE_BLOCK_PLACEHOLDER, $text) && isset($blockEntities[$text])) {
                        $blocks[] = $blockEntities[$text];
                        unset($blockEntities[$text]);
                    } else {
                        $blocks[] = paragraph($this->parseInlines($text));
                    }
                }
                $currentParagraphLines = [];
            }
        }; */
        $commitParagraph = function () use (&$blocks, &$currentParagraphLines, &$blockEntities) {
            if (empty($currentParagraphLines)) {
                return;
            }
            
            $text = trim(implode("\n", $currentParagraphLines));
            $currentParagraphLines = []; // Reset immediately
        
            if ($text === '') {
                return;
            }
        
            // A placeholder should be the *only* thing on its "paragraph"
            if (isset($blockEntities[$text])) {
                $blocks[] = $blockEntities[$text];
                unset($blockEntities[$text]); // Consume the entity
            } else {
                $blocks[] = paragraph($this->parseInlines($text));
            }
        };

        while (($line = current($lines)) !== false) {
            $trimmedLine = rtrim($line);
            next($lines);

            // An empty line signifies a break between blocks. Commit any pending paragraph.
            if (trim($trimmedLine) === '') {
                $commitParagraph();
                continue;
            }

            // =========================================================================
            // AUTHORITATIVE HTML BLOCK DELEGATION STRATEGY
            // =========================================================================
            $knownTagsRegex = implode('|', self::KNOWN_HTML_BLOCK_TAGS);
            if (preg_match('/^\s*<(' . $knownTagsRegex . ')\b/i', $trimmedLine, $m)) {
                // 1. An HTML block is detected. Commit any pending Markdown paragraph.
                $commitParagraph();

                // 2. Initialize the balanced tag scanner.
                $rootTag = strtolower($m[1]);
                $htmlBuffer = [$trimmedLine];
                $depth = 1;

                // 3. Build precise regexes for the specific root tag.
                $openRe = '/<' . preg_quote($rootTag, '/') . '\b/i';
                $closeRe = '/<\/' . preg_quote($rootTag, '/') . '\s*>/i';

                // 4. Safely consume lines until the root tag is balanced, even across newlines.
                while ($depth > 0 && ($nextLine = current($lines)) !== false) {
                    $htmlBuffer[] = $nextLine;
                    next($lines);
                    
                    $depth += preg_match_all($openRe, $nextLine);
                    $depth -= preg_match_all($closeRe, $nextLine);
                }
                
                $htmlString = implode("\n", $htmlBuffer);

                // 5. --- THE CRITICAL DELEGATION STEP ---
                // Check if the master HTML parser from the container is available.
                if ($htmlParser = Parsentinel::summon('html')) {
                    // Delegate the entire HTML block to the dedicated, powerful HTML processor.
                    // That method is responsible for its own DOM parsing and state management.
                    $htmlBlocks = $htmlParser->decipher($htmlString);
                    $blocks = array_merge($blocks, $htmlBlocks);
                } else {
                    // Failsafe: If the RichHTMLParser trait is not used in the final class,
                    // we prevent a fatal error and data loss by treating the block as preformatted HTML.
                    // This makes the trait more robust.
                    $blocks[] = pre($htmlString, 'html');
                }

                continue; // Continue to the next block.
            }

            // HYPER-DX FEATURE: Standalone media detection (Image, Video, etc.)
            if (preg_match('/^!\\[(?P<alt>.*?)\\]\\((?P<src>.*?)(?:\s+"(?P<caption_text>.*?)")?\\)\s*$/', $trimmedLine, $m)) {
                $commitParagraph();
                
                $url = $m['src'];
                // The 'tg://' protocol is handled by the advanced MasterRegEX in parseInlines,
                // but for block-level media, we only care about standard file URLs.
                if (str_starts_with($url, 'tg://')) {
                    // Let this fall through to be treated as a paragraph with inline entities
                    $currentParagraphLines[] = $line;
                } else {
                    $captionText = $m['caption_text'] ?? null;
                    $cap = $captionText ? caption($this->parseInlines($captionText)) : null;

                    $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));

                    $mediaEntity = match ($ext) {
                        'mp4', 'mov', 'webm' => video(['file_id' => $url], caption: $cap),
                        'gif' => animation(['file_id' => $url], caption: $cap),
                        'mp3', 'flac', 'wav' => audio(['file_id' => $url], caption: $cap),
                        'ogg', 'opus' => voiceNote(['file_id' => $url], caption: $cap),
                        default => photo(['file_id' => $url], caption: $cap)
                    };
                    $blocks[] = $mediaEntity;
                    continue;
                }
            }
            
            // HYPER-DX FEATURE: HTML Block Delegation
            // Detect if a line starts with a common HTML block tag.
            if (preg_match('/^\s*<(?=(?:div|p|ul|ol|table|blockquote|section|article|header|footer)\b)/i', $trimmedLine)) {
                $commitParagraph();
                $htmlContent = $trimmedLine . "\n";
                // Greedily consume lines until a blank line, assuming this is the HTML block.
                while (($nextLine = current($lines)) !== false && trim($nextLine) !== '') {
                    $htmlContent .= $nextLine . "\n";
                    next($lines);
                }
                // Delegate the entire HTML block to the dedicated HTML processor.
                $blocks = array_merge($blocks, Parsentinel::summon('html')->decipher($htmlContent));
                continue;
            }

            // Block-level Media: A standalone image/video/etc. on its own line.
            if (preg_match('/^!\[(?P<alt>.*?)\]\((?P<src>.*?)(?:\s+"(?P<caption>.*?)")?\)\s*$/', $trimmedLine, $m)) {
                $url = $m['src'];
                // Skip special tg:// links here; they are handled as inline entities by parseInlines.
                if (str_starts_with($url, 'tg://')) {
                     $currentParagraphLines[] = $line;
                     continue;
                }

                $commitParagraph();
                $captionText = $m['caption'] ?? null;
                $cap = $captionText ? caption($this->parseInlines($captionText)) : null;

                $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));

                $mediaBlock = match ($ext) {
                    'mp4', 'mov' => video(['file_id' => $url], caption: $cap),
                    'gif' => animation(['file_id' => $url], caption: $cap),
                    'mp3' => audio(['file_id' => $url], caption: $cap),
                    'ogg' => voiceNote(['file_id' => $url], caption: $cap),
                    default => photo(['file_id' => $url], caption: $cap)
                };
                $blocks[] = $mediaBlock;
                continue;
            }

            // Block-level Mathematical Expression ($$)
            if (preg_match('/^(\$\$)(.*)$/', $trimmedLine, $m)) {
                $commitParagraph();
                // Check if there was content on the same line as the opening $$
                $content = $m[2] !== '' ? $m[2] . "\n" : '';
                while (($blockLine = current($lines)) !== false) {
                    if (rtrim($blockLine) === '$$') {
                        next($lines);
                        break;
                    }
                    $content .= $blockLine . "\n";
                    next($lines);
                }
                $blocks[] = mathematicalExpression(rtrim($content), true);
                continue;
            }

            // Fenced Code Blocks (```) or (~~~) and Fenced Math Blocks (```math)
            if (preg_match('/^(```|~{3,})([a-zA-Z0-9_+\-.]*)\s*(.*)$/', $trimmedLine, $m)) {
                $commitParagraph();
                $marker = $m[1];

                $lang = $m[2] !== '' ? $m[2] : null;
                // Capture content on the same line as the opening marker
                $content = $m[3] !== '' ? $m[3] . "\n" : '';
                while (($blockLine = current($lines)) !== false) {
                    if (trim($blockLine) === $marker) { // Using trim() is slightly more robust here
                        next($lines);
                        break;
                    }
                    
                    $content .= $blockLine . "\n";
                    next($lines);
                }

                // Support for ```math as an alternative to $$
                if ($lang === 'math') {
                    $blocks[] = mathematicalExpression(rtrim($content), true);
                } else {
                    $blocks[] = pre(rtrim($content), $lang);
                }
                continue;
            }

            // Headings (# to ######)
            if (preg_match('/^(#{1,6})\s+(.*)/', $trimmedLine, $m)) {
                $commitParagraph();
                $level = strlen($m[1]);
                $contentEntities = $this->parseInlines(trim($m[2]));
                $blocks[] = heading($contentEntities, $level);
                continue;
            }

            // Blockquotes (>)
            if (preg_match('/^>\s?(.*)/', $trimmedLine, $m)) {
                $commitParagraph();
                $content = $m[1] . "\n";
                while (($nextLine = current($lines)) !== false && preg_match('/^>\s?(.*)/', $nextLine, $m_next)) {
                    $content .= $m_next[1] . "\n";
                    next($lines);
                }
                // CRITICAL: The content of a blockquote can contain other blocks.
                // Use the internal, state-safe processor for recursive parsing.
                $innerBlocks = $this->processMarkdown(trim($content), $blockEntities, $footnotes);
                $blocks[] = blockQuotation($innerBlocks);
                continue;
            }

            // Horizontal Rule / Divider (---, ***, ___)
            if (preg_match('/^(\*|-|_)\s*\1\s*\1\s*$/', $trimmedLine)) {
                 $commitParagraph();
                 $blocks[] = divider();
                 continue;
            }

            // List Items (*, -, +, 1.)
            if (preg_match(self::RE_LIST_START, $trimmedLine)) { /// '/^\s*(?:\*|\-|\+)\s|\s*\d+\.\s/'
                $commitParagraph();
                prev($lines); // The list processor needs to see the current line again.
                $listBlock = $this->processListBlock($lines, 0, $blockEntities, $footnotes);
                if (!empty($listBlock->items)) {
                   $blocks[] = $listBlock;
                }
                continue;
            }

            // Tables (identified by the header separator line)
            $peek = current($lines);
            if (str_contains($trimmedLine, '|') && $peek !== false && preg_match('/^\s*\|?(:?-+:?\|)+(:?-+:?)?\|?\s*$/', $peek)) {
                $commitParagraph();
                prev($lines); // The table processor needs to see the header line again.
                $blocks[] = $this->processTableBlock($lines);
                continue;
            }

            // If no other block type matches, it's part of a paragraph.
            $currentParagraphLines[] = $line;
        }

        // Commit any remaining lines as the last paragraph.
        $commitParagraph();

        return $blocks;
    }

    /* *
     * Processes a list block, handling ordered, unordered, and multi-line items.
     * @param array<string> &$lines The array of lines, passed by reference.
     * @return RichBlockList The parsed list block object.
     * /
    private function processListBlockOld(array &$lines, array &$blockEntities, array &$footnotes): RichBlockList
    {
        $items = [];
        $listType = null;

        // This loop continues as long as we are seeing lines that look like list items.
        while (($line = current($lines)) !== false && preg_match('/^(\s*)((\*|\-|\+)|(\d+)\.)\s(.*)/s', $line, $matches)) {
            next($lines);

            $marker = $matches[2];
            $content = $matches[5];
            $isOrdered = !empty($matches[4]);

            if ($listType === null) {
                $listType = $isOrdered ? 'ordered' : 'bullet';
            }

            // Look ahead for subsequent lines that are part of the same list item (indented).
            while (($nextLine = current($lines)) !== false) {
                 // Break if the next line is a new list item, or not indented enough, or empty.
                 if (trim($nextLine) === '' || preg_match(self::RE_LIST_START, $nextLine)) {
                     break;
                 }
                 $content .= "\n" . $nextLine;
                 next($lines);
            }

            // Check for GFM-style task list items like `* [x] Task`.
            $hasCheckbox = preg_match('/^\[([ xX])\]\s+/', $content, $checkMatches);
            $isChecked = false;
            if ($hasCheckbox) {
                $isChecked = strtolower(trim($checkMatches[1])) === 'x';
                $content = preg_replace('/^\[[ xX]\]\s+/', '', $content);
            }

            // CRITICAL: The content of a list item can contain multiple blocks (e.g., paragraphs, code blocks).
            // Use the internal, state-safe processor for recursive parsing.
            $itemBlocks = $this->processMarkdown(trim($content), $blockEntities, $footnotes);

            $items[] = listItem(
                blocks: $itemBlocks,
                label: $isOrdered ? $matches[4] : rtrim($marker),
                hasCheckbox: $hasCheckbox,
                isChecked: $isChecked
            );
        }

        return listBlock($items, $listType ?? 'bullet');
    }
    */

    /**
    * Processes a block of lines to form a list, handling nested lists and multi-line items correctly.
    * It intelligently tracks indentation levels to determine item boundaries and nesting.
    *
    * @param array   &$lines The array of lines to process.
    * @param int     $minIndent The minimum indentation level for an item to be part of this list.
    * @param array   &$blockEntities Reference to the block entities state.
    * @param array   &$footnotes Reference to the footnotes state.
    * @return RichBlockList The constructed list block.
    */
    private function processListBlock(array &$lines, int $minIndent, array &$blockEntities, array &$footnotes): RichBlockList
    {
        $items = [];
        $listType = null;
        $currentIndent = null;

        while (($line = current($lines)) !== false) {
            if (!preg_match(self::RE_LIST_ITEM, $line, $matches)) {
                break; // Not a list item
            }

            $indent = strlen(str_replace("\t", '    ', $matches[1]));
            if ($indent < $minIndent) {
                break; // Dedented, so this list has ended
            }

            if ($currentIndent === null) {
                $currentIndent = $indent;
            } elseif ($indent > $currentIndent) {
                // This is a nested list, should be handled by the parent item's recursive call.
                break;
            } elseif ($indent < $currentIndent) {
                // Dedent indicates the end of the current list.
                break;
            }

            next($lines);

            $marker = $matches[2];
            $content = $matches[3];
            $isOrdered = ctype_digit(rtrim($marker, '.'));

            if ($listType === null) {
                $listType = $isOrdered ? 'ordered' : 'bullet';
            }

            // Greedily consume subsequent lines belonging to this item.
            // This includes continuation lines and more-deeply-indented content (like nested lists or code blocks).
            while (($nextLine = current($lines)) !== false) {
                if (trim($nextLine) === '') {
                    // Handle blank lines. A blank line might separate items or just be part of an item's content.
                    // We need to peek ahead.
                    $key = key($lines);

                    /// $peek = isset($lines[$key + 1]) ? $lines[$key + 1] : null;

                    // FIX: Correct logic for handling blank lines and peeking ahead safely.
                    // Safe peek: use array_slice to handle non-sequential keys correctly.
                    $keys = array_keys($lines);
                    $currentKeyIndex = array_search($key, $keys);
                    $peek = ($currentKeyIndex !== false && isset($keys[$currentKeyIndex + 1])) ? $lines[$keys[$currentKeyIndex + 1]] : null;

                    if ($peek === null || trim($peek) === '') break; // End of content or multiple blank lines

                    // Check if the peeked line is a new list item at the same or lesser indent.
                    if (preg_match(self::RE_LIST_ITEM, $peek, $pm)) {
                        $peekIndent = strlen(str_replace("\t", '    ', $pm[1]));
                        if ($peekIndent <= $currentIndent) {
                            break; // It's a new item or parent item. Stop consuming.
                        }
                    }
                }

                $normNextLine = str_replace("\t", '    ', $nextLine);
                // A line is a continuation if it's indented more than the current list item's start.

                /// if (preg_match('/^\s{' . ($currentIndent + 1) . ',}/', $normNextLine) || preg_match(self::RE_LIST_ITEM, $nextLine, $nm) && strlen(str_replace("\t", '    ', $nm[1])) > $currentIndent) {

                // FIX: Fixed operator precedence with explicit parentheses.
                if (
                    preg_match('/^\s{' . ($currentIndent + 1) . ',}/', $normNextLine)
                    || (preg_match(self::RE_LIST_ITEM, $nextLine, $nm) && strlen(str_replace("\t", '    ', $nm[1])) > $currentIndent)
                ) {

                    $content .= "\n" . $nextLine;
                    next($lines);

                } else {
                    break; // Line is not a continuation.
                }
            }

            $hasCheckbox = (bool) preg_match('/^\[([ xX])\]\s+/', $content, $checkMatches);
            $isChecked = false;
            if ($hasCheckbox) {
                $isChecked = strtolower($checkMatches[1]) === 'x';
                $content = (string) preg_replace('/^\[[ xX]\]\s+/', '', $content, 1);
            }

            // CRITICAL INTEGRATION: Recursive call with state references to parse item content.
            $itemBlocks = $this->processMarkdown(trim($content), $blockEntities, $footnotes);

            // Per RichMan contract, label is just the number for ordered lists.
            $label = $isOrdered ? rtrim($marker, '.') : rtrim($marker);
            $items[] = listItem(
                label: $label,
                blocks: $itemBlocks,
                hasCheckbox: $hasCheckbox,
                isChecked: $isChecked
            );
        }

        return listBlock($items, $listType ?? 'bullet');
    }

    /**
     * Processes a table block.
     * @param array<string> &$lines The array of lines, passed by reference.
     * @return RichBlockTable The parsed table block object.
     */
    private function processTableBlock(array &$lines): RichBlockTable
    {
        $headerLine = trim(current($lines), " \t\n\r\0\x0B|");
        next($lines);
        $alignLine = trim(current($lines), " \t\n\r\0\x0B|");
        next($lines);

        $headers = array_map(fn($h) => $this->parseInlines(trim($h)), explode('|', $headerLine));
        $numCols = count($headers);

        // Determine column alignments from the separator line (e.g., :---:, ---:, :---).
        $aligns = array_map(function($cell) {
            $cell = trim($cell);
            $isLeft = str_starts_with($cell, ':');
            $isRight = str_ends_with($cell, ':');
            if ($isLeft && $isRight) return 'center';
            if ($isRight) return 'right';
            // Default alignment is left.
            return 'left';
        }, explode('|', $alignLine));

        $rowsData = [];
        $headerCells = [];
        foreach ($headers as $i => $headerContent) {
            $headerCells[] = cell($headerContent, isHeader: true, align: $aligns[$i] ?? 'left');
        }
        $rowsData[] = $headerCells;

        // Process all subsequent lines that contain a pipe '|' as table rows.
        while (($line = current($lines)) !== false && str_contains($line, '|')) {
            $rowCells = [];
            $cellStrings = explode('|', trim($line, " \t\n\r\0\x0B|"));
            foreach ($cellStrings as $i => $cellString) {
                // Ignore any columns beyond what is defined in the header.
                if ($i >= $numCols) break;
                $rowCells[] = cell($this->parseInlines(trim($cellString)), isHeader: false, align: $aligns[$i] ?? 'left');
            }
            $rowsData[] = $rowCells;
            next($lines);
        }

        return table($rowsData, isBordered: true, isStriped: false);
    }

    /**
     * Parses a string for inline-level entities like bold, italic, links, etc.
     * This version is significantly upgraded to auto-detect many more entity types.
     * @param string $text The text of a block (like a paragraph or heading).
     * @return array<RichEntity> An array of parsed inline entities.
     */
    private function parseInlines(string $text): string|RichEntity|array
    {

        // IMPROVEMENT: Regex order refined. Added a specific, stricter pattern for _italic_.
        /* $oldMasterRegex = '/' . implode('|', [
            '(?P<image>!\[(?P<image_alt>.*?)\]\((?P<image_src>.*?)\))', // Inline Media (Emojis, Timestamps)
            '(?P<link>\[(?P<link_text>.*?)\]\((?P<link_url>.*?)\))', // Links
            '(?P<code>`(?P<code_text>.+?)`)', // Inline code
            '(?P<bold>(\*\*|__)(?=\S)(?P<bold_text>.+?)(?<=\S)\1)', // Bold
            '(?P<italic>\*(?=\S)(?P<italic_text_star>.+?)(?<=\S)\*)', // Italic with *
            // BUG FIX #5: Stricter rule for _italic_ to avoid matching inside words like `hello_world`.
            '(?P<italic_underline>(?<!\w)_(?=\S)(?P<italic_text_underline>.+?)(?<=\S)_(?!\w))', // Italic with _
            '(?P<strike>~~(?=\S)(?P<strike_text>.+?)(?<=\S)~~)', // Strikethrough
            '(?P<spoiler>\|\|(?=\S)(?P<spoiler_text>.+?)(?<=\S)\|\|)', // Spoiler
            '(?P<marked>==(?=\S)(?P<marked_text>.+?)(?<=\S)==)', // Marked
            '(?P<math>\$(?P<math_text>[^\$]+?)\$)', // Inline math
            '(?P<footnote>\[\^(?P<footnote_id>.+?)\])', // Footnote ref
            '(?P<html><(u|ins|sup|sub)>(?P<html_text>.*?)<\/\1>)', // Simple inline HTML tags
        ]) . '/s'; */

        // This is the heart of the inline parser. The order of regex patterns is critical for correct matching.
        // More specific patterns (like special links) must come before more general ones (like standard links or auto-detection).
        $masterRegex = '/' . implode('|', [
            // Links and Media must come first to correctly capture their contents.
            '(?P<special_media>!\[(?P<special_alt>.*?)\]\((?P<special_src>tg:\/\/(emoji|time)\?.*?)\))',
            '(?P<link>\[(?P<link_text>(?:\[[^\]]*\]|[^\[\]])*)\]\((?P<link_url>[^\s\)]+)\))', // Handles nested brackets in link text

            // Code and Math are high priority to prevent their contents from being parsed.
            '(?P<code>`(?P<code_text>.+?)`)',
            '(?P<math>\$(?P<math_text>[^\$\n]+?)\$)',

            // Custom HTML-like tags for formatting.
            '(?P<spoiler_custom_tag><tg-spoiler>(?P<spoiler_custom_text>.*?)<\/tg-spoiler>)',
            '(?P<html><(u|ins|sup|sub)>(?P<html_text>.*?)<\/\1>)',

            // Standard Markdown formatting.
            '(?P<bold>(\*\*|__)(?=\S)(?P<bold_text>.+?)(?<=\S)\1)',
            '(?P<italic>\*(?=\S)(?P<italic_text_star>.+?)(?<=\S)\*)',
            '(?P<italic_underline>(?<!\w)_(?=\S)(?P<italic_text_underline>.+?)(?<=\S)_(?!\w))',
            '(?P<strike>~~(?=\S)(?P<strike_text>.+?)(?<=\S)~~)',
            '(?P<spoiler>\|\|(?=\S)(?P<spoiler_text>.+?)(?<=\S)\|\|)',
            '(?P<marked>==(?=\S)(?P<marked_text>.+?)(?<=\S)==)',

            // Footnote reference.
            '(?P<footnote>\[\^(?P<footnote_id>.+?)\])',

            // UPGRADE: Auto-detection of common patterns. These have lower priority.
            '(?P<mention>@\w{5,32})',
            '(?P<hashtag>#[\p{L}\p{N}_]{1,64})',
            '(?P<cashtag>\$[A-Z]{3,6})',
            '(?P<bot_command>\/\w{1,64}(?:@\w{5,32})?)',
            '(?P<email>[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})',
            '(?P<phone_number>\+[\d][\d\s\-()]{8,}[\d])',

        ]) . '/sui';

        $entities = [];
        $offset = 0;

        // Loop through all matches of the master regex in the text.
        while (preg_match($masterRegex, $text, $matches, PREG_OFFSET_CAPTURE, $offset)) {
            $matchText = $matches[0][0];
            $matchOffset = $matches[0][1];

            // If there is plain text between the last match and this one, capture it.
            if ($matchOffset > $offset) {
                $entities[] = plain(substr($text, $offset, $matchOffset - $offset));
            }

            // Find which named capture group actually matched.
            $captureKey = null;
            foreach ($matches as $key => $value) {
                if (is_string($key) && $value[1] !== -1) {
                    $captureKey = $key;
                    break;
                }
            }
            
            // Process the match based on which capture group was successful.
            switch ($captureKey) {
                case 'special_media':
                    $url = $matches['special_src'][0];
                    $alt = $matches['special_alt'][0];
                    if (preg_match('/^tg:\/\/emoji\?id=([\d_]+)$/', $url, $emoji_m)) {
                        $entities[] = customEmoji($emoji_m[1], $alt);
                    } elseif (preg_match('/^tg:\/\/time\?unix=(\d+)&format=(.*?)$/', $url, $time_m)) {
                        $entities[] = dateTime($alt, (int)$time_m[1], $time_m[2]);
                    }
                    break;
                case 'link':
                    // The text of a link can itself contain other inline entities (e.g., [**bold** link](...)).
                    $entities[] = href($this->parseInlines($matches['link_text'][0]), $matches['link_url'][0]);
                    break;
                case 'code':
                    $entities[] = code($matches['code_text'][0]);
                    break;
                case 'bold':
                    $entities[] = bold($this->parseInlines($matches['bold_text'][0]));
                    break;
                case 'italic':
                    $entities[] = italic($this->parseInlines($matches['italic_text_star'][0]));
                    break;
                case 'italic_underline':
                    $entities[] = italic($this->parseInlines($matches['italic_text_underline'][0]));
                    break;
                case 'strike':
                    $entities[] = strikethrough($this->parseInlines($matches['strike_text'][0]));
                    break;
                case 'spoiler':
                    $entities[] = spoiler($this->parseInlines($matches['spoiler_text'][0]));
                    break;
                case 'spoiler_custom_tag':
                    $entities[] = spoiler($this->parseInlines($matches['spoiler_custom_text'][0]));
                    break;
                case 'marked':
                     $entities[] = marked($this->parseInlines($matches['marked_text'][0]));
                    break;
                case 'math':
                    $entities[] = mathematicalExpression($matches['math_text'][0], false);
                    break;
                case 'footnote':
                    $entities[] = referenceLink($matches['footnote_id'][0], $matches['footnote_id'][0]);
                    break;
                case 'html':
                    /*
                    $tag = strtolower($matches[1][0]);
                    $content = $this->parseInlines($matches['html_text'][0]);
                    $entities[] = match($tag) {
                        'u', 'ins' => underline($content),
                        'sup' => superscript($content),
                        'sub' => subscript($content),
                        default => plain($matchText), // Failsafe
                    };
                    */
                    // FIX: Safely resolve tag. Prefer named group 'html_tag', fall back to positional index 1.
                    $tag = strtolower(
                        (isset($matches['html_tag']) && $matches['html_tag'][1] !== -1)
                            ? $matches['html_tag'][0]
                            : ($matches[1][0] ?? '')
                    );
                    $content = $this->parseInlines($matches['html_text'][0]);
                    $entities[] = match($tag) {
                        'u', 'ins' => underline($content),
                        'sup'      => superscript($content),
                        'sub'      => subscript($content),
                        default    => plain($matchText),
                    };
                    break;
                case 'mention':       $entities[] = mention($matchText); break;
                case 'hashtag':       $entities[] = hashtag($matchText); break;
                case 'cashtag':       $entities[] = cashtag($matchText); break;
                case 'bot_command':   $entities[] = botCommand($matchText); break;
                case 'email':         $entities[] = emailAddress($matchText); break;
                case 'phone_number':  $entities[] = phoneNumber($matchText); break;
            }

            // Move the offset to the end of the current match to continue searching from there.
            $offset = $matchOffset + strlen($matchText);
        }

        // Capture any remaining plain text at the end of the string.
        if ($offset < strlen($text)) {
            $entities[] = plain(substr($text, $offset));
        }

        // --- POWER-UP ACTIVATED ---
        // Activate the previously unused `consolidatePlaintext` method.
        // This merges adjacent plain text nodes, significantly reducing object count
        // and improving rendering performance.
        $entities = $this->consolidatePlaintext($entities);

        return $this->optimizeEntityContent($entities);
    }

    /**
     * Optimizes an array of entities. If the array contains only a single
     * RichTextPlain entity, it unwraps it and returns the raw string.
     * Otherwise, it returns the original array. This is the core trick
     * to reduce payload size and memory usage.
     *
     * @param array<RichEntity> $entities The input array of entities. /// array|RichEntity $entities
     * @return string|array The optimized content (either a raw string or the original array).
    */
    private function optimizeEntityContent(mixed $entities): string|RichEntity|array
    {
        if(is_array($entities)) {
            // The core optimization logic, tailored/bent for general use.
            if (count($entities) === 1 && $entities[0] instanceof RichTextPlain) {
                // Unwrap the single plain text object into a raw string.
                return $entities[0]->text;
            }
        }
        
        // Return the original array for complex content (multiple entities, nested entities, or empty).
        return $entities;
    }

    /* *
     * Finds placeholder objects in the final block array and replaces them with the actual complex block entities.
     * @param array<RichBlockEntity|RichTextPlain> $blocks The array of parsed blocks, which may contain placeholders.
     * @return array<RichBlockEntity> The final array of blocks with placeholders resolved.
     * /
    private function reinsertBlockEntitiesOld(array $blocks): array
    {
        if (empty($this->mdBlockEntities)) {
            return $blocks;
        }

        $finalBlocks = [];
        foreach ($blocks as $block) {
            // Check for the lightweight RichTextPlain placeholder object created in commitParagraph.
            if ($block instanceof RichTextPlain) {
                $placeholder = trim($block->text);
                if (isset($this->mdBlockEntities[$placeholder])) {
                    $finalBlocks[] = $this->mdBlockEntities[$placeholder];
                    continue; // Skip adding the placeholder itself.
                }
            }
            // Also check for the older paragraph-wrapped placeholder for compatibility/safety.
            elseif ($block instanceof RichBlockParagraph
                && count($block->text) === 1
                && $block->text[0] instanceof RichTextPlain
            ) {
                $placeholder = trim($block->text[0]->text);
                if (isset($this->mdBlockEntities[$placeholder])) {
                    $finalBlocks[] = $this->mdBlockEntities[$placeholder];
                    continue;
                }
            }
            $finalBlocks[] = $block;
        }
        return $finalBlocks;
    }
    */

    private function reinsertBlockEntities(array $blocks): array
    {
        if (empty($this->mdBlockEntities)) {
            return $blocks;
        }

        $finalBlocks = [];
        foreach ($blocks as $block) {
            $placeholder = null;

            if ($block instanceof RichTextPlain) {
                // Case 1: The placeholder is a raw RichTextPlain entity
                $placeholder = trim($block->text);
            }
            elseif (
                $block instanceof RichBlockParagraph
            &&
                /// && is_array($block->text)
                count($block->text) === 1
            &&
                $block->text[0] instanceof RichTextPlain
            ) {
                // Case 2: The placeholder is inside a Paragraph
                $placeholder = trim($block->text[0]->text);
            }

            /*elseif (
                $block instanceof RichBlockParagraph
            &&
                is_string($block->text)
            ) {
                // Handle cases where parseInlines optimized to a single string
                $placeholder = trim($block->text);
            }*/

            if ($placeholder !== null && isset($this->mdBlockEntities[$placeholder])) {
                $finalBlocks[] = $this->mdBlockEntities[$placeholder];
                unset($this->mdBlockEntities[$placeholder]); // Clean up the map
                continue;
            }

            $finalBlocks[] = $block;
        }

        // Add any remaining block entities that might not have been in a paragraph (edge case)
        // This is a fail-safe.
        return array_merge($finalBlocks, array_values($this->mdBlockEntities));
    }
    
    /**
     * A helper function to merge consecutive RichTextPlain entities into a single one.
     * This optimizes the entity tree and is essential for correct rendering.
     * @param array $entities An array of entities.
     * @return array The consolidated array of entities. Now This function ALWAYS returns an array.
     */
    private function consolidatePlaintext(array $entities): array
    {
        if (count($entities) < 2) {
            return $entities;
        }

        $consolidated = [];
        $buffer = '';

        foreach ($entities as $entity) {
            if ($entity instanceof RichTextPlain) {
                $buffer .= $entity->text;
            } else {
                if ($buffer !== '') {
                    $consolidated[] = plain($buffer);
                    $buffer = '';
                }
                $consolidated[] = $entity;
            }
        }

        if ($buffer !== '') {
            $consolidated[] = plain($buffer);
        }

        // FIX: The logic of this function is to consolidate, not optimize.
        // It must always return an array for the caller (parseInlines) to work with.
       // The final optimization to a potential string is handled once, at the end of parseInlines.
       return $consolidated;

        // Return the consolidated array, but still pass it through optimizer
        // in case the result is a single plain text entity.
        /// return $this->optimizeEntityContent($consolidated);
    }
}