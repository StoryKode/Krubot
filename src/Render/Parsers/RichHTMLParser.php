<?php

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

// PHP's native DOM extension for robust HTML parsing.
use DOMDocument;
use DOMNode;
use DOMElement;
use DOMText;
use DOMXPath;

use KrubiK\Facades\Parsentinel;
use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\RichElements\Texts\RichTextPlain;
use KrubiK\Render\RichElements\Texts\RichTextCode;
use KrubiK\Render\RichElements\Blocks\RichBlockEntity;
use KrubiK\Render\RichElements\Blocks\RichBlockList;
use KrubiK\Render\RichElements\Blocks\RichBlockListItem;
use KrubiK\Render\RichElements\Blocks\RichBlockDetails;
use KrubiK\Render\RichElements\Blocks\RichBlockPhoto;
use KrubiK\Render\RichElements\Blocks\RichBlockVideo;
use KrubiK\Render\RichElements\Blocks\RichBlockAnimation;
use KrubiK\Render\RichElements\Blocks\RichBlockParagraph;
use KrubiK\Render\RichElements\Blocks\RichBlockButtons;
use KrubiK\Render\RichElements\Components\RichButton;

// --- FACTORY HELPERS (DSL) ---
// Import all necessary helper functions to create RichEntity instances.
// This is the core of the v8.1 Hyper-DX-Refactoring.
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
    voiceNote,

    // 10.3 Support
    expandableBlockQuotation,
    button,
    buttons
};

/**
 * RichParser v8.1.8 (Factory Edition) - The Definitive, Multi-Stage Rich Message Builder.
 *
 * This version represents a significant architectural evolution, combining a Single
 * Source of Truth (SSoT) approach with a multi-stage parsing pipeline. All input
 * formats (RichMD, RichHTML, etc.) are first normalized into a RichHTML intermediate
 * representation.
 *
 * The pipeline then executes:
 * 1.  **Preprocessing Stage:** Scans the DOM for complex structures like footnotes,
 *     extracts their data, and removes them from the main tree to simplify parsing.
 * 2.  **Main Parsing Stage:** Recursively walks the sanitized DOM tree, converting
 *     HTML nodes into a stack of universal KrubiK\Render\RichElements\* objects.
 * 3.  **Post-processing Stage:** Appends the extracted data (e.g., footnote definitions)
 *     to the final parsed stack.
 *
 * This version (v8.1) refactors all object instantiations to use the dedicated
 * KrubiK\Render\Helpers functions, creating a fluent, readable, and maintainable DSL.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
 */
class RichHTMLParser implements SyntaxWarden
{
    /**
     * The final stack of RichEntity objects representing the message structure.
     * @var RichEntity[]
     */
    private array $stack = [];
    
    /**
     * The DOMDocument instance for the current parsing operation.
     * @var DOMDocument
     */
    private DOMDocument $dom;

    /**
     * The DOMXPath instance for efficient querying of the DOM.
     * @var DOMXPath
     */
    private DOMXPath $xpath;
    
    /**
     * Holds parsed footnote definitions found during the pre-processing stage.
     * The structure is: ['footnote_name' => RichBlockFootnoteDefinition, ...]
     * @var array<string, RichBlockFootnoteDefinition>
     */
    private array $footnoteDefinitions = [];

    /**
     * The primary entry point for parsing HTML & RichHTML documents into RichEntity[]
     * The core DOM-based parser orchestrator that converts RichHTML into the final object stack.
     *
     * @param string $html The normalized RichHTML string.
     * @return array<RichEntity> An array of rich entities representing the document structure.
     */
    public function decipher(string $html): array
    {
        // Reset state for a fresh parse.
        $this->stack = [];
        $this->footnoteDefinitions = [];
        
        if (empty(trim($html))) {
            return [];
        }

        $this->dom = new DOMDocument();
        // Suppress warnings for custom HTML5 tags (e.g., <tg-spoiler>, <details>).
        libxml_use_internal_errors(true);
        // Load the HTML, wrapping it to ensure correct parsing of fragments and UTF-8 handling.
        $this->dom->loadHTML(
            '<?xml encoding="UTF-8"><html><head><meta charset="UTF-8"></head><body>' . $html . '</body></html>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $this->xpath = new DOMXPath($this->dom);
        
        // --- STAGE 1: Pre-processing ---
        $this->preprocessFootnotes();

        // --- STAGE 2: Main Parsing ---
        $body = $this->dom->getElementsByTagName('body')->item(0);
        $mainEntities = [];
        if ($body) {
            foreach ($body->childNodes as $node) {
                $mainEntities = array_merge($mainEntities, $this->walkNode($node));
            }
        }

        // --- STAGE 3: Post-processing & Final Assembly ---
        // Append the processed footnote definitions to the end of the entity list.
        $this->stack = array_merge($mainEntities, array_values($this->footnoteDefinitions));

        // --- STAGE 4: Do The Final Duty ---
        return $this->stack;
    }
    
    /**
     * Retrieves the final stack of parsed RichEntity objects.
     *
     * @return RichEntity[] An array of RichEntity objects, possibly empty.
     */
    public function getStack(): array
    {
        return $this->stack;
    }    

    /**
     * The primary entry point for parsing any supported rich text format.
     *
     * @param string $text The input string to be parsed.
     * @param string $mode The format of the input string ('RichMD', 'RichHTML'). Defaults to 'RichMD'.
     * @return self Returns the instance of the parser for method chaining.
     */
    public function legacyParse(string $text, string $mode = 'RichMD'): self
    {

        if($mode === 'RichMD') {
            $this->stack = Parsentinel::summon('md')->decipher($text);
            return $this;
        }

        // Step 1: Normalize the input string to our intermediate RichHTML format.
        $html = match ($mode) {
            'RichHTML' => $text,
            default => htmlspecialchars($text, ENT_QUOTES, 'UTF-8'),
        };

        // Step 2: fullfill the stack[] by Execute the master multi-stage parser on the normalized HTML.
        $this->decipher($html);

        return $this;
    }

    /**
     * Recursively traverses a DOMNode, converting it and its children into our SSoT (Single Source of Truth) Rich Entities.
     * This method is the designated handler for all HTML content. This is the heart of our HTML-to-Object engine.
     *
     * @param DOMNode $node The DOMNode to process.
     * @return RichEntity[]
     */
    protected function walkNode(DOMNode $node): array
    {
        // Base Case 1: Handle text nodes.
        if ($node instanceof DOMText) {
            // Ignore whitespace-only text nodes often found between block elements.
            return trim($node->nodeValue) === '' ? [] : [plain($node->nodeValue)];
        }

        // Base Case 2: Handle line breaks.
        if ($node instanceof DOMElement && strtolower($node->nodeName) === 'br') {
            return [plain("\n")];
        }

        // Must be an element node from here.
        if (!$node instanceof DOMElement) {
            return [];
        }

        // Recursive Step: Process all child nodes first to build the content array.
        // Note: Some handlers (like `details` or `table`) may re-process or inspect
        // raw child nodes themselves for more complex logic.
        $children = [];
        foreach ($node->childNodes as $childNode) {
            $children = array_merge($children, $this->walkNode($childNode));
        }
        
        // Consolidate adjacent RichTextPlain objects for a cleaner, more optimized entity stack.
        $children = $this->consolidatePlaintext($children);
        $childrenIsString = $children && is_string($children);

        /*
        if($children) {
            if($children instanceof RichEntity)
                $children = $children->toArray();
        }
        */
        
        // The central mapping engine from HTML tags to our SSoT Objects.
        // This is where you would add support for every new HTML tag you need.
        $entityFromNode = match (strtolower($node->nodeName)) {
            // === Inline Text Formatting ===
            'b', 'strong'         => [bold($children)],
            'i', 'em'             => [italic($children)],
            'u', 'ins'            => [underline($children)],
            's', 'strike', 'del'  => [strikethrough($children)],
            'tg-spoiler'          => [spoiler($children)],
            'mark'                => [marked($children)],
            'sup'                 => [superscript($children)],
            'sub'                 => [subscript($children)],
            /// 'code'                => [code($this->extractTextFromChildren($children))],
            'code'                => $childrenIsString ? [code($children)] : (!empty($children) && $children[0] instanceof RichTextPlain ? [code($children[0]->text)] : []),
            'tg-math'             => [mathematicalExpression($this->extractTextFromChildren($children))],
            'span'                => '__KCONST_DANGER_UNK_ENTT__', // Span is transparent, just pass its children through.

            // === Links, Mentions, and Special Text Entities ===
            'a'                   => $this->createLinkEntity($node, $children),

            // === Custom Telegram Inline Entities ===
            'tg-emoji'            => [customEmoji($node->getAttribute('emoji-id'), $this->extractTextFromChildren($children))],
            'tg-time'             => [dateTime($this->extractTextFromChildren($children), (int)$node->getAttribute('unix'), $node->getAttribute('format'))],
            'tg-reference'        => [reference($children, $node->getAttribute('name'))],

            // === Block Elements: Standard ===
            // Note: `walkNode` might return block entities if it encounters them.
            'p'                   => [paragraph($children)],
            'div'                 => [paragraph($children)], // Treat divs like paragraphs by default.
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6' => [heading($children, ((int)substr($node->nodeName, 1)))],
            'blockquote'          => $this->createAnyBlockQuotation($node),
            'aside'               => [pullQuotation($children, $this->extractCite($node))],
            'pre'                 => $this->createPreformattedBlock($node, $children),
            'hr'                  => [divider()],
            'br'                  => [plain("\n")],
            'footer'              => [footer($children)],

            // === Lists (Advanced Handler) ===
            'ul', 'ol'            => [$this->createListBlock($node)],

            // === Tables (Advanced Handler) ===
            'table'               => [$this->createTableBlock($node)],

            // === Media & Figures (Advanced Handlers) ===
            'figure'              => $this->createFigureBlock($node),
            'img', 'video', 'audio', 'tg-document' => [$this->createMediaBlock($node)], // Standalone media

            // === Custom Telegram Block Entities ===
            'details'             => [$this->createDetailsBlock($node)],
            'tg-map'              => [map(['latitude' => $node->getAttribute('lat'), 'longitude' => $node->getAttribute('long')], (int)$node->getAttribute('zoom'), 0, 0)],
            'tg-collage'          => [collage($this->filterMediaChildren($children))],
            'tg-slideshow'        => [slideshow($this->filterMediaChildren($children))],
            'tg-math-block'       => [pre($this->extractTextFromChildren($children), 'math')],
            'tg-thinking'         => [thinking($children)],

            'tg-button'           => [button($this->createButtonEntity($node))],
            'tg-button-row'       => [$this->createButtonRowEntity($node)],

            'rich-md'             => Parsentinel::summon('md')->decipher($node->textContent),

            // Default Case: If a tag is not recognized (e.g., summary, tbody, li, cite, figcaption),
            // we don't create a parent object for it, but critically, we still process its children.
            // This ensures content is never lost from unknown structural tags.
            default               => '__KCONST_DANGER_UNK_ENTT__',
        };

        // Process Un-Processed Elements, if we retrive them their soul or destiny
        return ($entityFromNode === '__KCONST_DANGER_UNK_ENTT__') ?
        (
            $childrenIsString ? ([plain($children)])
            :
            $children /// Simulates `default => $children`,
        )
        :
        $entityFromNode; // result from `match (strtolower($node->nodeName)) {...}`
    }

    // --- Pre-processing Stage Helpers ---

    /**
     * Finds the footnote definition block, parses its content, stores it,
     * and then removes the block from the DOM to prevent re-processing.
     */
    private function preprocessFootnotes(): void
    {
        // Find the main container for footnotes.
        $footnotesDiv = $this->xpath->query('//div[@class="footnotes"]')->item(0);
        if (!$footnotesDiv) {
            return;
        }

        // Find all list items (each one is a footnote definition).
        $footnoteItems = $this->xpath->query('.//li[@id]', $footnotesDiv);

        foreach ($footnoteItems as $item) {
            $name = $item->getAttribute('id');
            // Skip if the item doesn't have a valid ID.
            if (!$name || !str_starts_with($name, 'fn:')) {
                continue;
            }

            // Clean up the back-reference link (e.g., "↩") before parsing content.
            $backRefLink = $this->xpath->query('.//a[@class="footnote-backref"]', $item)->item(0);
            if ($backRefLink) {
                $backRefLink->parentNode->removeChild($backRefLink);
            }
            
            // Parse the content of the footnote.
            $blocks = [];
            foreach ($item->childNodes as $child) {
                 $blocks = array_merge($blocks, $this->walkNode($child));
            }
            $blocks = $this->consolidatePlaintext($blocks);

            $this->footnoteDefinitions[$name] = footnoteDefinition($name, $blocks);
        }

        // Remove the entire footnotes div from the DOM to prevent it from being parsed again.
        $footnotesDiv->parentNode->removeChild($footnotesDiv);
    }
    
    // --- Complex Node Processing Helpers ---

    /**
     * Creates a link-based entity, intelligently determining the type from the href attribute and content.
     * @param DOMElement $node The <a> node.
     * @param RichEntity[] $children The child elements (link text).
     * @return RichEntity[]
     */
    private function createLinkEntity(DOMElement $node, array $children): array
    {
        $href = $node->getAttribute('href');

        // Handle named anchors (destinations, not links).
        if ($node->hasAttribute('name') && empty($href)) {
            return [anchor($node->getAttribute('name'))];
        }

        // Handle in-document anchor links.
        if (str_starts_with($href, '#')) {
            $refName = substr($href, 1);
            if (str_starts_with($refName, 'fn:')) {
                return [referenceLink($children, $refName)]; // Footnote link
            }
            if (str_starts_with($refName, 'note-')) {
                return [referenceLink($children, $refName)]; // Legacy note link
            }
            return [anchorLink($refName, $children)]; // Standard anchor link
        }
        
        // Handle Telegram-specific URI schemes.
        if (str_starts_with($href, 'tg://')) {
            $parsedUrl = parse_url($href);
            if (($parsedUrl['host'] ?? null) === 'user' && isset($parsedUrl['query'])) {
                parse_str($parsedUrl['query'], $params);
                return [textMention($children, ['id' => (int)$params['id']])];
            }
        }

        // Handle standard web URI schemes.
        if (str_starts_with($href, 'mailto:')) {
            return [emailAddress($children, substr($href, 7))];
        }
        if (str_starts_with($href, 'tel:')) {
            return [phoneNumber($children, substr($href, 4))];
        }

        // Heuristic entity recognition from text content for plain links.
        $text = $this->extractTextFromChildren($children);
        if (preg_match('/^\/(\w+)/', $text, $matches)) {
            return [botCommand($children, $matches[1])];
        }
        if (preg_match('/^#(\w+)/', $text, $matches)) {
            return [hashtag($children, $matches[1])];
        }
        if (preg_match('/^\$(\w+)/', $text, $matches)) {
            return [cashtag($children, $matches[1])];
        }
        if (preg_match('/^@(\w+)/', $text, $matches)) {
            return [mention($children, $matches[1])];
        }

        // Default to a generic URL.
        return [href($children, $href)];
    }
    
    /**
     * Creates a pre-formatted block, extracting language from the inner <code> class.
     * @param DOMElement $preNode The <pre> node.
     * @return RichEntity[]
     */
    private function createPreformattedBlock(DOMElement $preNode, mixed $children = null): array
    {
        $codeNode = $this->xpath->query('.//code', $preNode)->item(0);
        $content = $codeNode ? $codeNode->textContent : $preNode->textContent;
        $lang = '';
        if ($codeNode && $codeNode->hasAttribute('class')) {
            preg_match('/language-(\S+)/', $codeNode->getAttribute('class'), $matches);
            $lang = $matches[1] ?? '';
        }

        if(empty($lang) && $preNode->hasAttribute('lang'))
            $lang = trim($preNode->getAttribute('lang'));

        if($codeNode && empty($lang) && $codeNode->hasAttribute('lang'))
            $lang = trim($codeNode->getAttribute('lang'));

        $result = is_string($children)
        ?
            [pre($children, $lang)]
        :
            (
                ((!empty($children)) && ($children[0] instanceof RichTextPlain || $children[0] instanceof RichTextCode))
                ?
                    [pre($children[0]->text, $lang)]
                :
                    [pre($content, $lang)] /// []
            );


        return $result;
    }

    /**
     * Creates a highly-featured table block, parsing attributes, caption, and cell properties.
     * @param DOMElement $tableNode The <table> node.
     * @return RichEntity[]
     */
    private function createTableBlock(DOMElement $tableNode): array
    {
        $rows = [];
        $captionContent = null;

        $captionNode = $this->xpath->query('./caption', $tableNode)->item(0);
        if ($captionNode) {
            $captionContent = $this->walkNode($captionNode);
        }

        // XPath is more reliable for direct children than iterating childNodes with text nodes.
        $trNodes = $this->xpath->query('.//tr', $tableNode);

        foreach ($trNodes as $tr) {
            $cells = [];
            foreach ($tr->childNodes as $cellNode) {
                if ($cellNode instanceof DOMElement && in_array($cellNode->nodeName, ['td', 'th'])) {
                    $cells[] = tableCell(
                        $this->walkNode($cellNode),
                        $cellNode->nodeName === 'th',
                        (int)$cellNode->getAttribute('colspan') ?: 1,
                        (int)$cellNode->getAttribute('rowspan') ?: 1,
                        $cellNode->getAttribute('align'), // 'left', 'center', 'right'
                        $cellNode->getAttribute('valign') // 'top', 'middle', 'bottom'
                    );
                }
            }
            if (!empty($cells)) {
                $rows[] = $cells;
            }
        }
        
        return [table(
            $rows, 
            $tableNode->hasAttribute('bordered'), 
            $tableNode->hasAttribute('striped'),
            $captionContent
        )];
    }
    
    /**
     * Creates a media block from a standalone media tag (img, video, audio).
     * @param DOMElement $mediaNode The media node.
     * @return RichBlockEntity
     */
    private function createMediaBlock(DOMElement $mediaNode): RichBlockEntity
    {
        $src = $mediaNode->getAttribute('src');
        $isSpoiler = $mediaNode->hasAttribute('tg-spoiler');
        $nodeName = strtolower($mediaNode->nodeName);

        if ($nodeName === 'img') {
            return photo(['file_id' => $src], $isSpoiler);
        }

        if ($nodeName === 'tg-document') {
            return document(['file_id' => $src]);
        }

        $pathInfo = pathinfo(parse_url($src, PHP_URL_PATH) ?: '');
        $extension = strtolower($pathInfo['extension'] ?? '');

        if ($nodeName === 'video') {
            // A simple heuristic to differentiate animations from videos.
            if ($extension === 'gif' || ($extension === 'mp4' && $mediaNode->hasAttribute('autoplay'))) {
                return animation(['file_id' => $src], $isSpoiler);
            }
            return video(['file_id' => $src], $isSpoiler);
        }

        if ($nodeName === 'audio') {
            // A simple heuristic to differentiate voice notes from audio files.
            if ($extension === 'ogg' && $mediaNode->hasAttribute('controls') === false) {
                return voiceNote(['file_id' => $src]);
            }
            return audio(['file_id' => $src]);
        }

        // Fallback for unknown media types.
        return paragraph([plain("[Unsupported Media: {$src}]")]);
    }
    
    /**
     * Creates a complex figure block, associating media with its caption.
     * @param DOMElement $figureNode The <figure> node.
     * @return RichEntity[]
     */
    private function createFigureBlock(DOMElement $figureNode): array
    {
        $mediaNode = $this->xpath->query('.//img | .//video | .//audio | .//tg-map', $figureNode)->item(0);

        if (!$mediaNode) {
            return []; // No primary media found in figure.
        }
        
        // Create the primary media entity.
        $mediaEntity = strtolower($mediaNode->nodeName) === 'tg-map'
            ? map(['latitude' => $mediaNode->getAttribute('lat'), 'longitude' => $mediaNode->getAttribute('long')], (int)$mediaNode->getAttribute('zoom'), 0, 0)
            : $this->createMediaBlock($mediaNode);

        // Find and process the caption.
        $captionNode = $this->xpath->query('./figcaption', $figureNode)->item(0);
        if ($captionNode) {
            $captionContent = $this->walkNode($captionNode);
            $cite = $this->extractCite($captionNode);
            $caption = caption($captionContent, $cite);
            if (method_exists($mediaEntity, 'setCaption')) {
                $mediaEntity->setCaption($caption);
            }
        }

        return [$mediaEntity];
    }
    
    /**
     * Creates a List Block (<ul> or <ol>), acting as a controller that delegates
     * <li> creation to createListItem.
     * @param DOMElement $listNode The <ul> or <ol> node.
     * @return RichBlockList
     */
    private function createListBlock(DOMElement $listNode): RichBlockList
    {
        $isOrdered = strtolower($listNode->nodeName) === 'ol';
        $style = $isOrdered ? 'ordered' : 'bullet';
        
        $items = [];
        // Use XPath to reliably get only direct <li> children.
        $liNodes = $this->xpath->query('./li', $listNode);
        
        foreach ($liNodes as $liNode) {
            // Delegate the complex task of creating a list item to its specialized helper.
            $items[] = $this->createListItem($liNode, $isOrdered ? $listNode->getAttribute('type') : null);
        }
        
        $list = listBlock($items, $style);

        if ($isOrdered) {
            if ($listNode->hasAttribute('start')) {
                $list->setStart((int)$listNode->getAttribute('start'));
            }
            if ($listNode->hasAttribute('reversed')) {
                $list->setReversed(true);
            }
        }

        return $list;
    }

    /**
     * Creates a structured RichBlockListItem component from an <li> element,
     * detecting checkboxes, values, and separating content.
     * @param DOMElement $liNode The <li> node.
     * @param string|null $listType The type inherited from the parent <ol> (e.g., 'a', '1').
     * @return RichBlockListItem
     */
    private function createListItem(DOMElement $liNode, ?string $listType): RichBlockListItem
    {
        $has_checkbox = null;
        $is_checked = null;
        $blocks = [];
        
        $value = $liNode->hasAttribute('value') ? (int)$liNode->getAttribute('value') : null;

        // Iterate through raw child nodes of <li> to find special elements like checkboxes.
        foreach ($liNode->childNodes as $childNode) {
            // Check if the node is an <input type="checkbox"> tag.
            if ($childNode instanceof DOMElement && 
                strtolower($childNode->nodeName) === 'input' && 
                $childNode->getAttribute('type') === 'checkbox') 
            {
                $has_checkbox = true;
                $is_checked = $childNode->hasAttribute('checked');
                continue; // Skip adding the checkbox itself to content blocks.
            }
            
            // For all other nodes, process them recursively.
            $blocks = array_merge($blocks, $this->walkNode($childNode));
        }

        $blocks = $this->consolidatePlaintext($blocks);
        
        // The label (e.g., "1.", "a.", "•") is a rendering concern.
        // We provide raw data; the renderer constructs the final label.
        return listItem(
            label: '', 
            blocks: $blocks, 
            hasCheckbox: $has_checkbox, 
            isChecked: $is_checked, 
            value: $value, 
            type: $listType
        );
    }
    
    /**
     * Creates a Details block by separating the summary from the main content.
     * @param DOMElement $detailsNode The <details> DOM element.
     * @return RichBlockDetails
     */
    private function createDetailsBlock(DOMElement $detailsNode): RichBlockDetails
    {
        $isOpen = $detailsNode->hasAttribute('open');
        $summaryContent = [];
        $blocksContent = [];

        // Iterate through child nodes to separate <summary> from the rest.
        foreach ($detailsNode->childNodes as $childNode) {
            if ($childNode instanceof DOMElement && strtolower($childNode->nodeName) === 'summary') {
                // Process the children of <summary>, not the tag itself.
                foreach ($childNode->childNodes as $summaryChild) {
                    $summaryContent = array_merge($summaryContent, $this->walkNode($summaryChild));
                }
            } else {
                // All other nodes are part of the main blocks content.
                $blocksContent = array_merge($blocksContent, $this->walkNode($childNode));
            }
        }
        
        $summaryContent = $this->consolidatePlaintext($summaryContent);
        $blocksContent = $this->consolidatePlaintext($blocksContent);

        return details($summaryContent, $blocksContent, $isOpen);
    }

    // --- Utility Helpers ---

    /**
     * Helper to extract citation text from a <cite> tag within a node.
     * @param DOMElement $parentNode The blockquote, aside, or figcaption node.
     * @return RichEntity[]
     */
    private function extractCite(DOMElement $parentNode): array
    {
        $citeNode = $this->xpath->query('./cite', $parentNode)->item(0);
        return $citeNode ? $this->walkNode($citeNode) : [];
    }

    /**
     * Extracts plain text content from an array of RichEntity objects.
     * @param RichEntity[] $children
     * @return string
     */
    private function extractTextFromChildren(array $children): string
    {
        return implode('', array_map(fn($c) => $c->text ?? '', $children));
    }
    
    /**
     * Filters an array of children to only include media-type blocks for collages/slideshows.
     * @param RichEntity[] $children
     * @return RichBlockEntity[]
     */
    private function filterMediaChildren(array $children): array
    {
        return array_filter($children, fn($c) => 
            $c instanceof RichBlockPhoto ||
            $c instanceof RichBlockVideo ||
            $c instanceof RichBlockAnimation
        );
    }
    
    /**
     * Merges consecutive RichTextPlain objects into a single one for optimization.
     * @param RichEntity[] $entities
     * @return RichEntity[]
     */
    private function consolidatePlaintext(array $entities): array
    {
        if (count($entities) < 2) {
            return $entities;
        }
        $newEntities = [];
        $buffer = '';
        foreach ($entities as $entity) {
            if ($entity instanceof RichTextPlain) {
                $buffer .= $entity->text;
            } else {
                if ($buffer !== '') {
                    $newEntities[] = plain($buffer);
                    $buffer = '';
                }
                $newEntities[] = $entity;
            }
        }
        if ($buffer !== '') {
            $newEntities[] = plain($buffer);
        }
        return $newEntities;
    }

    protected function createAnyBlockQuotation(DOMElement $node) {
        $isExpandable = $node->hasAttribute('expandable');
    
        $credit = $this->extractCite($node);
    
        if ($isExpandable) {
            // محتوای بلاک‌کوتیشن expandable را به صورت یک RichEntity یا رشته استخراج می‌کنیم
            // توجه: $children به صورت آرایه نیست، بلکه باید به صورت text یا RichEntity باشد
            $text = $this->extractTextFromChildren($this->walkNode($node));
            return [expandableBlockQuotation($text, $credit)];
        } else {
            $children = [];
            foreach ($node->childNodes as $childNode) {
                $children = array_merge($children, $this->walkNode($childNode));
            }
            $children = $this->consolidatePlaintext($children);
            return [blockQuotation($children, $credit)];
        }
    }

    protected function createButtonEntity(DOMElement $buttonNode): RichButton
    {
        // 1. استخراج متن داخل دکمه (ممکن است شامل نودهای داخلی مثل <tg-time> یا <tg-emoji> باشد)
        $children = $this->walkNode($buttonNode);
        $textContent = $this->extractTextFromChildren($children);

        // 2. استخراج type و style
        $type = $buttonNode->getAttribute('type') ?: null;
        $style = $buttonNode->getAttribute('style') ?: null;

        // 3. ساخت نمونه RichButton{PowerButton} با متن و type
        $btn = RichButton::make($textContent, null, $type);

        // 4. تنظیم style اگر موجود بود
        if ($style !== null) {
            $btn->style($style);
        }

        // 5. بر اساس نوع دکمه، تنظیم سایر خصوصیات
        switch ($type) {
            case 'url':
            case 'web_app':
                $url = $buttonNode->getAttribute('url') ?: null;
                if ($url !== null) {
                    if ($type === 'web_app') {
                        $btn->webApp($url);
                    } else {
                        $btn->url($url);
                    }
                }
                break;

            case 'login_url':
                $url = $buttonNode->getAttribute('url') ?: null;
                $forwardText = $buttonNode->getAttribute('forward-text') ?: null;
                $requestWriteAccess = $buttonNode->hasAttribute('request-write-access');
                $loginUrlData = [
                    'url' => $url,
                    'forward_text' => $forwardText,
                    'request_write_access' => $requestWriteAccess,
                ];
                $btn->loginUrl($loginUrlData);
                break;

            case 'callback_data':
                // Resolve callback data attribute with fallback priority
                $data = $buttonNode->getAttribute('callback_data') 
                    ?: $buttonNode->getAttribute('data') 
                    ?: null;
                if ($data !== null) {
                    $btn->callbackData($data);
                }
                break;

            case 'switch_inline_query':
                $query = $buttonNode->getAttribute('query') ?: null;
                $btn->switchInlineQuery($query);
                break;

            case 'switch_inline_query_current_chat':
                $query = $buttonNode->getAttribute('query') ?: null;
                $btn->switchInlineQueryCurrentChat($query);
                break;

            case 'switch_inline_query_chosen_chat':
                $query = $buttonNode->getAttribute('query') ?: null;
                $allowUserChats = $buttonNode->hasAttribute('allow-user-chats');
                $allowBotChats = $buttonNode->hasAttribute('allow-bot-chats');
                $allowGroupChats = $buttonNode->hasAttribute('allow-group-chats');
                $allowChannelChats = $buttonNode->hasAttribute('allow-channel-chats');
                $btn->switchInlineQueryChosenChat(
                    null,
                    $query,
                    $allowUserChats,
                    $allowBotChats,
                    $allowGroupChats,
                    $allowChannelChats
                );
                break;

            case 'copy_text':
                $copyText = $buttonNode->getAttribute('text') ?: null;
                $btn->copyText($copyText);
                break;

            case 'disabled':
                $btn->disabled(true);
                break;

            default:
                // اگر type تعریف نشده یا ناشناخته است، کاری انجام نمی‌دهیم یا می‌توانیم نوع پیش‌فرض را تنظیم کنیم.
                break;
        }

        // 6. بازگشت RichButton
        return $btn;
    }

    protected function createButtonRowEntity(DOMElement $rowNode): RichBlockButtons
    {
        // استخراج تمام دکمه‌های <tg-button> داخل این ردیف
        $buttons = [];

        foreach ($rowNode->childNodes as $childNode) {
            if ($childNode instanceof DOMElement && strtolower($childNode->nodeName) === 'tg-button') {
                $buttons[] = $this->createButtonEntity($childNode);
            }
        }

        $align = $rowNode->getAttribute('align') ?: null;

        return buttons($buttons, $align);
    }


    /// OLDER VERSIONZ :::   
    //================================================================================
    // HYPER-DX: Dedicated HTML Processing Logic
    //================================================================================

    /**
     * Processes a string containing an HTML block using DOMDocument and delegates node processing to walkNode.
     * @param string $html The raw HTML string for the block.
     * @return array An array of RichBlockEntity or RichEntity.
     */
    protected function processHtmlBlockOld(string $html): array
    {
        // Suppress warnings for malformed HTML, as we want to be lenient.
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        // Load the HTML, wrapping it in a body tag and specifying UTF-8 to handle various characters correctly.
        $dom->loadHTML('<?xml encoding="UTF-8"><body>' . $html . '</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $body = $dom->getElementsByTagName('body')->item(0);
        if (!$body) {
            // If parsing fails completely, fall back to treating it as a code block.
            return [pre($html, 'html')];
        }

        $entities = [];
        foreach ($body->childNodes as $node) {
            $entities = array_merge($entities, $this->walkNode($node));
        }
        
        // This is a good place to convert a flat list of inline entities into a paragraph block.
        if (!empty($entities) && $entities[0] instanceof RichEntity) {
             return [paragraph($entities)];
        }
        
        return $entities;
    }
}
