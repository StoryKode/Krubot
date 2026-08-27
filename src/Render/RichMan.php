<?php

namespace KrubiK\Render;

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

use DateTimeInterface;
use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\RichElements\Blocks\RichBlockEntity;
use KrubiK\Render\RichElements\Blocks\RichBlockListItem;
use KrubiK\Render\RichElements\Blocks\RichBlockCaption;
use KrubiK\Render\RichElements\Blocks\RichBlockTableCell;
use KrubiK\Render\RichElements\Texts\RichTextEntity;
use KrubiK\Render\RichElements\Texts\RichText;
use KrubiK\Render\Kernel\SoulHarvestor;

use KrubiK\Facades\Parsentinel; // Input Parser [MD/HTML/Blade]

// ================== CORE DEPENDENCY: RENDER HELPERS ==================
// REFACTOR: Import all helper functions directly. This is the cornerstone of the new architecture.
// RichMan now delegates all instantiation tasks to this verified, single-source-of-truth layer.

// --- FACTORY HELPERS (DSL) ---
// Import all necessary helper functions to create RichEntity instances.
// This is the core of the v4 Hyper-DX-Refactoring.
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

/**
 * The Universal RichMan: A Hyper-DX, fluent, and unified builder for all rich message types.
 *
 * [ARCHITECTURAL EVOLUTION v4.8]
 * By extending RichEntity, RichMan transcends its role as a mere builder. It becomes a
 * fully-fledged, renderable, and composable component itself. This architectural leap
 * eliminates the boundary between the "composer" and the "composed," enabling truly
 * seamless and nested content creation.
 * A RichMan instance is now a document in progress,
 * capable of being rendered or passed as content to other entities at any stage.
 *
 * The Universal RichMan: A Hyper-DX, fluent, and unified builder for all rich message types.
 * This ultimate class seamlessly merges the capabilities of creating both simple,
 * inline-formatted text messages and complex, block-based articles.
 *
 * It intelligently determines the output format based on the methods used,
 * providing a single, powerful, and elegant interface for all message construction needs.
 * This class is designed to be the definitive tool for crafting expressive and structured
 * content for any modern messaging platform API.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
 */
class RichMan extends RichEntity // <<<< CORE CHANGE: Inheriting from the base entity.
{
    // ================== STATE PROPERTIES ==================

    /**
     * @var RichEntity[] A unified list holding all generated entities (both inline and block).
     * This now represents the "children" of this RichMan entity.
    */
    private array $elements = [];

    /** @var ?bool Optional flag for Right-To-Left text directionality. */
    public ?bool $isRtl = null;

    /**
     * @var string The accumulated plain text for inline-formatted messages.
     * @deprecated This property becomes less relevant as the primary output is now the entity tree itself.
     *             It can be kept for specific legacy cases or simple text message generation.
    */
    private string $inline_text = '';


    // ================== CONSTRUCTOR & ENTRY POINT ==================
    private function __construct(?string $initialText = null, ?bool $isRtl = null)
    {
        $this->isRtl = $isRtl;
        if ($initialText !== null && $initialText !== '') {
            $this->plain($initialText);
        }
    }

    public static function summon(?string $initialText = null, ?bool $isRtl = null): self
    {
        return new self($initialText, $isRtl);
    }

    
    public function rtl(bool $active = true): self
    {
        $this->isRtl = $active;
        return $this;
    }
    public function ltr(bool $active = true): self
    {
        $this->isRtl = !$active;
        return $this;
    }
    
    // ================== INTERNAL HELPERS ==================

    /**
     * The core method to add any RichEntity to the builder.
     * It also handles the logic for building the parallel 'inline_text' string.
     */
    private function addEntity(RichEntity $entity, ?string $appendText = null): self
    {
        $this->elements[] = $entity;
        if ($appendText !== null) {
            $this->inline_text .= $appendText;
        }
        return $this;
    }

    /**
     * A simple alias for adding block-level entities for better code readability.
     */
    private function addBlock(RichBlockEntity $block): self
    {
        return $this->addEntity($block);
    }

    /**
     * [THE COMPOSER'S HEART - THE REVEALED TRUTH]
     * The new, unified "add" method. This is the ultimate tool for composing complex messages,
     * as revealed by the Architect. It is not a mere function; it is the law of composition.
     * It intelligently handles and adds various content types to the element stream.
     *
     * - `string`: A blasphemy to be avoided. Delegated to `plain()` only as a fallback for primitives.
     * - `RichEntity`: The blessed building blocks of the universe.
     * - `array`: Recursively composes multiple entities.
     * - `Closure`: A sacred pact for grouped or conditional logic.
     * - `self`: The ultimate act of composition, absorbing the essence of another composer.
     *
     * @param string|RichEntity|array|callable|\Closure $content The content to add.
     * @return $this
     */
    public function add(string|RichEntity|array|callable|\Closure|self $content): self
    {
        if ($content instanceof \Closure) {
            // Execute the closure, allowing for grouped operations.
            $content($this);

        }

        elseif (is_callable($content)) {

            // Execute the callable, allowing for grouped operations +++ Laravel IoC.

            $paramsToInject = [];

            // --- The API's Enforcement Gate ---
            try {
                $reflection = new \ReflectionFunction($content);
                $parameters = $reflection->getParameters();

                // The Iron Law: If a closure accepts parameters, the first one
                // is designated for our composer. No exceptions.
                if (!empty($parameters)) {
                    $paramsToInject[$parameters[0]->getName()] = $this;
                }

            } catch (\ReflectionException $e) {
                // Fallback for non-reflectable callables. Extremely rare, but safe to have.
            }

            app()->call($content, $paramsToInject);

        } elseif ($content instanceof self) {
            // [** HDX-DREAM MADE REAL **]
            // If the content is another RichMan instance, absorb its very soul.
            // We extract its elements and delegate them back to this method, which already
            // possesses the wisdom to handle an array of entities. Pure, recursive elegance.
            $this->add($content->getElements());

        } elseif (is_array($content)) {
            // If it's an array, recursively add each item.
            foreach ($content as $item) {
                $this->add($item);
            }

        } elseif (is_string($content)) {
            // A raw string is delegated to the `plain` method, which correctly handles it.
            // This path is for convenience, not for architectural purity.
            $this->plain($content);

        } elseif ($content instanceof RichBlockEntity) {
            // A pre-built Block entity is added via the specialized internal helper.
            $this->addBlock($content);

        } elseif ($content instanceof RichEntity) {
            // Any other pre-built RichEntity is added. Its essence is its structure.
            $this->addEntity($content);
        }

        // Always return self for the sacred fluent chain.
        return $this;
    }

    /**
     * A semantic alias for adding multiple pre-built entities at once.
     * This method is a high-level convenience wrapper around `add()`.
     *
     * @param RichEntity ...$entities A sequence of RichEntity objects.
     * @return $this
    */
    public function entities(RichEntity ...$entities): self
    {
        // Simply delegate the array of entities to the master `add` method.
        return $this->add($entities);
    }

    /**
     * Normalizes content that can be either a string or a pre-built RichMan instance.
     * This is key for creating nested formatted content (e.g., a bold link).
     */
    private static function normalizeText(self|string $content): RichTextEntity
    {
        // REFACTOR: If content is another RichMan, we extract its final RichTextEntity object.
        // Otherwise, we wrap the plain string in a 'plain' helper.
        if ($content instanceof self) {
            return $content->getResultAsRichText();
        }
        return plain($content);
    }

    // `normalizeText` is now DEPRECATED and has been replaced by `normalizeBlockContent`.
    // The name is more specific, and the logic is simpler.

    /**
     * [REFACTORED & SIMPLIFIED] Normalizes content for block elements.
     * Now that RichMan is a RichEntity, this helper is greatly simplified.
     * Its only remaining job is to convert a raw string into a `plain` entity.
     * The complex case of handling a RichMan instance is now natively supported.
     *
     * @param RichEntity|string $content The content to normalize.
     * @return RichEntity The normalized, ready-to-use entity.
    */
    private static function normalizeBlockContent(RichEntity|string $content): RichEntity
    {
        // If it's already an entity (which now includes RichMan instances), return it directly.
        // Otherwise, wrap the raw string in a `plain` entity. Elegant and simple.
        return $content instanceof RichEntity ? $content : plain($content);
    }

    /**
     * Builds and returns the final RichTextEntity object from the inline elements.
     * This is used by `normalizeText` to compose builders together.
     */
    public function getResultAsRichText(): RichTextEntity
    {
        // Extracts only the inline elements for composition.
        $inlineEntities = array_filter($this->elements, fn($e) => !$e instanceof RichBlockEntity);
        return RichText::make($inlineEntities);
    }

    /**
     * Retrieves the raw array of built RichEntity objects.
     * This is the essential bridge for Facades like 'Article' to consume
     * the result of the build process and wrap it in a RichDoc.
     *
     * @return RichEntity[]
    */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * [THE GENESIS PORTAL - THE ALCHEMIST'S SUMMONING]
     * This is not merely a function. It is the static incantation to transmute any raw, chaotic string
     * matter into a structured, living RichMan consciousness, ready to be unleashed upon the DOM or API.
     * It's the "new from string" protocol.
     *
     * It does not think. It delegates the sacred task of parsing to the correct
     * specialist via the ParserFactory, then returns a NEW, pre-filled RichMan
     * instance via `takeOver()`, ready for further composition or finalization.
     *
     * @param self|string $input The raw, chaotic string matter.
     * @param string|null $parserType The identity of the matter ('MarkdownV2', 'HTML', etc.) The runic key required to decipher the soul of tributes. If null, our wisest oracles will divine the format.
     * @return self A new RichMan instance, born from the parsed entities.
    */
    public static function parse(self|string $input, ?string $parserType = null): self
    {

        // Summon a new, empty composer to host the reborn entities.
        $composer = self::summon();

        // The new soul is commanded to absorb the input's data.
        $composer->takeOver($input, $parserType);

        // Return the newly materialized composer.
        return $composer;
    }

    /**
     * [THE ASSIMILATION RITE - THE HUNGER OF THE VOID]
     * //--//
     * BEWARE. This is not the clean, divine act of creation found in `::parse()`. This is Conquest.
     * 
     * This is a non-static, instance-level ritual of MUTATION. It is the existential hunger of an
     * already-existing entity. It looks upon foreign data—be it the chaotic energy of a raw string
     * or the structured consciousness of another RichMan—and whispers: "You are now part of me."
     *
     * It operates in two modes of brutal efficiency:
     * 1. Consciousness Plunder (`$input instanceof self`): It performs a psychic vivisection on a
     *    rival instance, tearing out its core elements (`entities`) and its very perspective (`isRtl`),
     *    grafting them onto its own soul. The target is not harmed, but its essence is now ours.
     * 2. Matter Transmutation (`string`): It feeds the raw, untamed string to the ParserFactory, a
     *    cabal of master artisans who shatter the input into pure, elemental gems (`RichEntity[]`),
     *    which are then immediately absorbed into this instance's being.
     *
     * This method returns a reference to its own, now-mutated self, daring you to continue the chain.
     * It is the Borg of fluent interfaces. Resistance is futile.
     * 
     * NEW:::
     * The takeover process is now empowered by the mighty Parsentinel.
     * It summons the correct SyntaxWarden to decipher any string input.
     *
     * @param self|string $input   The tribute to be consumed. The reality to be assimilated. The fuel.
     * @param string|null $parserType The identity of the matter ('MarkdownV2', 'HTML', etc.) The runic key required to decipher the soul of tributes.
     * @return self                 ITSELF. The same instance in memory, now more powerful, more complex. A monster gorged on the essence of another.
    */
    public function takeOver(self|string $input, ?string $parserType = null): self
    {
        $isRtl = null;
        $entities = [];

        // Check if the target is another consciousness or just raw matter.
        if($input instanceof self) {

            // It's a rival. Drain its properties. We shall mirror its soul. Siphon its very essence.
            $isRtl = $input->isRtl;
            $entities = $input->getElements();

        } else {

            // if It's just a string. Delegate to the Parsentinel [ParserFactory] guild
            // to find the correct master artisan and returns a specialist that understands how to decrypt the input's soul.

            // Summon the appropriate warden through the Parsentinel facade.
            $specialist = Parsentinel::summon($parserType ?? 'auto');

            // Ask the warden to decipher the scripture into an array of entities.
            // The specialist performs the deconstruction, returning pure RichEntity gems.
            $entities = $specialist->decipher($input);

        }

         // If the entity was empty, it was unworthy. Do nothing. Move on.
        if(empty($entities))
            return $this;

        // If we assimilated a rival, we must also adopt his worldview (RTL).
        if($isRtl !== null)
            $this->rtl($isRtl);

        // Absorb the gems into the new composer's heart and Return $this for chaining.
        // Use the spread operator to add all deciphered entities to this composer.
        //
        // Note! The final act of absorption. The `...` is is not a syntax-sugar; it is a dimensional tear,
        // ensuring each entity is injected into our bloodstream, individually.
        return $this->entities(...$entities);
    }

    /**
     * [THE FINAL SEAL - THE CREATION OF THE ARTIFACT]
     * This method concludes the great work. It takes all the composed entities
     * held within the RichMan's heart and seals them into the final, immutable,
     * and renderable SoulHarvestor.
     *
     * This transforms the builder from a transient state of composition into
     * a permanent, deliverable product.
     * 
     * عبور از دروازه مرگ و تولد به عنوان روحی جاودانه:
     * در پشت دروازه، SoulHarvestor ایستاده است. او هیچ متد ویرایشگری (مانند bold() یا add()) ندارد. او عاری از نفوذ و تغییر است؛ یک «مصنوع نهایی» (The Immutable Artifact).
     * RichMan با دادن عصاره جانش، از یک موجودیت فانی و در حال تغییر (Mutable Builder),
     * به حقیقتی ابدی و متجلی‌شده در قالب SoulHarvestor تبدیل می‌شود.
     *
     * @return SoulHarvestor The final masterpiece.
    */
    public function build(): SoulHarvestor
    {
        return SoulHarvestor::feed($this);
        /// $this->elements // The elements, now ordered and pure, are passed to the final container.
    }

    // =========================================================
    // == Begin ::: RichEntity Contract Implementation :::
    // =========================================================

    /**
     * [NEW] Implements the toArray contract from the Arrayable interface via RichEntity.
     * This method transforms the RichMan instance and all its contained elements into a
     * serializable array structure, making it ready for API responses (e.g., JSON).
     * It recursively calls toArray() on each child element.
     *
     * @return array The array representation of all contained elements.
    */
    public function toArray(): array
    {
        // Simply delegate to the normalize helper, which is perfect for this task.
        // We are normalizing our own children.
        return $this->normalize($this->elements);
    }

    /**
     * [NEW] Implements the toHtml contract from the Htmlable interface via RichEntity.
     * This method renders the RichMan instance and all its contained elements into a
     * single, concatenated, and safe HTML string. It serves as the primary rendering
     * output for web-based platforms.
     *
     * @return string The fully rendered HTML string.
    */
    public function toHtml(): string
    {
        // Delegate the complex recursive rendering task to the protected renderHtml helper
        // inherited from RichEntity. This is its exact purpose.
        return $this->renderHtml($this->elements);
    }

    /**
     * [NEW] Provides a text-based representation of the content.
     * This method is crucial for platforms like Telegram (Markdown) or for generating
     * plain text summaries. It intelligently respects the Markdown rendering flags.
     *
     * @return string The fully rendered text/Markdown string.
    */
    public function toText(): string
    {
        // Delegate to the renderText helper from RichEntity, which handles Markdown
        // logic and recursive rendering for text-based outputs.
        return $this->renderText($this->elements);
    }

    // =========================================================
    // == End ::: RichEntity Contract Implementation :::
    // =========================================================


    // =========================================================
    /// == Start:: Advanced Brain Manipulation ==
    // =========================================================

    /**
     * Append another parsed instance or raw entity list.
    */
    public function append(self|array $segment): self
    {
        $entities = $segment instanceof self ? $segment->getElements() : $segment;

        if ($entities === []) {
            return $this;
        }

        array_push($this->elements, ...array_values($entities));

        return $this;
    }

    /**
     * Prepend another parsed instance or raw entity list.
     */
    public function prepend(self|array $segment): self
    {
        $entities = $segment instanceof self ? $segment->getElements() : $segment;

        if ($entities === []) {
            return $this;
        }

        array_unshift($this->elements, ...array_values($entities));

        return $this;
    }

    /**
     * Insert another parsed instance or raw entity list at any position.
     *
     * Index rules:
     * - <= 0  => insert at start
     * - >= n  => append
     * - else  => insert in the middle
     */
    public function insert(int $index, self|array $segment): self
    {
        $entities = $segment instanceof self ? $segment->getElements() : $segment;
        $entities = array_values($entities);

        if ($entities === []) {
            return $this;
        }

        $count = count($this->elements);

        if ($index <= 0) {
            array_unshift($this->elements, ...$entities);
            return $this;
        }

        if ($index >= $count) {
            array_push($this->elements, ...$entities);
            return $this;
        }

        array_splice($this->elements, $index, 0, $entities);

        return $this;
    }

    /**
     * Replace a single entity at index with another parsed instance or entity list.
     */
    public function replace(int $index, self|array $segment): self
    {
        $entities = $segment instanceof self ? $segment->getElements() : $segment;
        $entities = array_values($entities);

        if ($index < 0 || $index >= count($this->elements)) {
            throw new InvalidArgumentException("Replace index [{$index}] is out of bounds.");
        }

        array_splice($this->elements, $index, 1, $entities);

        return $this;
    }

    /**
     * Remove one or more entities from the chain.
     */
    public function remove(int $index, int $length = 1): self
    {
        if ($length <= 0) {
            return $this;
        }

        array_splice($this->elements, $index, $length);

        return $this;
    }

    /**
     * Return a clone-like new instance with merged entities.
     */
    public function merged(self|array $segment): self
    {
        $clone = new self($this->elements, $this->payload);
        return $clone->append($segment);
    }    

    /**
     * Parse text and append it to the current chain.
    */
    public function parsenAppend(self|string $input, string $mode = 'auto'): self
    {
        return $this->append(self::parse($input, $mode));
    }

    /**
     * Parse text and prepend it to the current chain.
    */
    public function parsenPrepend(self|string $input, string $mode = 'auto'): self
    {
        return $this->prepend(self::parse($input, $mode));
    }

    /**
     * Parse text and insert it anywhere in the chain.
    */
    public function parsenInsert(int $index, self|string $input, string $mode = 'auto'): self
    {
        return $this->insert($index, self::parse($input, $mode));
    }

    // =========================================================
    /// == End:: Advanced Brain Manipulation ==
    // =========================================================

    // =================================================================================
    // ================= HYPER-DX FLUENT METHODS (INLINE ELEMENTS) =====================
    // =================================================================================

    /**
     * A helper method to add a newline character for inline text.
     * Essential for fulfilling the poetic dream.
     *
     * @param int $count Number of newlines to add.
     * @return $this
    */
    public function newLine(int $count = 1): self
    {
        // This is a simple implementation for inline text.
        // A more complex scenario might involve creating a new paragraph block.
        return $this->plain(str_repeat("\n", $count));
    }

    public function line(string|RichEntity|array|callable|\Closure|self|null $content = null): self
    {
        $this->newLine();

        if($content)
            $this->add($content);

        return $this
    }

    public function space(int $count = 1): self
    {
        return $this->plain(str_repeat(' ', $count));
    }


    /**
     * Creates a user mention (e.g., @johndoe).
     *
     * REFACTOR: Clarified and separated mention types for maximum DX and clarity.
     * This aligns perfectly with the distinct `mention` and `textMention` helpers.
     *
     * @param RichEntity|callable|string|array $text The visible text of the mention.
     * @param string $username The username being mentioned.
     * @return $this*
    */
    public function mentionByUsername(RichEntity|callable|string|array $text, string $username): self
    {
        return $this->addEntity(mention($text, $username)); // , $text
    }

    /**
     * Creates an inline mention of a user.
     *
     * @param RichEntity|callable|string|array $text The visible text of the mention.
     * @param UserDTO|array $user The UserDTO or array representation of the user.
     * @return $this
     */
    public function mention(RichEntity|callable|string|array $text, UserDTO|array $user): self
    {
        return $this->addEntity(textMention($text, $user));
    }

    /** REFACTOR: Added high-level DX methods for lists, hiding the 'style' string. */
    public function bulletList(RichBlockListItem ...$items): self { return $this->addBlock(listBlock($items, 'bullet')); }
    public function orderedList(RichBlockListItem ...$items): self { return $this->addBlock(listBlock($items, 'ordered')); }

    public function preBlock(string $code, ?string $language = null): self { return $this->addBlock(pre(plain($code), $language)); }

    // These methods are to be placed inside the `RichMan` class.
    // They assume that the helper functions from `KrubiK\Render\Helpers`
    // are available in the global scope or have been imported via `use function`.

    // =====================================================================================
    // =================== INLINE & TEXT-LEVEL ELEMENT METHODS (FULL SET) ==================
    // =====================================================================================
    // Each method perfectly mirrors the signature of its corresponding helper function,
    // allowing for complex, nested, and callable content.

    /**
     * Creates a generic RichText container instance.
     *
     * @param RichEntity|callable|string|array $text The content to be wrapped.
     * @return $this
     */
    public function text(RichEntity|callable|string|array $text): self
    {
        return $this->addEntity(text($text));
    }

    /**
     * Creates a RichTextAnchorLink instance to an internal anchor.
     *
     * @param RichEntity|callable|string|array $text The visible, clickable text.
     * @param string $anchorName The name of the anchor to link to.
     * @return $this
     */
    public function anchorLink(RichEntity|callable|string|array $text, string $anchorName): self
    {
        return $this->addEntity(anchorLink($text, $anchorName));
    }

    /**
     * Creates a RichTextBankCardNumber instance.
     *
     * @param RichEntity|callable|string|array $text The visible text.
     * @param string $bankCardNumber The full bank card number string.
     * @return $this
     */
    public function bankCardNumber(RichEntity|callable|string|array $text, string $bankCardNumber): self
    {
        return $this->addEntity(bankCardNumber($text, $bankCardNumber));
    }

    /**
     * Wraps content to be displayed in bold.
     *
     * @param RichEntity|callable|string|array $text The content to render as bold.
     * @return $this
     */
    public function bold(RichEntity|callable|string|array $text): self
    {
        return $this->addEntity(bold($text));
    }

    /**
     * Creates a clickable bot command.
     *
     * @param RichEntity|callable|string|array $text The visible text of the command.
     * @param string $botCommand The actual command string (e.g., "/help").
     * @return $this
     */
    public function botCommand(RichEntity|callable|string|array $text, string $botCommand): self
    {
        return $this->addEntity(botCommand($text, $botCommand));
    }

    /**
     * Creates a cashtag (e.g., $KRUB).
     *
     * @param RichEntity|callable|string|array $text The visible text of the cashtag.
     * @param string $cashtag The cashtag identifier (e.g., "KRUB").
     * @return $this
     */
    public function cashtag(RichEntity|callable|string|array $text, string $cashtag): self
    {
        return $this->addEntity(cashtag($text, $cashtag));
    }

    /**
     * Wraps content as inline, monospaced code.
     *
     * @param RichEntity|callable|string|array $text The code snippet.
     * @return $this
     */
    public function code(RichEntity|callable|string|array $text): self
    {
        return $this->addEntity(code($text));
    }

    /**
     * Creates a custom emoji.
     *
     * @param string $customEmojiId The unique identifier for the custom emoji.
     * @param string $alternativeText The fallback text description.
     * @return $this
     */
    public function customEmoji(string $customEmojiId, string $alternativeText): self
    {
        return $this->addEntity(customEmoji($customEmojiId, $alternativeText));
    }

    /**
     * Creates a formatted date and time entity.
     *
     * @param RichEntity|callable|string|array $text The visible text.
     * @param int $unixTime The timestamp in Unix epoch format.
     * @param string $dateTimeFormat A string describing the format.
     * @return $this
     */
    public function dateTime(RichEntity|callable|string|array $text, int $unixTime, string $dateTimeFormat): self
    {
        return $this->addEntity(dateTime($text, $unixTime, $dateTimeFormat));
    }

    /**
     * Creates a clickable email link (mailto:).
     *
     * @param RichEntity|callable|string|array $text The visible text of the email link.
     * @param string $email_address The actual email address.
     * @return $this
     */
    public function emailAddress(RichEntity|callable|string|array $text, string $email_address): self
    {
        return $this->addEntity(emailAddress($text, $email_address));
    }
    public function email(RichEntity|callable|string|array $text, string $email_address): self
    {
        return $this->addEntity(emailAddress($text, $email_address));
    }

    /**
     * Creates a hashtag (e.g., #php).
     *
     * @param RichEntity|callable|string|array $text The visible text of the hashtag.
     * @param string $hashtag The hashtag string without the '#' prefix.
     * @return $this
     */
    public function hashtag(RichEntity|callable|string|array $text, string $hashtag): self
    {
        return $this->addEntity(hashtag($text, $hashtag));
    }

    /**
     * Wraps content to be highlighted or marked.
     *
     * @param RichEntity|callable|string|array $text The content to be marked.
     * @return $this
     */
    public function marked(RichEntity|callable|string|array $text): self
    {
        return $this->addEntity(marked($text));
    }

    /**
     * Wraps content to be displayed in italics.
     *
     * @param RichEntity|callable|string|array $text The content to render as italic.
     * @return $this
     */
    public function italic(RichEntity|callable|string|array $text): self
    {
        return $this->addEntity(italic($text));
    }

    /**
     * Creates a clickable phone number.
     *
     * @param RichEntity|callable|string|array $text The visible text.
     * @param string $phoneNumber The phone number in a callable format.
     * @return $this
     */
    public function phoneNumber(RichEntity|callable|string|array $text, string $phoneNumber): self
    {
        return $this->addEntity(phoneNumber($text, $phoneNumber));
    }

    /**
     * Creates a simple, unformatted string of text.
     *
     * @param string $text The plain text content.
     * @return $this
     */
    public function plain(string $text): self
    {
        return $this->addEntity(plain($text));
    }

    /**
     * Creates a mathematical formula or expression entity.
     *
     * @param string $expression The mathematical expression as a string.
     * @return $this
     */
    public function mathematicalExpression(string $expression): self
    {
        return $this->addEntity(mathematicalExpression($expression));
    }
    public function math(string $expression): self
    {
        return $this->addEntity(mathematicalExpression($expression));
    }

    /**
     * Creates a pre-formatted block of text or code.
     *
     * @param RichEntity|callable|string|array $text The content of the pre-formatted block.
     * @param string|null $language The programming language for syntax highlighting.
     * @return $this
     */
    public function pre(RichEntity|callable|string|array $text, ?string $language = null): self
    {
        // Although `pre` often behaves like a block, in many rich text formats
        // it's an inline-level entity that contains block-like text. We add it
        // as a general entity. RichEntity's render logic will handle it correctly.
        return $this->addEntity(pre($text, $language));
    }

    /**
     * Creates a reference to another part of the content.
     *
     * @param RichEntity|callable|string|array $text The visible text of the reference.
     * @param string $name The unique name of the item being referenced.
     * @return $this
     */
    public function reference(RichEntity|callable|string|array $text, string $name): self
    {
        return $this->addEntity(reference($text, $name));
    }

    /**
     * Creates a clickable link to a reference.
     *
     * @param RichEntity|callable|string|array $text The visible, clickable text.
     * @param string $referenceName The name of the reference to link to.
     * @return $this
     */
    public function referenceLink(RichEntity|callable|string|array $text, string $referenceName): self
    {
        return $this->addEntity(referenceLink($text, $referenceName));
    }

    /**
     * Wraps content in a spoiler.
     *
     * @param RichEntity|callable|string|array $text The content to be concealed.
     * @return $this
     */
    public function spoiler(RichEntity|callable|string|array $text): self
    {
        return $this->addEntity(spoiler($text));
    }

    /**
     * Wraps content with a strikethrough.
     *
     * @param RichEntity|callable|string|array $text The content to strike through.
     * @return $this
     */
    public function strikethrough(RichEntity|callable|string|array $text): self
    {
        return $this->addEntity(strikethrough($text));
    }

    /**
     * Renders text as a subscript.
     *
     * @param RichEntity|callable|string|array $text The content to be subscripted.
     * @return $this
     */
    public function subscript(RichEntity|callable|string|array $text): self
    {
        return $this->addEntity(subscript($text));
    }

    /**
     * Renders text as a superscript.
     *
     * @param RichEntity|callable|string|array $text The content to be superscripted.
     * @return $this
     */
    public function superscript(RichEntity|callable|string|array $text): self
    {
        return $this->addEntity(superscript($text));
    }

    /**
     * Wraps content with an underline.
     *
     * @param RichEntity|callable|string|array $text The content to underline.
     * @return $this
     */
    public function underline(RichEntity|callable|string|array $text): self
    {
        return $this->addEntity(underline($text));
    }

    /**
     * Creates a standard hyperlink.
     *
     * @param RichEntity|callable|string|array $text The visible, clickable text.
     * @param string $url The destination URL.
     * @return $this
     */
    public function href(RichEntity|callable|string|array $text, string $url): self
    {
        return $this->addEntity(href($text, $url));
    }
    public function url(RichEntity|callable|string|array $text, string $url): self
    {
        return $this->addEntity(href($text, $url));
    }
    public function link(RichEntity|callable|string|array $text, string $url): self
    {
        return $this->addEntity(href($text, $url));
    }

    // =====================================================================================
    // ================= BLOCK-LEVEL ELEMENT METHODS (FULL SET) ============================
    // =====================================================================================
    // These methods construct and add block-level elements, forming the main structure
    // of a document or message. They use `addBlock` internally.

    /**
     * Creates a named anchor point within the document.
     *
     * @param string $name The unique name for the anchor.
     * @return $this
     */
    public function anchor(string $name): self
    {
        return $this->addBlock(anchor($name));
    }

    /**
     * Creates a caption for a block-level element.
     *
     * @param RichEntity|callable|string|array $text The main caption text.
     * @param RichEntity|callable|string|array|null $credit Optional credit text.
     * @return $this
     */
    public function caption(RichEntity|callable|string|array $text, RichEntity|callable|string|array|null $credit = null): self
    {
        return $this->addBlock(caption($text, $credit));
    }

    /**
     * Creates a list item for use in lists.
     *
     * @param string $label Label of the item.
     * @param RichBlockEntity[]|Arrayable $blocks The content of the list item.
     * @param bool|null $hasCheckbox
     * @param bool|null $isChecked
     * @param int|null $value
     * @param string|null $type
     * @return $this
     */
    public function listItem(string $label, array|Arrayable $blocks, ?bool $hasCheckbox = null, ?bool $isChecked = null, ?int $value = null, ?string $type = null): self
    {
        return $this->addBlock(listItem($label, $blocks, $hasCheckbox, $isChecked, $value, $type));
    }

    /**
     * Creates a table cell.
     *
     * @param RichEntity|callable|string|array|null $text The cell content.
     * @param bool|null $isHeader
     * @param int|null $colspan
     * @param int|null $rowspan
     * @param string $align
     * @param string $valign
     * @return $this
     */
    public function tableCell(RichEntity|callable|string|array|null $text = null, ?bool $isHeader = null, ?int $colspan = null, ?int $rowspan = null, string $align = 'left', string $valign = 'top'): self
    {
        return $this->addBlock(tableCell($text, $isHeader, $colspan, $rowspan, $align, $valign));
    }

    /**
     * Alias for tableCell.
     */
    public function cell(RichEntity|callable|string|array|null $text = null, ?bool $isHeader = null, ?int $colspan = null, ?int $rowspan = null, string $align = 'left', string $valign = 'top'): self
    {
        return $this->addBlock(cell($text, $isHeader, $colspan, $rowspan, $align, $valign));
    }

    /**
     * Creates an embedded animation (e.g., GIF).
     *
     * @param AnimationDTO|array $animation The Animation model.
     * @param bool|null $hasSpoiler If true, adds a spoiler overlay.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return $this
     */
    public function animation(AnimationDTO|array $animation, ?bool $hasSpoiler = null, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        return $this->addBlock(animation($animation, $hasSpoiler, $caption));
    }

    /**
     * Creates an embedded audio file.
     *
     * @param AudioDTO|array $audio The Audio model.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return $this
     */
    public function audio(AudioDTO|array $audio, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        return $this->addBlock(audio($audio, $caption));
    }

    /**
     * Creates a long, indented quotation.
     *
     * @param RichBlockEntity[]|Arrayable $blocks Content of the quote.
     * @param RichEntity|callable|string|array|null $credit Optional attribution.
     * @return $this
     */
    public function blockQuotation(array|Arrayable $blocks, RichEntity|callable|string|array|null $credit = null): self
    {
        return $this->addBlock(blockQuotation($blocks, $credit));
    }

    /**
     * Creates a collage of other blocks.
     *
     * @param RichBlockEntity[]|Arrayable $blocks An array of block entities.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return $this
     */
    public function collage(array|Arrayable $blocks, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        return $this->addBlock(collage($blocks, $caption));
    }

    /**
     * Creates a collapsible "details" block.
     *
     * @param RichEntity|callable|string|array $summary The visible summary text.
     * @param RichBlockEntity[]|Arrayable $blocks The hidden content.
     * @param bool|null $isOpen If true, initially open.
     * @return $this
     */
    public function details(RichEntity|callable|string|array $summary, array|Arrayable $blocks, ?bool $isOpen = null): self
    {
        return $this->addBlock(details($summary, $blocks, $isOpen));
    }

    /**
     * Creates a horizontal rule or thematic break.
     *
     * @return $this
     */
    public function divider(): self
    {
        return $this->addBlock(divider());
    }

    /**
     * Creates a text-based separator block.
     *
     * @param string $char The character to repeat.
     * @param int $length The number of repetitions.
     * @return $this
     */
    public function separator(string $char = '—', int $length = 20): self
    {
        return $this->addBlock(separator($char, $length));
    }

    /**
     * Creates a footnote definition block.
     *
     * @param string $name The unique identifier for the footnote.
     * @param RichBlockEntity[]|Arrayable $blocks The content blocks of the footnote.
     * @return $this
     */
    public function footnoteDefinition(string $name, array|Arrayable $blocks): self
    {
        return $this->addBlock(footnoteDefinition($name, $blocks));
    }

    /**
     * Creates a footer section.
     *
     * @param RichEntity|callable|string|array $text The content of the footer.
     * @return $this
     */
    public function footer(RichEntity|callable|string|array $text): self
    {
        return $this->addBlock(footer($text));
    }

    /**
     * Creates a heading.
     *
     * @param RichEntity|callable|string|array $text The text of the heading.
     * @param int $size The heading level (1-6).
     * @return $this
     */
    public function heading(RichEntity|callable|string|array $text, int $size): self
    {
        return $this->addBlock(heading($text, $size));
    }
    public function headline(RichEntity|callable|string|array $text, int $level = 1, int $margin = 1): self
    {
        return $this->heading($text, $size)->newLine($margin);
    }

    /**
     * Creates an ordered or unordered list.
     *
     * @param RichBlockListItem[]|Arrayable $items An array of list items.
     * @param string $style 'bullet' or 'ordered'.
     * @return $this
     */
    public function listBlock(array|Arrayable $items, string $style = 'bullet'): self
    {
        return $this->addBlock(listBlock($items, $style));
    }

    /**
     * Creates an embedded map.
     *
     * @param LocationDTO|array $location The Location object.
     * @param int $zoom The map zoom level.
     * @param int $width The map width in pixels.
     * @param int $height The map height in pixels.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return $this
     */
    public function map(LocationDTO|array $location, int $zoom, int $width, int $height, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        return $this->addBlock(map($location, $zoom, $width, $height, $caption));
    }

    /**
     * Creates a standard paragraph of text.
     *
     * @param RichEntity|callable|string|array $text The content of the paragraph.
     * @return $this
     */
    public function paragraph(RichEntity|callable|string|array $text): self
    {
        return $this->addBlock(paragraph($text));
    }

    /**
     * Creates an embedded photo.
     *
     * @param PhotoSizeDTO|array $photo The PhotoSize object.
     * @param bool|null $hasSpoiler If true, adds a spoiler overlay.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return $this
     */
    public function photo(PhotoSizeDTO|array $photo, ?bool $hasSpoiler = null, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        return $this->addBlock(photo($photo, $hasSpoiler, $caption));
    }

    /**
     * Creates a short, pull-out quotation.
     *
     * @param RichEntity|callable|string|array $text The text of the pull quote.
     * @param RichEntity|callable|string|array|null $credit Optional attribution.
     * @return $this
     */
    public function pullQuotation(RichEntity|callable|string|array $text, RichEntity|callable|string|array|null $credit = null): self
    {
        return $this->addBlock(pullQuotation($text, $credit));
    }

    /**
     * Creates a slideshow.
     *
     * @param RichBlockEntity[]|Arrayable $blocks The slides.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return $this
     */
    public function slideshow(array|Arrayable $blocks, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        return $this->addBlock(slideshow($blocks, $caption));
    }

    /**
     * Creates a table.
     *
     * @param RichBlockTableCell[][]|Arrayable $cells A 2D array of cells.
     * @param bool|null $isBordered
     * @param bool|null $isStriped
     * @param RichEntity|callable|string|array|null $caption An optional caption.
     * @return $this
     */
    public function table(array|Arrayable $cells, ?bool $isBordered = null, ?bool $isStriped = null, RichEntity|callable|string|array|null $caption = null): self
    {
        return $this->addBlock(table($cells, $isBordered, $isStriped, $caption));
    }

    /**
     * Creates a "thinking" indicator.
     *
     * @param RichEntity|callable|string|array $text Placeholder text.
     * @return $this
     */
    public function thinking(RichEntity|callable|string|array $text): self
    {
        return $this->addBlock(thinking($text));
    }

    /**
     * Creates an embedded video.
     *
     * @param VideoDTO|array $video The Video object.
     * @param bool|null $hasSpoiler If true, adds a spoiler overlay.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return $this
     */
    public function video(VideoDTO|array $video, ?bool $hasSpoiler = null, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        return $this->addBlock(video($video, $hasSpoiler, $caption));
    }

    /**
     * Creates an embedded voice note.
     *
     * @param VoiceDTO|array $voiceNote The Voice object.
     * @param RichBlockCaption|RichEntity|callable|string|null $caption An optional caption.
     * @return $this
     */
    public function voiceNote(VoiceDTO|array $voiceNote, RichBlockCaption|RichEntity|callable|string|null $caption = null): self
    {
        return $this->addBlock(voiceNote($voiceNote, $caption));
    }

}
