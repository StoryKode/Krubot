<?php

declare(strict_types=1);

namespace KrubiK\Render\RichElements;

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

use Stringable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use KrubiK\Enums\Platform;
use KrubiK\Render\RenderAura;

/**
 * Class RichEntity
 * 
 * Standard base class for serializable structures with built-in normalization.
 *
 * Plus A helper trait to recursively normalize RichText content.
 * Handles strings, arrays, and RichEntity objects.
 * This is a core utility for handling the recursive nature of RichText.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
abstract class RichEntity implements Arrayable, Stringable, Htmlable, Renderable
{

    /**
     * [The Gateway to the Current Realm]
     *
     * Dynamically resolves the request-scoped contextual realm (RenderAura) from the IoC container.
     * This method acts as a stable, sovereign portal to the established "laws" of the current
     * request (its platform and locale).
     *
     * By invoking the realm dynamically, we preserve the entity's immutability,
     * eliminate constructor boilerplate, and prevent memory leaks in long-running
     * application servers like Octane.
     * 
     * The resolving complexity is O(1) as Laravel retrieves it from its resolved instances array.
     *
     * @return RenderAura The sacred context object defining the rendering space.
    */
    protected function realm(): RenderAura
    {
        return app(RenderAura::class);
    }
    /**
     *
     * @return RenderAura The Arcane Vision - Dynamic RenderContext Accessor
    */
    protected function aura(): RenderAura
    {
        return app(RenderAura::class);
    }

    /**
     * Checks if the current rendering target is the Web platform.
     * This is a high-performance, direct-path method that bypasses __call.
     *
     * @return bool
     */
    protected final function targetsWeb(): bool
    {
        // Direct enum comparison is the fastest possible()) check in PHP 8.1+.
        return $this->aura()->platform->matches(Platform::Web());
    }

    /**
     * Checks if the current rendering target is the Telegram platform.
     * This is a high-performance, direct-path method that bypasses __call.
     *
     * @return bool
     */
    protected final function targetsTelegram(): bool
    {
        return $this->aura()->platform->matches(Platform::Telegram());
    }

    /**
     * Checks if the current rendering target is the Command-Line Interface (CLI).
     * This is a high-performance, direct-path method that bypasses __call.
     *
     * @return bool
     */
    protected final function targetsCli(): bool
    {
        return $this->aura()->platform->matches(Platform::CLI());
    }

    /**
     * ✨ [The Ultra-Hyper-DX Metaprogramming Engine] ✨
     *      [Multi-Prefix Dynamic Method Resolver]
     *
     * This magic method dynamically handles calls for platform-specific checks
     * either `livesOn[Platform]()` or `targets[Platform]()` , such as `livesOnTG()` or `targetsWeb()`. // Infinte possiblities from aliases in config.php
     * It provides an incredibly expressive, fluent, and future-proof API without cluttering the base class.
     *
     * How it Works (The Arcane Mechanism):
     * 1. Intercepts calls to non-existent methods starting with ['livesOn', 'targets'].
     * 2. Extracts the platform name (e.g., "Telegram" from method name).
     * 3. Securely resolves the corresponding Platform enum case from the extracted name.
     * 4. Performs a hyper-efficient, direct comparison against the current realm's platform.
     *
     * This approach is infinitely scalable. Adding a new platform requires ZERO changes here.
     *
     * @param string $name The dynamic method name (e.g., "livesOnTelegram" or "targetsWeb", or "targetsTg", ....).
     * @param array $arguments The arguments passed to the method (ignored in this case).
     * @return bool True if the current rendering platform matches the one in the method name.
     * @throws BadMethodCallException if the method name does not match the `renderFor[Platform]` pattern.
     */
    public function __call(string $name, array $arguments): bool
    {
        // Define the magic prefixs we are looking for.
        // Supporting both organic presence (livesOn) and active intent (targets)
        $allowedPrefixes = ['livesOn', 'targets'];
        $matchedPrefix = null;

        // High-performance prefix detection
        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($name, $prefix)) {
                $matchedPrefix = $prefix;
                break;
            }
        }

        // 1. Pattern Matching: Check if the method call starts with our magic prefix.
        // This is a high-performance check.
        if ($matchedPrefix) {
            // 2. Platform Extraction: Get the part of the string after the prefix.
            // e.g., "Telegram", "Web", "Cli"
            $platformName = substr($name, strlen($matchedPrefix));

            // 3. Secure Enum Resolution: Attempt to find a Platform case matching the extracted name.
            // We use `tryFrom` with a case-insensitive conversion for maximum robustness.
            // It safely returns null if no matching platform enum exists.
            $targetPlatform = Platform::tryFrom($platformName);
            
            // If `tryFrom` returns null, it means a method like `{_prefix_}InvalidPlatform()` was called.
            // We treat this as a non-matching platform, return false without throwing an error,
            // which can be a more forgiving and flexible behavior in templates.
            if ($targetPlatform === null) {
                return false;
            }

            // 4. The Core Logic (Hyper-Performant):
            // Access the realm and perform a direct, $O(1)$ comparison.
            // The `matches()` method on your Enum is super-perfect for this.
            return app(RenderAura::class) /// Faster-way, than `$this->realm()`, _!_ Utilizing realm(), we can hijack a custome value here, But not Neccesary, as we aleardy did it ;=) in `RenderAura::infuse();`
                   ->platform->matches($targetPlatform);
        }

        // If the method name doesn't match our pattern, it's a legitimate "method not found" error.
        // We throw the standard exception to maintain predictable PHP behavior.
        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.', static::class, $name
        ));
    }

    // =================================================================
    // == Begin ::: Markdown Rendering Control System :::
    // =================================================================

    /**
     * The global static flag to control Markdown rendering across all elements.
     * A per-instance setting will always override this.
     * `null` (default): Inherit/off. `true`: Enable Markdown. `false`: Disable Markdown.
     *
     * @var bool|null
     */
    protected static ?bool $globalMarkdownFlag = null;

    /**
     * The per-instance flag to control Markdown rendering for this specific element.
     * If set, this value takes precedence over the global static flag.
     * `null` (default): Inherit from global. `true`: Enable Markdown. `false`: Disable Markdown.
     *
     * @var bool|null
     */
    protected ?bool $useMarkdown = null;

    /**re4
     * [Hyper-DX Global Setter]
     * Sets the global default for Markdown rendering. This can be called once,
     * for example, in a nexus method, to change the rendering strategy for an entire request.
     *
     * @param bool $enable Sets the desired global state.
     * @return void
     */
    public static function markdownAll(bool $enable = true): void
    {
        static::$globalMarkdownFlag = $enable;
    }

    /**
     * [Hyper-DX Instance Setter]
     * Fluently sets the Markdown rendering preference for this specific instance,
     * overriding any global setting.
     *
     * @param bool $enable The desired state for this instance.
     * @return static Returns the instance for method chaining.
     */
    public function autoMarkdown(bool $enable = true): static
    {
        $this->useMarkdown = $enable;
        return $this;
    }

    /**
     * Determines whether this element should be rendered as Markdown.
     *
     * The decision follows a strict hierarchy, providing maximum control:
     * 1. **Per-Instance Setting:** Checks `$this->useMarkdown`. If not `null`, its value is used.
     * 2. **Global Static Setting:** If the instance flag is `null`, it checks `static::$globalMarkdownFlag`.
     * 3. **Ultimate Default:** If both are `null`, it defaults to `false`.
     *
     * This logic is elegantly executed using the null coalescing operator.
     *
     * @return bool True if Markdown rendering should be used, false otherwise.
     */
    public function shouldRenderMarkdown(): bool
    {
        // Return the first non-null value in the chain: instance -> global -> false.
        return $this->useMarkdown ?? static::$globalMarkdownFlag ?? false;
    }

    // =================================================================
    // == End ::: Markdown Rendering Control System :::
    // =================================================================

    /**
     * Normalizes a given content into a serializable array or a primitive type.
     * @param mixed $content The content to normalize.
     * @return mixed The normalized, serializable content.
    */
    protected function normalize(mixed $content, ?bool $falsyToNull = false): mixed
    {

        if($falsyToNull)
            if(!$content)
                return null;

        // --- FAST PATH ---
        // If the value is not an object or an array, it's a primitive (int, string, bool, null).
        // We return it immediately without any further checks.
        if (!is_object($value) && !is_array($value)) {
            return $value;
        }

        // If the object knows how to become an array, let it.
        // THEN, recursively normalize the result to handle nested complex types.
        if ($content instanceof Arrayable) {
            // <<< THE KEY FIX IS HERE
            // Instead of returning directly, we feed the result back into normalize.
            return $this->normalize($content->toArray());
        }

        /*
        if ($content instanceof Arrayable) {
            return $content->toArray();
        }
        */

        if ($content instanceof \JsonSerializable) {
            return $this->normalize($content->jsonSerialize());
        }

        if ($content instanceof \DateTimeInterface) {
            return $content->format(DATE_ATOM);
        }

        if ($content instanceof \BackedEnum) {
            return $content->value;
        }

        if ($content instanceof \Traversable) {
            return $this->normalize(iterator_to_array($content));
        }

        if (is_array($content)) {
            return array_map([$this, 'normalize'], $content);
        }
        /* a-bit-slower version would be

        if (is_array($content)) {
            return array_map(
                fn (mixed $value): mixed => $this->normalize($value),
                $content
            );
        }*/

        // Primitives like string, int, bool, null are returned as is.
        return $content;
    }

    /**
     * The primary helper method for converting any supported input into a clean, final array.
     * This powerful method first recursively normalizes the input data to resolve all
     * complex types (Entities, Enums, etc.) into primitives and arrays, and then
     * filters out any top-level `null` values from the result.
     *
     * This is the designated helper for `toArray()` implementations in child classes.
     *
     * @param mixed $data The data to be normalized and filtered. Can be an array,
     *                    an Arrayable object, or any other type `normalize` supports.
     * @return array A fully processed, serializable array with no top-level `null` values.
     */
    protected function filterEmpty(mixed $data, ?bool $deep_mode = false): array
    {
        // Step 1: Delegate the complex task of recursive type conversion to the normalize method.
        // This ensures that what we receive back is a pure array of primitives.
        $normalizedData = (
            $deep_mode ? $this->normalize($data) :
            (
                ($data instanceof Arrayable) ?
                    $data->toArray()
                :
                    $data
            )
        );

        // Safety Check: Although `normalize` should return an array for array-like inputs,
        // if a non-array-like primitive was passed in, we ensure the contract (return array) is met.
        if (!is_array($normalizedData)) {
            // A non-array result after normalization cannot be filtered.
            // Returning it within an array might be an option, but for serialization,
            // an empty array is a safer, more predictable default.
            return [];
        }

        // Step 2: Perform the final filtering on the now-guaranteed-to-be-an-array data.
        return array_filter(
            $normalizedData,
            static fn($value): bool => $value !== null
        );
    }

    /**
     * [THE CORE RESOLVER - THE HEART OF ALL CONSTRUCTORS]
     * This is the soul of the composition pattern, now designed to be called
     * directly from within the constructor of any child entity. It inspects
     * the raw input, and if it's a Closure, executes it within an ephemeral
     * RichMan instance to capture the resulting composed content.
     *
     * By centralizing this logic here, we empower every constructor to be "smart"
     * by default, ensuring absolute consistency regardless of how an entity is
     * instantiated, while keeping the target constructors themselves clean.
     * 
     * now supports Laravel Dependency Injection.
     *
     * This is the powerhouse behind the fluent API. It intelligently processes a
     * given piece of content, with special handling for callables. Instead of a
     * simple invocation, it uses Laravel's Service Container (`App::call`) to
     * execute the callable. This provides full Dependency Injection, allowing
     * developers to type-hint services directly in their fluent closures.
     *
     * This method enforces the "Iron Law" of the API: the RichMan instance MUST be the first parameter
     * of any content-generating closure. This strictness ensures predictability, eliminates ambiguity, and
     * guides the developer towards writing clean, consistent code.
     * 
     * The core DI capability of `app()->call()` for other service parameters (after first param)
     * remains fully intact.
     *
     * The return value is optimized:
     * - Returns a single `RichEntity` if the closure produces one element.
     * - Returns an `array` of `RichEntity` if it produces multiple.
     * - Returns `null` if the result is empty.
     * - Passes through non-callable content (strings, existing entities) untouched.
     *
     * @param mixed $content The content to resolve (string, array, RichEntity, callable).
     * @return RichEntity|array|string|null The resolved, ready-to-use content.
    */
    protected static function resolveContent(mixed $content): RichEntity|array|string|null
    {
        // Guard Clause: If it's not a callable, return it as is.
        // This handles strings, arrays, existing RichEntity objects, etc.
        if (!is_callable($content)) {
            return $content;
        }

        // Create a new RichMan instance to serve as the composer for the closure.
        $composer = RichMan::summon();

        $paramsToInject = [];

        // --- The API's Enforcement Gate ---
        try {
            $reflection = new \ReflectionFunction($content);
            $parameters = $reflection->getParameters();

            // The Iron Law: If a closure accepts parameters, the first one
            // is designated for our composer. No exceptions.
            if (!empty($parameters)) {
                $paramsToInject[$parameters[0]->getName()] = $composer;
            }

        } catch (\ReflectionException $e) {
            // Fallback for non-reflectable callables. Extremely rare, but safe to have.
        }

        // Use Laravel's container to call the user's function.
        // But `app()->call` will now act as our enforcer. If the developer placed
        // a different type-hinted parameter first (e.g., Request), this call
        // will fail with a clear TypeError, correcting their code immediately.
        app()->call($content, $paramsToInject);

        // Retrieve the elements generated within the closure.
        $elements = $composer->getElements();
        
        // [THE DIVINE PURIFICATION]
        // We filter the result to ensure ONLY block entities are returned,
        // upholding the sanctity of block containers. This prevents theological corruption.
        /* $elements = array_filter(
            $composer->getElements(),
            fn($entity) => $entity instanceof RichBlockEntity
        ); */

        // Smart Return Logic:
        // - If the array is empty, it means nothing was generated. Return null.
        if (empty($elements)) {
            return null;
        }
        
        // - If the array contains exactly one element, return that element directly.
        //   This cleans up constructors that expect a single entity, not an array with one.
        if (count($elements) === 1) {
            return $elements[0];
        }

        // - If there are multiple elements, return the full array as they were composed.
        return $elements;
    }

    /**
     * Converts an associative array of attributes into a string for an HTML tag.
     * This method centrally handles escaping of all attribute values, enhancing security.
     * It intelligently ignores null, false, or empty string values.
     *
     * Example: ['class' => 'map', 'data-id' => 123, 'disabled' => false]
     * becomes: class="map" data-id="123"
     *
     * @param array<string, mixed> $attributes The attributes to convert.
     * @return string The generated HTML attribute string.
    */
    protected function attributesToString(array $attributes): string
    {
        $htmlParts = [];
        foreach ($attributes as $key => $value) {
            // Skip attributes that are null, false, or empty strings.
            // This is useful for boolean attributes where their absence means 'false'.
            if ($value === null || $value === false || $value === '') {
                continue;
            }

            // For true boolean attributes, just output the key (e.g., 'disabled').
            if ($value === true) {
                $htmlParts[] = $this->esc($key);
            } else {
                // For all other attributes, create a "key="value"" pair, ensuring the value is escaped.
                $htmlParts[] = $this->esc($key) . '="' . $this->esc((string)$value) . '"';
            }
        }
        return implode(' ', $htmlParts);
    }

    /**
     * Centralized HTML escaping function.
     * All HTML output of string content should pass through this method to ensure security
     * and allow for global changes to escaping strategy (e.g., switching libraries, at once).
     *
     * @param string|null $value The string to escape.
     * @return string The escaped string.
    */
    protected function esc(?string $value): string
    {
        // Using PHP's built-in htmlspecialchars is a robust default.
        // ENT_QUOTES ensures both single and double quotes are escaped.
        // false for $double_encode prevents double-escaping entities.
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
    }

    /**
     * [The Guardian of Syntax - Final Version]
     *
     * Centralized Markdown V2 character escaping function, fully compliant with
     * Telegram's official documentation. It escapes all required characters for
     * general-purpose text content to be treated as a literal string.
     *
     * This method is the cornerstone of preventing markup injection and API errors.
     *
     * @see https://core.telegram.org/bots/api#markdownv2-style
     *
     * @param string|null $value The string to escape for Telegram's MarkdownV2.
     * @return string The escaped, safe string.
    */
    protected function escForMd(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        // According to the documentation, in "all other places", these characters must be escaped.
        // We also include the backslash '\' itself, as it's the escape character.
        // The order matters: we must escape the escape character first.
        $chars = ['\\', '_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
        
        $escapedChars = [];
        foreach ($chars as $char) {
            $escapedChars[] = '\\' . $char;
        }

        return str_replace($chars, $escapedChars, $value);
    }

    /**
     * Recursively renders any content into a safe HTML string.
     * This hyper-intelligent engine centralizes the logic for unwrapping
     * nested Arrayable and Htmlable structures, simplifying component-level code.
     *
     * @param mixed $content The content to render (object, array, string).
     * @return string The resulting HTML string.
    */
    protected function renderHtml(mixed $content): string
    {

        // Priority 0: _fAST-fAIL_ Graceful handling of empty values.
        if ($content === null || $content === false) {
            return '';
        }

        // Priority 1: Htmlable Interface.
        // If an object explicitly knows how to present itself as HTML,
        // we honor that contract above all else. This is the primary render path.
        if ($content instanceof Htmlable) {
            // This allows components like RichBlockTable to inject their wrapper tags (<table>).
            return $content->toHtml();
        }

        // Priority 2: The New Powerhouse - Arrayable Interface.
        // If we get an object that can be an array (like a Collection or any RichEntity
        // that wasn't rendered via its own toHtml), we unwrap it one level
        // and recursively pass its contents back to this same engine.
        // This is the core of handling nested structures automatically.
        if ($content instanceof Arrayable) {
            return $this->renderHtml($content->toArray());
        }

        // Priority 3: Plain Arrays.
        // This now handles the output of Arrayable objects or simple arrays of elements.
        // It recursively renders each item and concatenates the results.
        if (is_array($content)) {
            return implode('', array_map([$this, 'renderHtml'], $content));
        }

        // Priority 4: Primitives.
        // For simple strings or Stringable objects, we escape them to prevent XSS.
        // This is a terminal point in the recursion.
        if (is_string($content) || $content instanceof Stringable) {
            return $this->esc((string)$content);
        }

        // Fallback for any unknown or unrenderable types.
        // This maintains safety in production and provides debug info in development.
        if (app()->isProduction()) {
            return '';
        }
        return '<!-- Cannot render type: ' . gettype($content) . ' -->';
    }

    /**
     * Recursively renders any content into a rich, formatted text string.
     * This hyper-intelligent engine is the text-based counterpart to `renderHtml`.
     * It centralizes the logic for unwrapping nested structures and allows entities
     * to provide platform-specific text formatting (e.g., Markdown for Telegram).
     *
     * It follows a similar recursive pattern to `renderHtml` but prioritizes the `toText()` contract.
     *
     * @param mixed $content The content to render (object, array, string).
     * @return string The resulting formatted text string.
    */
    protected function renderText(mixed $content): string
    {
        // Priority 0: Graceful handling of empty values.
        /// if ($content === null || $content === false) {
        if (!$content) {
            return '';
        }

        // Priority 1: The `toMd()|OR|toText()` Contract.
        // If an object explicitly knows how to present itself as formatted text,
        // we honor that contract. This is the primary render path for all entities
        // and the main extension point for custom formatting.
        // Check if the content is an object that can have custom rendering methods.
        if (is_object($content)) {
            // 1. Is Markdown rendering enabled for this context?
            if ($this->shouldRenderMarkdown()) {
                // 2. Does this specific object have a specialized `toMd` method?
                if (method_exists($content, 'toMd')) {
                    // If yes, delegate to its high-fidelity Markdown renderer.
                    return $content->toMd();
                }
            }
            
            // 3. Fallback: If Markdown is off, or if `toMd` doesn't exist,
            //    we fall back to the universal `toText` contract.
            if (method_exists($content, 'toText')) {
                return $content->toText();
            }
        }
        if (is_object($content) && method_exists($content, 'toText')) {
            return $content->toText();
        }

        // Priority 2: Arrayable Interface.
        // Unwraps Arrayable objects (like Collections or Entities without a toText)
        // and recursively feeds their contents back into this same engine.
        if ($content instanceof Arrayable) {
            return $this->renderText($content->toArray());
        }

        // Priority 3: Plain Arrays.
        // This handles the output of Arrayable objects or simple arrays of elements.
        // It recursively renders each item and concatenates them with no separator.
        // The responsibility of adding separators (like newlines) belongs to the
        // `toText()` implementation of block-level entities.
        if (is_array($content)) {
            return implode('', array_map([$this, 'renderText'], $content));
        }

        // Priority 4: Primitives.
        // Simple strings or Stringable objects are the terminal points of the recursion.
        // They are returned as-is, without escaping, as this is a text context.
        if (is_string($content) || $content instanceof Stringable) {
            // When rendering a raw string within a Markdown context, it MUST be md-escaped
            // to prevent accidental formatting.
            return $this->shouldRenderMarkdown() ? $this->escForMd((string)$content) : (string)$content;
        }

        // Fallback for any unknown or unrenderable types.
        if (app()->isProduction()) {
            return '';
        }
        return '[Cannot render type to text: ' . gettype($content) . ']';
    }


    /**
     * Recursively renders any content into a plain text string.
     * This is the essential method for alt tags, summaries, etc.
     *
     * @param mixed $content The content to convert to plain text.
     * @return string The resulting plain text string.
    */
    protected function renderPlainText(mixed $content): string
    {
        if($content === null)
            return '';

        // If the object has a dedicated `toText` method, use it.
        // This allows objects like RichBlockCaption to provide a specific plain text format.
        if (is_object($content) && method_exists($content, 'toText')) {
            return $content->toText();
        }

        // If we receive an array, recursively convert each item and join them with a space.
        // A space is a sensible default separator for lists of text elements.
        if (is_array($content)) {
            // Filter out empty results before joining to avoid extra spaces.
            $parts = array_filter(array_map([$this, 'renderPlainText'], $content));
            return implode(' ', $parts);
        }

        // For simple strings, return them directly without any modification.
        if (is_string($content) || $content instanceof Stringable) {
            return (string)$content;
        }
        
        // Null or other non-stringable types should result in an empty string.
        return '';
    }

    /**
     * A specific helper to render an array of RichBlock elements.
     *
     * @param array<RichComponentEntity&Htmlable> $blocks The array of blocks to render.
     * @return string The concatenated HTML of all blocks.
    */
    protected function renderBlocks(array $blocks): string
    {
        return $this->renderHtml($blocks);
    }

    public function render(): string
    {
        if ($this->targetsCli()) {
            if (method_exists($this, 'toText') && is_callable([$this, 'toText'])) {
                // The entity knows how to represent itself as clean text. This is the ideal path.
                return $this->toText();
            }
            // --- The Developer-Friendly Fallback ---
            return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        return $this->toHtml();
    }

    public function __toString(): string
    {
        // Ensure no exception bubbles through __toString in production
        try {
            return $this->render();
        } catch (\Throwable $e) {
            report($e);
            return '';
        }
    }

    abstract public function toArray(): array;
    abstract public function toHtml(): string;
}
