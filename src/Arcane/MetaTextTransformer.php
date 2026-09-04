<?php

namespace KrubiK\Arcane;
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

// --- Import all the necessary Rich Entity classes ---
// This ensures we have type information for our match statement.
use KrubiK\Render\RichMan;
use KrubiK\Facades\Parsentinel; // KrubiK Global Input Parser
use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\RichElements\Texts\RichTextBold;
use KrubiK\Render\RichElements\Texts\RichTextCode;
use KrubiK\Render\RichElements\Texts\RichTextItalic;
use KrubiK\Render\RichElements\Texts\RichTextPlain;
use KrubiK\Render\RichElements\Texts\RichTextPre;
use KrubiK\Render\RichElements\Texts\RichTextSpoiler;
use KrubiK\Render\RichElements\Texts\RichTextStrikethrough;
use KrubiK\Render\RichElements\Texts\RichTextUnderline;
use KrubiK\Render\RichElements\Texts\RichTextUrl;
use KrubiK\Render\RichElements\Blocks\RichBlockParagraph;
use KrubiK\Render\RichElements\Blocks\RichBlockExpandableBlockQuotation; // TG 10.3 API in Rubika, this is how KrubiK Works@!
// ... add other entities if needed for rendering.

/**
 * DevGX MetaTextTransformer for Rubika - The Ultimate, Feature-Complete Rubika-API compatible.
 *
 * Now augmented with domain-specific knowledge from API analysis to produce
 * metadata for features like code block languages and rich MentionText objects.
 *
 * This class leverages the powerful, multi-pass Parsentinel engine
 * to parse complex Rich:: _Markdown/_HTML/_Bladen codes into a structured object tree (SSoT).
 * It then renders this tree into the flat metadata format required by the Rubika API.
 *
 * It represents the best of robust architecture, targeted output and feature-rich rendering.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
 */
trait MetaTextTransformer
{
    /**
     * @var array<string, string> Maps URL prefixes to Rubika's MentionText object types.
     * This is a critical piece of domain knowledge for creating rich mentions.
     */
    private const MENTION_PREFIX_TYPES = [
        'u' => 'User',
        'g' => 'Group',
        'c' => 'Channel',
        'b' => 'Bot',
    ];

    /**
     * Main public entry point.
    */
    public function parseToRubika(array|string|RichMan $input, ?string $format = 'auto'): array
    {
        $richEntities = [];

        // Step 1: Use the powerful engine to convert the raw text into a
        // structured tree of RichEntity objects (SSoT).

        if($input instanceof RichMan) {
            $richEntities = $input->toArray(); // Use the object's own array representation.
        }
        elseif(is_string($input)) {

            $specialist = Parsentinel::summon($format);
            $richEntities = $specialist->decipher($input);
        }
        else
            $richEntities = $input;

        // Step 2: Render this structured tree into the flat Rubika metadata format.
        return $this->renderEntitiesToMetadata($richEntities);
    }

    /**
     * Renders an array of RichBlockEntity objects into the final text/metadata format.
     * This is the "flattener" that traverses the SSoT.
     *
     * @param array<RichBlockEntity> $entities The array of block entities from the parser.
     * @return array{text: string, metadata?: array{meta_data_parts: array<int, array<string, mixed>>}}
    */
    private function renderEntitiesToMetadata(array $entities): array
    {
        $finalText = '';
        $metadataParts = [];
        $utf16Offset = 0; // The current character offset, measured in UTF-16 units.

        // Process each block entity sequentially.
        foreach ($entities as $index => $entity) {
            // This is the recursive heart of the renderer.
            $this->walkAndRenderMetadata($entity, $finalText, $metadataParts, $utf16Offset);

            if ($index < count($entities) - 1) {
                // Use a single newline between blocks for a more compact representation,
                // which is common in messaging apps. Change to "\n\n" if needed.
                $finalText .= "\n";
                $utf16Offset += 1;
            }

            /*
            // Add double newlines between block elements for proper separation,
            // but not after the very last element.
            if ($index < count($entities) - 1) {
                $finalText .= "\n\n";
                $utf16Offset += 2;
            }*/
        }

        $result = ["text" => $finalText];
        if (!empty($metadataParts)) {
            // Sort parts by their starting index to ensure correct order, just in case.
            usort($metadataParts, fn($a, $b) => $a['from_index'] <=> $b['from_index']);
            $result["metadata"] = ["meta_data_parts" => $metadataParts];
        }

        return $result;
    }

    /**
     * Recursively "walks" a RichEntity, appending to the final text
     * and generating metadata parts along the way.
     *
     * @param RichEntity|array|string $entity The current entity (or array of entities) to process.
     * @param string &$text The final plain text string, passed by reference.
     * @param array &$parts The array of metadata parts, passed by reference.
     * @param int &$offset The current UTF-16 offset, passed by reference.
     * @return void
    */
    private function walkAndRenderMetadata(mixed $entity, string &$text, array &$parts, int &$offset): void
    {
        // Handle arrays of entities (like the content of a paragraph).
        if (is_array($entity)) {
            foreach ($entity as $child) $this->walkAndRenderMetadata($child, $text, $parts, $offset);
            return;
        }

        // Handle raw strings that might have been optimized.
        if (is_string($entity)) {
            $text .= $entity;
            $offset += $this->utf16Len($entity);
            return;
        }
        
        // This check is essential for safety.
        if (!is_object($entity)) {
            return;
        }

        // The main dispatcher: determine action based on the entity's type, now upgraded with new logic.
        // This is where we map our SSoT to the Rubika metadata types.
        match (get_class($entity)) {
            // --- BLOCK LEVEL ENTITIES ---
            // For most blocks, we just need to render their inner content.
            RichBlockParagraph::class, RichBlockExpandableBlockQuotation::class =>
                $this->walkAndRenderMetadata($entity->text, $text, $parts, $offset),

            // --- INLINE (FORMATTING) ENTITIES ---
            RichTextPlain::class => (function() use ($entity, &$text, &$offset) {
                $text .= $entity->text;
                $offset += $this->utf16Len($entity->text);
            })(),

            // --- Standard formatting entities using the helper ---
            RichTextBold::class => $this->applyMetadata('Bold', $entity->text, $text, $parts, $offset),
            RichTextItalic::class => $this->applyMetadata('Italic', $entity->text, $text, $parts, $offset),
            RichTextUnderline::class => $this->applyMetadata('Underline', $entity->text, $text, $parts, $offset),
            RichTextStrikethrough::class => $this->applyMetadata('Strike', $entity->text, $text, $parts, $offset),
            RichTextSpoiler::class => $this->applyMetadata('Spoiler', $entity->text, $text, $parts, $offset),
            RichTextCode::class => $this->applyMetadata('Mono', $entity->text, $text, $parts, $offset),

            // --- UPGRADED: Handling for Preformatted code blocks ---
            RichTextPre::class => (function() use ($entity, $text, &$parts, $offset) {
                $part = $this->applyMetadata('Pre', $entity->text, $text, $parts, $offset, true);
                if ($part && !empty($entity->language)) {
                    // Augment the metadata part with the language information.
                    $part['language'] = $entity->language;
                    $parts[] = $part; // Add the augmented part to the list.
                }
            })(),

            // --- UPGRADED: Intelligent handling for URLs (Link vs MentionText) ---
            RichTextUrl::class => (function() use ($entity, &$text, &$parts, &$offset) {
                $url = $entity->url;
                $urlPrefix = substr($url, 0, 1);

                $startIndex = $offset;
                $this->walkAndRenderMetadata($entity->text, $text, $parts, $offset);
                $length = $offset - $startIndex;

                if ($length > 0) {
                    // Check if it's a special Rubika mention link.
                    if (isset(self::MENTION_PREFIX_TYPES[$urlPrefix])) {
                        $parts[] = [
                            'type' => 'MentionText',
                            'from_index' => $startIndex,
                            'length' => $length,
                            // The API expects the full ID string (e.g., "u0B...")
                            'mention_text_object_guid' => $url,
                            'mention_text_object_type' => self::MENTION_PREFIX_TYPES[$urlPrefix],
                        ];
                    } else { // Otherwise, it's a standard hyperlink.
                        $parts[] = [
                            'type' => 'Link',
                            'from_index' => $startIndex,
                            'length' => $length,
                            'link_data' => ['url' => $url],
                        ];
                    }
                }
            })(),

            // --- DEFAULT CATCH-ALL ---
            // For any unsupported entity type, we try to render its children if possible,
            // so we don't lose the text content.
            default => (function() use ($entity, &$text, &$parts, &$offset) {
                if (property_exists($entity, 'text')) $this->walkAndRenderMetadata($entity->text, $text, $parts, $offset);
                elseif (property_exists($entity, 'blocks')) $this->walkAndRenderMetadata($entity->blocks, $text, $parts, $offset);
            })(),
        };
    }

    /**
     * A helper function to reduce code duplication for simple metadata types.
     * It records the start offset, renders the content, calculates the length,
     * and adds the corresponding metadata part.
     * Now it can return the part for augmentation.
     *
     * @param string $type The metadata type (e.g., 'Bold', 'Italic').
     * @param mixed $content The inner content of the entity.
     * @param string &$text The final plain text string.
     * @param array &$parts The array of metadata parts.
     * @param int &$offset The current UTF-16 offset.
     * @return null|array The created metadata part if $returnPart is true.
    */
    private function applyMetadata(string $type, mixed $content, string &$text, array &$parts, int &$offset, bool $returnPart = false): ?array
    {
        $startIndex = $offset;
        $this->walkAndRenderMetadata($content, $text, $parts, $offset);
        $length = $offset - $startIndex;

        // Only add metadata if it actually applies to some text.
        if ($length > 0) {
            $part = [
                'type' => $type,
                'from_index' => $startIndex,
                'length' => $length,
            ];
            if ($returnPart) {
                return $part;
            }
            $parts[] = $part;
        }
        return null;
    }

    /**
     * Calculates the UTF-16 "length" of a UTF-8 string.
     * This is a critical utility for Rubika API compatibility.
     *
     * @param string $str The input UTF-8 string.
     * @return int The length in UTF-16 code units.
    */
    private function utf16Len(string $str): int
    {
        // Each UTF-16 character is 2 bytes, so we divide the byte length by 2.
        return strlen(mb_convert_encoding($str, 'UTF-16BE', 'UTF-8')) / 2;
    }
}
