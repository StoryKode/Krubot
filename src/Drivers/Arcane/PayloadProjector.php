<?php

namespace KrubiK\Drivers\Arcane;
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

// Import ProRendering Toolkit
use KrubiK\Render\RichMan;
use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\RichElements\Blocks\RichBlockEntity;
use function KrubiK\Render\Helpers\paragraph;

/**
 * Trait Telegram-Exclusive Rich_Payload Node-Reshaper
 * 
 * Projects rich message structures into a platform-ready payload.
 * 
 * Projects RichMan structures into Telegram Bot API 10.3+ correct payloads,
 * Resolving native blocks, entities, buttons and fallbacks while recursively
 * normalizing, sanitizing items and enforcing structural, textual, table, media and
 * depth limits (defined in TG API). Preserves intentional formatting and RTL
 * semantics while preventing unsupported or malformed content from reaching Telegram and make 400 ERROR.
 *
 * @internal Platform projection layer
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
trait PayloadProjector
{

    /**
     * Official Telegram Rich Message limits (overridable via config).
     *
     * @see https://core.telegram.org/bots/api#rich-message-limits
     *
     * @return array{max_chars:int, max_blocks:int, max_depth:int, max_media:int, max_table_columns:int}
    */
    public function getRichMessageLimits(array $config): array
    {
        $cfg = $config['rich_limits'] ?? [];

        return [
            'max_chars'         => (int) ($cfg['max_chars']         ?? 32768),
            'max_blocks'        => (int) ($cfg['max_blocks']        ?? 500),
            'max_depth'         => (int) ($cfg['max_depth']         ?? 16),
            'max_media'         => (int) ($cfg['max_media']         ?? 50),
            'max_table_columns' => (int) ($cfg['max_table_columns'] ?? 20),
        ];
    }

    /**
     * Build a valid InputRichMessage payload for Telegram Bot API 10.3+.
     * Handles both native blocks and fallback strategies while enforcing
     * all official limits (configurable via $this->config['rich_limits']).
    */
    public function generateTelegramRichPayload(RichMan $richMan, ?array $config = null): array
    {
        $config ??= $this->config ?? [];
        $fallback = strtolower($config['rich_render_fallback'] ?? 'text');
        $fallback = in_array($fallback, ['text', 'omit', 'markdown'], true) ? $fallback : 'text';

        $limits = $this->getRichMessageLimits($config);

        $blocks = $this->buildTelegramRichBlocks($richMan->getElements(), $fallback);

        if ($blocks === null) {
            // Fallback to markdown mode when native blocks cannot be produced
            $richMan->autoMarkdown(true);
            $message = [
                'markdown' => $richMan->toText(),
            ];
        } else {
            // Normalize wire format + enforce hard limits before shipping to Telegram
            $message = [
                'blocks' => $this->normalizeAndEnforceRichBlocks($blocks, $limits),
            ];
        }

        if ($richMan->isRtl !== null) {
            $message['is_rtl'] = (bool) $richMan->isRtl;
        }

        return $message;
    }

    /**
     * Convert RichMan elements into a raw array of blocks.
     * Returns null when markdown fallback is required.
     *
     * Non-native blocks (isTgNative() === false) are never sent as-is.
     * They are converted via toText() → paragraph (text mode)
     * or force the whole document into markdown mode.
    */
    protected function buildTelegramRichBlocks(iterable $elements, string $fallback): ?array
    {
        $blocks = [];

        foreach ($elements as $element) {

            // -------------------------------------------------------
            // 1. Structural blocks (RichBlockEntity)
            // -------------------------------------------------------
            if ($element instanceof RichBlockEntity) {

                // Native Telegram block → use its official toArray()
                if ($element->isTgNative()) {
                    $blocks[] = $element->toArray();
                    continue;
                }

                // ---------- Non-native block ----------
                // Examples: RichBlockNewLine, RichBlockSeparator, custom extensions...

                if ($fallback === 'markdown') {
                    // One non-native block forces the entire message into markdown mode.
                    return null;
                }

                if ($fallback === 'text') {
                    $text = $this->richElementText($element);
                    if ($text !== null && $text !== '') {
                        // Convert unsupported block into a legal paragraph
                        $blocks[] = paragraph($text)->toArray();
                    }
                    // if toText() returned empty → silently drop (omit behaviour)
                }

                // 'omit' mode → do nothing
                continue;
            }

            // -------------------------------------------------------
            // 2. Inline / text-level entities (RichEntity)
            // -------------------------------------------------------
            if ($element instanceof RichEntity) {

                if ($element->isTgNative()) {
                    // Promote inline entity to a paragraph block
                    $blocks[] = paragraph($element)->toArray();
                    continue;
                }

                // Non-native inline entity
                if ($fallback === 'markdown') {
                    // again even One non-native element, forces the entire message into markdown mode.
                    return null;
                }

                if ($fallback === 'text') {
                    $text = $this->richElementText($element);
                    if ($text !== null && $text !== '') {
                        $blocks[] = paragraph($text)->toArray();
                    }
                }
                continue;
            }

            // -------------------------------------------------------
            // 3. Defensive / scalar fallback
            // -------------------------------------------------------
            if ($fallback === 'markdown') {
                return null;
            }

            if ($fallback === 'text' && is_scalar($element)) {
                $blocks[] = paragraph((string) $element)->toArray();
            }
        }

        return array_values($blocks);
    }

    /**
     * Recursively normalize the entire block tree to Telegram's official wire format
     * and enforce all configured limits (chars / blocks / depth / media / table columns).
     * Also collapses consecutive empty / whitespace-only paragraphs that would create
     * unwanted vertical space.
    */
    protected function normalizeAndEnforceRichBlocks(array $blocks, array $limits): array
    {
        $state = [
            'blockCount' => 0,
            'mediaCount' => 0,
            'charCount'  => 0,
        ];

        $normalized = [];
        foreach ($blocks as $block) {
            $clean = $this->normalizeBlock($block, 1, $limits, $state);
            if ($clean !== null) {
                $normalized[] = $clean;
            }
            if ($state['blockCount'] >= $limits['max_blocks']) {
                break;
            }
        }

        // Final pass: collapse consecutive empty/whitespace paragraphs
        return $this->collapseEmptyParagraphs($normalized);
    }

    /**
     * Remove consecutive empty or whitespace-only paragraph blocks.
     * Keeps at most one blank paragraph when it is intentionally used as a separator,
     * but never emits chains like:
     *   {"type":"paragraph","text":" "}, {"type":"paragraph","text":" "}, ...
    */
    protected function collapseEmptyParagraphs(array $blocks): array
    {
        $result = [];
        $lastWasEmpty = false;

        foreach ($blocks as $block) {
            if (!is_array($block) || ($block['type'] ?? null) !== 'paragraph') {
                $result[] = $block;
                $lastWasEmpty = false;
                continue;
            }

            $text = $block['text'] ?? '';
            $isEmpty = $this->isEmptyRichText($text);

            if ($isEmpty) {
                if ($lastWasEmpty) {
                    // skip consecutive empty paragraphs
                    continue;
                }
                // keep a single empty paragraph only if it actually contains a newline
                // (intentional visual break). Pure spaces are dropped.
                if (is_string($text) && str_contains($text, "\n")) {
                    $result[] = ['type' => 'paragraph', 'text' => "\n"];
                    $lastWasEmpty = true;
                }
                // pure " " or "" → drop completely
                continue;
            }

            $result[] = $block;
            $lastWasEmpty = false;
        }

        return $result;
    }

    /**
     * Returns true when the RichText value is empty or contains only whitespace.
    */
    protected function isEmptyRichText(mixed $text): bool
    {
        if ($text === null || $text === '') {
            return true;
        }
        if (is_string($text)) {
            return trim($text) === '';
        }
        if (is_array($text)) {
            if (array_is_list($text)) {
                foreach ($text as $part) {
                    if (!$this->isEmptyRichText($part)) {
                        return false;
                    }
                }
                return true;
            }
            // typed node – check its "text" child
            if (isset($text['text'])) {
                return $this->isEmptyRichText($text['text']);
            }
        }
        return false;
    }

    /**
     * Normalize a single block (and all nested children) while updating limit counters.
     * Returns null when the block must be dropped due to depth or block-count limits.
    */
    protected function normalizeBlock(mixed $block, int $depth, array $limits, array &$state): ?array
    {
        if (!is_array($block) || empty($block['type'])) {
            return null;
        }

        if ($depth > $limits['max_depth']) {
            return null;
        }

        $state['blockCount']++;
        if ($state['blockCount'] > $limits['max_blocks']) {
            return null;
        }

        $type = $block['type'];

        // Media budget
        static $mediaTypes = ['photo', 'video', 'animation', 'audio', 'voice_note', 'document'];
        if (in_array($type, $mediaTypes, true)) {
            $state['mediaCount']++;
            if ($state['mediaCount'] > $limits['max_media']) {
                return null;
            }
        }

        // Table column hard limit – silently truncate excess columns
        if ($type === 'table' && isset($block['cells']) && is_array($block['cells'])) {
            $block['cells'] = array_map(
                static function ($row) use ($limits) {
                    return is_array($row)
                        ? array_slice($row, 0, $limits['max_table_columns'])
                        : $row;
                },
                $block['cells']
            );
        }

        // -------------------------------------------------------
        // Buttons block – force official RichMessageButton shape
        // -------------------------------------------------------
        if ($type === 'buttons' && isset($block['buttons']) && is_array($block['buttons'])) {
            $cleanButtons = [];
            foreach ($block['buttons'] as $btn) {
                if (!is_array($btn)) {
                    continue;
                }
                $clean = $this->normalizeRichMessageButton($btn, $depth, $limits, $state);
                if ($clean !== null) {
                    $cleanButtons[] = $clean;
                }
            }
            $block['buttons'] = $cleanButtons;

            if (isset($block['align']) && !in_array($block['align'], ['left', 'center', 'right'], true)) {
                unset($block['align']);
            }
        }

        // Normalize all text-bearing fields
        foreach (['text', 'credit', 'summary', 'caption'] as $field) {
            if (isset($block[$field])) {
                $block[$field] = $this->normalizeRichText($block[$field], $depth + 1, $limits, $state);
            }
        }

        // Nested block collections
        foreach (['blocks', 'items'] as $key) {
            if (!isset($block[$key]) || !is_array($block[$key])) {
                continue;
            }

            $children = [];
            foreach ($block[$key] as $child) {
                if ($type === 'list' && $key === 'items') {
                    // List items require proper RichBlock children
                    $item = $this->normalizeListItem($child, $depth + 1, $limits, $state);
                    if ($item !== null) {
                        $children[] = $item;
                    }
                } else {
                    $clean = $this->normalizeBlock($child, $depth + 1, $limits, $state);
                    if ($clean !== null) {
                        $children[] = $clean;
                    }
                }

                if ($state['blockCount'] >= $limits['max_blocks']) {
                    break;
                }
            }
            $block[$key] = $children;
        }

        // Table cells – normalize their text content
        if ($type === 'table' && isset($block['cells']) && is_array($block['cells'])) {
            foreach ($block['cells'] as &$row) {
                if (!is_array($row)) {
                    continue;
                }
                foreach ($row as &$cell) {
                    if (is_array($cell) && array_key_exists('text', $cell)) {
                        $cell['text'] = $this->normalizeRichText($cell['text'], $depth + 1, $limits, $state);
                    }
                }
                unset($cell);
            }
            unset($row);
        }

        return $block;
    }

    /**
     * Normalize a single button (used by both RichBlockButtons and RichTextButton)
     * into the official RichMessageButton wire shape.
     *
     * Accepts the messy internal format produced by the builder and returns
     * a clean array or null if the button is invalid and should be dropped.
    */
    protected function normalizeRichMessageButton(array $btn, int $depth, array $limits, array &$state): ?array
    {
        $clean = [];

        // text (required)
        if (isset($btn['text'])) {
            $clean['text'] = $this->normalizeRichText($btn['text'], $depth + 1, $limits, $state);
        } elseif (isset($btn['button_text'])) {
            $clean['text'] = $this->normalizeRichText($btn['button_text'], $depth + 1, $limits, $state);
        } else {
            return null; // invalid
        }

        // style (optional)
        if (isset($btn['style']) && in_array($btn['style'], ['danger', 'success', 'primary', 'link'], true)) {
            $clean['style'] = $btn['style'];
        }

        // Must have at least one action
        $hasAction = false;

        // Exactly one action field (first match wins)
        if (isset($btn['disabled'])) {
            $clean['disabled'] = new \stdClass(); // → {} || = $btn['disabled'];
            $hasAction = true;
        } elseif (isset($btn['callback_data'])) {
            $clean['callback_data'] = (string) $btn['callback_data'];
            $hasAction = true;
        } elseif (isset($btn['action_data'])) {
            $clean['callback_data'] = is_array($btn['action_data'])
                ? json_encode($btn['action_data'])
                : (string) $btn['action_data'];
            $hasAction = true;
        } elseif (isset($btn['url'])) {
            $clean['url'] = (string) $btn['url'];
            $hasAction = true;
        } else {

            $actionKeys = [
                'web_app',
                'login_url',
                'switch_inline_query',
                'switch_inline_query_current_chat',
                'switch_inline_query_chosen_chat',
                'copy_text',
            ];

            foreach ($actionKeys as $key) {
                if (isset($btn[$key])) {
                    $clean[$key] = $btn[$key];
                    $hasAction = true;
                    break; // only one action allowed
                }
            }

        }

        if (!$hasAction) {
            return null;
        }

        return $clean;
    }

    /**
     * Ensure a list item always contains an array of proper RichBlock objects.
     *
     * Key intelligence:
     * - Strips illegal "label" field (InputRichBlockListItem has no label)
     * - Groups consecutive inline RichText nodes into a SINGLE paragraph
     * - Only starts a new paragraph when a real block or intentional break appears
     * - Collapses pure-whitespace noise
    */
    protected function normalizeListItem(mixed $item, int $depth, array $limits, array &$state): ?array
    {
        if (!is_array($item)) {
            return null;
        }

        if ($depth > $limits['max_depth']) {
            return null;
        }

        $state['blockCount']++; // the list-item node itself counts

        // CRITICAL: InputRichBlockListItem must NOT contain "label"
        unset($item['label']);

        $rawBlocks = $item['blocks'] ?? [];
        if (!is_array($rawBlocks)) {
            $rawBlocks = [];
        }

        $knownBlockTypes = [
            'paragraph', 'heading', 'pre', 'footer', 'divider', 'list',
            'blockquote', 'expandable_blockquote', 'pullquote', 'table',
            'details', 'buttons', 'photo', 'video', 'animation', 'audio',
            'document', 'voice_note', 'map', 'collage', 'slideshow',
            'mathematical_expression', 'anchor', 'thinking',
        ];

        $cleanBlocks = [];
        $inlineBuffer = [];   // accumulates consecutive inline RichText

        $flushInlineBuffer = function () use (&$cleanBlocks, &$inlineBuffer, &$state, $depth, $limits) {
            if (empty($inlineBuffer)) {
                return;
            }

            // Normalize every piece and drop pure-empty ones
            $merged = [];
            foreach ($inlineBuffer as $piece) {
                $n = $this->normalizeRichText($piece, $depth + 1, $limits, $state);
                if (!$this->isEmptyRichText($n)) {
                    $merged[] = $n;
                }
            }

            if (!empty($merged)) {
                // Single paragraph containing the whole inline sequence
                $cleanBlocks[] = [
                    'type' => 'paragraph',
                    'text' => count($merged) === 1 ? $merged[0] : $merged,
                ];
                $state['blockCount']++;
            }

            $inlineBuffer = [];
        };

        foreach ($rawBlocks as $child) {
            // Already a real structural block?
            if (is_array($child) && isset($child['type']) && in_array($child['type'], $knownBlockTypes, true)) {
                $flushInlineBuffer(); // finish any pending inline content first

                $clean = $this->normalizeBlock($child, $depth + 1, $limits, $state);
                if ($clean !== null) {
                    $cleanBlocks[] = $clean;
                }
                continue;
            }

            // Everything else is treated as inline RichText → buffer it
            $inlineBuffer[] = $child;

            if ($state['blockCount'] >= $limits['max_blocks']) {
                break;
            }
        }

        // Flush whatever remains
        $flushInlineBuffer();

        // Final safety collapse (should be almost no-op now)
        $item['blocks'] = $this->collapseEmptyParagraphs($cleanBlocks);

        return $item;
    }

    /**
     * Convert any RichText value to the official Telegram wire format:
     *   - plain text/ {"type":"plain",...}     → bare JSON string
     *   - array of RichText                    → array of normalized values
     *   - typed object                         → keep type + recursively normalize children
     *
     * Also accumulates character count against the configured limit.
    */
    protected function normalizeRichText1(mixed $text, int $depth, array $limits, array &$state): mixed
    {
        if ($depth > $limits['max_depth']) {
            return '';
        }

        // Already correct plain text
        if (is_string($text)) {
            $state['charCount'] += mb_strlen($text, 'UTF-8');
            return $text;
        }

        if (is_scalar($text)) {
            $str = (string) $text;
            $state['charCount'] += mb_strlen($str, 'UTF-8');
            return $str;
        }

        if (!is_array($text)) {
            return '';
        }

        // List of RichText parts
        if (array_is_list($text)) {
            $clean = [];
            foreach ($text as $part) {
                $normalized = $this->normalizeRichText($part, $depth + 1, $limits, $state);
                if ($normalized !== '' && $normalized !== null) {
                    $clean[] = $normalized;
                }
                if ($state['charCount'] >= $limits['max_chars']) {
                    break;
                }
            }
            return $clean;
        }

        // Broken plain object produced by some helpers: {"text":"foo"} (no type)
        if (isset($text['text']) && !isset($text['type']) && count($text) <= 2) {
            return $this->normalizeRichText($text['text'], $depth, $limits, $state);
        }

        // Typed RichText node
        if (isset($text['type'])) {
            // Recurse into the nested text payload
            if (array_key_exists('text', $text)) {
                $text['text'] = $this->normalizeRichText($text['text'], $depth + 1, $limits, $state);
            }

            // Count extra string fields that also contribute to the character budget
            foreach (['bank_card_number', 'bot_command', 'hashtag', 'cashtag', 'username', 'url', 'email', 'phone_number', 'name'] as $extra) {
                if (isset($text[$extra]) && is_string($text[$extra])) {
                    $state['charCount'] += mb_strlen($text[$extra], 'UTF-8');
                }
            }

            return $text;
        }

        // Last-resort fallback
        if (isset($text['text'])) {
            return $this->normalizeRichText($text['text'], $depth, $limits, $state);
        }

        return '';
    }

    /**
     * Convert any RichText value to the official Telegram wire format:
     *   - plain text / {"type":"plain",...}  → bare JSON string
     *   - array of RichText                  → array of normalized values
     *   - typed object                       → keep type + recursively normalize children
     *
     * Also accumulates character count against the configured limit.
    */
    protected function normalizeRichText(mixed $text, int $depth, array $limits, array &$state): mixed
    {
        if ($depth > $limits['max_depth']) {
            return '';
        }

        // Already correct plain text
        if (is_string($text)) {
            $state['charCount'] += mb_strlen($text, 'UTF-8');
            return $text;
        }

        if (is_scalar($text)) {
            $str = (string) $text;
            $state['charCount'] += mb_strlen($str, 'UTF-8');
            return $str;
        }

        if (!is_array($text)) {
            return '';
        }

        // List of RichText parts
        if (array_is_list($text)) {
            $clean = [];
            foreach ($text as $part) {
                $normalized = $this->normalizeRichText($part, $depth + 1, $limits, $state);
                if ($normalized !== '' && $normalized !== null) {
                    $clean[] = $normalized;
                }
                if ($state['charCount'] >= $limits['max_chars']) {
                    break;
                }
            }
            return $clean;
        }

        // Explicit "plain" type OR broken plain object → bare string
        // {"type":"plain","text":"foo"}  OR  {"text":"foo"}
        if (
            (isset($text['type']) && $text['type'] === 'plain') ||
            (isset($text['text']) && !isset($text['type']) && count($text) <= 2)
        ) {
            return $this->normalizeRichText($text['text'] ?? '', $depth, $limits, $state);
        }

        // Typed RichText node
        if (isset($text['type'])) {

            // -------------------------------------------------------
            // Special case: RichTextButton (inline single button)
            // -------------------------------------------------------
            if ($text['type'] === 'button' && isset($text['button']) && is_array($text['button'])) {
                $cleanBtn = $this->normalizeRichMessageButton($text['button'], $depth, $limits, $state);
                if ($cleanBtn === null) {
                    return ''; // invalid → drop
                }
                return [
                    'type'   => 'button',
                    'button' => $cleanBtn,
                ];
            }

            if (array_key_exists('text', $text)) {
                $text['text'] = $this->normalizeRichText($text['text'], $depth + 1, $limits, $state);
            }

            foreach (['bank_card_number', 'bot_command', 'hashtag', 'cashtag', 'username', 'url', 'email', 'phone_number', 'name'] as $extra) {
                if (isset($text[$extra]) && is_string($text[$extra])) {
                    $state['charCount'] += mb_strlen($text[$extra], 'UTF-8');
                }
            }

            return $text;
        }

        // Last-resort fallback
        if (isset($text['text'])) {
            return $this->normalizeRichText($text['text'], $depth, $limits, $state);
        }

        return '';
    }

    /**
     * Extract plain-text representation from a RichEntity (used for fallback path).
    */
    protected function richElementText(RichEntity $element): ?string
    {
        if (!is_callable([$element, 'toText'])) {
            return null;
        }

        $text = $element->toText();

        return is_scalar($text) ? (string) $text : null;
    }
}
