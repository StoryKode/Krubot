<?php
declare(strict_types=1);

namespace KrubiK\Render\Kernel;

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

use Illuminate\Contracts\Foundation\Application;
use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\RichMan;
use KrubiK\Facades\Parsentinel;
use LogicException;

/**
 * The Celestial Conductor of Story Composition.
 *
 * This builder does not manage entities directly. Instead, it orchestrates an army of
 * RichMan composers. Each nested component in Blade gets its own temporary composer.
 * When a component closes, its composer's contents are harvested to build the final,
 * immutable parent entity, which is then passed to the parent composer.
 * This respects the "build-then-create" philosophy of the RichMan architecture.
 *
 * @version 3.0 - The Composer Stack architecture
 */
class BladeCipher
{
    public const BUILDER_CLASS = BladeCipher::class;
    public const ConfigPrefix = 'krubot.blade-cipher.compiler.modes.';
    private const HELPERS_NAMESPACE = 'KrubiK\\Render\\Helpers\\';

    /**
     * [CORRECTED] GROUP 1: VOID / SELF-CLOSING DIRECTIVES
     * Added missing simple directives.
     */
    private const VOID_MAP = [
        'Anchor'                => 'anchor',
        'CustomEmoji'           => 'customEmoji',
        'MathematicalExpression'=> 'mathematicalExpression',
        'Math'                  => 'mathematicalExpression', // ALIAS
        'Divider'               => 'divider',
        'Separator'             => 'separator', // ADDED
        'NewLine'               => 'newLine', // ADDED
        'Heading'               => 'heading',
        'Photo'                 => 'photo',
        'Video'                 => 'video',
        'Animation'             => 'animation',
        'Audio'                 => 'audio',
        'VoiceNote'             => 'voiceNote',
        'Map'                   => 'map',
    ];

    /**
     * [CORRECTED] GROUP 2: WRAPPER DIRECTIVES
     * Added missing aliases for better DX.
     */
    private const WRAPPER_MAP = [
        // Simple Text Wrappers
        'Text'          => 'text',
        'Bold'          => 'bold',
        'Italic'        => 'italic',
        'Underline'     => 'underline',
        'Strikethrough' => 'strikethrough',
        'Spoiler'       => 'spoiler',
        'Code'          => 'code',
        'Marked'        => 'marked',
        'Subscript'     => 'subscript',
        'Superscript'   => 'superscript',
        'Plain'         => 'plain',
        
        // Block-level Text Wrappers
        'Paragraph'     => 'paragraph',
        'Footer'        => 'footer',
        'Thinking'      => 'thinking',
        'PullQuotation' => 'pullQuotation',

        // Complex Wrappers
        'Href'          => 'href',
        'Url'           => 'href', // ALIAS
        'AnchorLink'    => 'anchorLink',
        'BankCardNumber'=> 'bankCardNumber',
        'BotCommand'    => 'botCommand',
        'Cashtag'       => 'cashtag',
        'DateTime'      => 'dateTime',
        'EmailAddress'  => 'emailAddress',
        'Email'         => 'emailAddress', // ALIAS
        'Hashtag'       => 'hashtag',
        'MentionByUsername' => 'mention', // CORRECTED to point to 'mention' helper
        'TextMention'   => 'textMention',
        'PhoneNumber'   => 'phoneNumber',
        'Pre'           => 'pre',
        'PreBlock'      => 'preBlock', // ADDED for raw code blocks
        'Reference'     => 'reference',
        'ReferenceLink' => 'referenceLink',
        'Caption'       => 'caption',
    ];

    /**
     * [CORRECTED] GROUP 3: STRUCTURAL BLOCK DIRECTIVES
     * Added specific list directives for better semantics.
     */
    private const STRUCTURE_MAP = [
        // Generic
        'BlockQuotation'    => 'blockQuotation',
        'Details'           => 'details',
        'ListBlock'         => 'listBlock', // Generic fallback
        'ListItem'          => 'listItem',
        'Table'             => 'table',
        'TableCell'         => 'tableCell',
        'Cell'              => 'cell',
        'Collage'           => 'collage',
        'Slideshow'         => 'slideshow',
        'FootnoteDefinition'=> 'footnoteDefinition',
        // Specific & More Readable
        'BulletList'        => 'bulletList', // ADDED
        'OrderedList'       => 'orderedList', // ADDED
    ];

    /**
     * The stack of RichMan instances. The end of the array is the active composer.
     * @var RichMan[]
     */
    private array $composerStack = [];

    /**
     * A parallel stack to hold the arguments of the opening components.
     * When @EndDetails is called, we need to know the 'summary' passed to @Details.
     * @var array[]
    */
    private array $argumentStack = [];

    private array $harvested = []; // The storage for our named channels

    /**
     * @var bool Determines if the compiler is currently processing a valid rich-enabled view.
    */
    protected static bool $active = false;

    /**
     * @var bool Indicates whether the engine is actively capturing and converting outputs into entities.
    */
    protected bool $isCapturing = false;

    // --- Singleton Pattern for easy access ---
    protected static ?self $instance = null;

    /**
     * Taps into the global render stream.
     * This is the sole entry point to acquire the active BladeCipher instance.
     *
     * @return self The one true instance of the BladeCipher engine.
    */
    public static function stream(): self
    {
        // The Gatekeeper. This check is the heart of the Singleton's stability.
        // It is the switch that determines if we are initiating a new stream or
        // tapping into the existing one.
        if (self::$instance === null) {
            // BENEFIT OF THIS CHECK: By executing the constructor only ONCE, we guarantee
            // a single, consistent state (stacks, context). We listen to the entire story
            // on one channel, not a cacophony of many. This is efficiency and sanity.

            // First Contact: The stream is not yet flowing. We must forge the conduit.
            // This is the one-time action of creating the instance that will ever exist.
            self::$instance = new self();
        }

        // ABSENCE OF THIS CHECK: If we blindly called `new self()` every time, it would be
        // chaos. Each `::stream()` call would spawn a new, isolated universe with its own
        // empty stacks. The `begin()` on one instance would have no relation to the `end()`
        // on another. The pattern, and the story, would be utterly broken.

        // Return the established, living channel, ready to receive commands (->begin()).
        return self::$instance;
    }
    public static function getInstance(): self
    {
        return self::stream();
    }
    // Make constructor private to enforce singleton
    private function __construct() {}
    // Prevent cloning
    private function __clone(): void
    {
        throw new LogicException('Cannot clone a singleton.');
    }

    // Prevent unserialization
    public function __wakeup(): void
    {
        throw new LogicException('Cannot unserialize a singleton.');
    }

    // --- End Singleton ---

    // --- Context Management ---

    public static function activate(bool $on = true): void
    {
        self::$active = $on;
    }

    public function setRichContext(bool $isRich): void
    {
        self::$active = $isRich;
    }

    public function isInRichContext(): bool
    {
        return self::$active;
    }

    /**
     * [THE GENESIS] Begins the story capture process.
     * A root composer is created to hold the entire story.
     */
    public function begin(): void
    {
        if (!self::$active) {
            throw new LogicException('The divine capture engine cannot be summoned outside of a rich context.');
        }

        if ($this->isCapturing) {
            throw new LogicException('Cannot start a new story capture while another is in progress. Check for nested @Story directives.');
        }

        ob_start();
        $this->composerStack = [RichMan::summon()]; // The root composer
        $this->argumentStack = [];
        $this->harvested = []; // Reset on begin
        $this->isCapturing = true;
    }

    /**
     * [THE ASCENSION] Ends the story capture, returning the final masterpiece.
     *
     * @return SoulHarvestor Ends the capture and returns the final "SoulHarvestor" treasure chest, containing the entire nested structures.
     */
    public function end(): ?SoulHarvestor
    {
        if (!$this->isCapturing) {
            return null;
        }

        try {
            // Capture any final trailing text
            $this->captureBufferedContent();

            $this->flushNestedComponents();
            
            // At the end, only the root composer should remain on the stack.
            if (count($this->composerStack) !== 1) {
                throw new LogicException('Mismatched component directives. A @End... tag is likely missing.');
            }

            $finalMasterpiece = array_pop($this->composerStack);

            // Reset the state for the next symphony
            $this->isCapturing = false;
            $this->composerStack = [];
            $this->argumentStack = [];
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            return new SoulHarvestor($finalMasterpiece, $this->harvested);

        } finally {

            // Reset the state for the next symphony
            $this->isCapturing = false;
            $this->composerStack = [];
            $this->argumentStack = [];

            ///ob_end_clean();
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
        }
    }

    public function endAndRender(): string
    {
        $masterpiece = $this->end();
        return $masterpiece?->render() ?? '';
    }

    /**
     * [THE SUB-DIMENSION] A new structural component begins.
     * A new, temporary composer is pushed onto the stack to capture its children.
     *
     * @param string $helperFunctionName The name of the helper function (e.g., 'details', 'blockQuotation').
     * @param array $arguments The arguments passed to the directive, EXCLUDING the children.
     */
    public function startComponent(string $helperFunctionName, array $arguments): void
    {
        $this->captureBufferedContent();

        // Push the component's arguments for later construction
        $this->argumentStack[] = [
            'name' => $helperFunctionName,
            'args' => $arguments,
        ];

        // Push a new, blank composer to capture the children of this component.
        $this->composerStack[] = RichMan::summon();
    }
    
    /**
     * [THE CONVERGENCE] A structural component ends.
     * The child composer is consumed, the final entity is built, and it's added to its parent.
     */
    public function endComponent(): void
    {
        $this->captureBufferedContent();

        // Ensure we're actually inside a component
        if (count($this->composerStack) < 2) {
            throw new LogicException('Mismatched component directives. Found an @End... tag with no matching opening tag.');
        }

        // Pop the child composer and harvest its elements.
        $childComposer = array_pop($this->composerStack);
        $children = $childComposer->getElements();

        // Pop the parent component's saved arguments.
        $parentComponentData = array_pop($this->argumentStack);
        $helperName = $parentComponentData['name'];
        $arguments = $parentComponentData['args'];
        
        // Add the harvested children as the final argument. This is the convention our helpers follow.
        $arguments[] = $children;

        
        // Dynamically call the global helper function (e.g., details('summary', $children))
        // This is where the final, immutable entity is created in one go.
        // Ensure your helper functions are globally accessible.

        /** @var RichEntity $fullyFormedEntity */
        $fullyFormedEntity = $helperName(...$arguments);

        // Add the newly-minted, complete entity to its parent composer (which is now at the top of the stack).
        $this->addComponent($fullyFormedEntity);
    }

    private function pushDirectEntity(RichEntity $element): void
    {
        if (!empty($this->composerStack)) {
            $this->composerStack[array_key_last($this->composerStack)]->add($element);
        }
    }

    // ─────────────────────────────────────────────────────
    // [NEW] flushNestedComponents() — پشتیبانی از
    //         تودرتویی بینهایت: تمام pending components
    //         را fold-up می·کند به parent
    // ─────────────────────────────────────────────────────
    private function flushNestedComponents(): void
    {
        while (count($this->composerStack) > 1) {
            $childComposer = array_pop($this->composerStack);
            $children = $childComposer->getElements();

            if (!empty($this->argumentStack)) {
                $parentData = array_pop($this->argumentStack);
                $helperName = $parentData['name'];
                $arguments  = $parentData['args'];
                $arguments[] = $children;

                try {
                    $entity = $helperName(...$arguments);
                    $this->addComponent($entity);
                } catch (\Throwable) {
                    // Fail-safe: children را مستقیماً attach کن
                    $placeholder = RichMan::summon();
                    $this->addComponent($placeholder);
                    foreach ($children as $child) {
                        $placeholder->add($child);
                    }
                }
            } else {
                // No parent argument — children را مستقیم
                // به composer قبلی اضافه کن
                $target = end($this->composerStack);
                foreach ($children as $child) {
                    $target->add($child);
                }
            }
        }
    }

    /**
     * Harvests the currently built MAIN story into a named channel
     * and starts a new, fresh main story.
    */
    public function harvest(string $name): void
    {
        if (!$this->isInRichContext()) return;

        $this->flushNestedComponents(); /// ???
        
        // Take the current main composer (at the bottom of the stack)
        $storyToHarvest = $this->composerStack[0];
        
        // Store it
        $this->harvested[$name] = $storyToHarvest;
        
        // Replace the main composer with a fresh one to continue building, so content continues cleanly.
        $this->composerStack[0] = RichMan::summon();
    }
    
    /**
     * [THE ATOMIZATION] Adds a simple, non-structural entity to the current composer.
     */
    public function addComponent(RichEntity $element): void
    {
        $this->captureBufferedContent();
        
        // Add the element to the currently active composer (the one on top of the stack).
        if (!empty($this->composerStack)) {
            end($this->composerStack)->add($element);
        }
    }

    /**
     * Captures any buffered plain text (like spaces, newlines, or text outside directives)
     * and adds it as a RichTextPlain entity to the current composer.
     */
    private function captureBufferedContent(): void
    {
        if (!$this->isCapturing) return;
        if (ob_get_level() > 0) {
            $buffered = ob_get_clean();
            if ($buffered !== false && $buffered !== '') {
                ///  if (! empty($this->composerStack)) /// Guard so a rogue call outside begin/end does not leave orphan ob_start().
                    $this->addComponent(plain($buffered));
            }
            ob_start();
        }
    }


    /////////////////////////////////////////////////////////////////////
    // *Another Side of the Moon*...
    /////////////////////////////////////////////////////////////////////

    public static function registerOnSP(Application $app): void
    {
        // Register the BladeCipher as a singleton for the entire request lifecycle.
        // [FIX] self::stream() is called inside the closure, which is bound correctly.
        $app->singleton(self::BUILDER_CLASS, static fn () => self::stream());
        self::$instance = app(self::BUILDER_CLASS); // ← NEW: sync once in static cache

        // Replace Laravel's default blade compiler with our context-aware version.
        // This is a high-level, powerful override.
        $app->singleton('blade.compiler', function ($app) {
            return new RichBladeCompiler(
                $app['files'],
                $app['config']['view.compiled']
            );
        });
    }

    /**
     * Bootstrap any application services.
     * This is where we define our directives and context awareness.
    */
    public static function bootOnSP(Application $app): void
    {
        self::registerVoidDirectives(self::VOID_MAP);
        self::registerWrapperDirectives(self::WRAPPER_MAP);
        self::registerStructuralDirectives(self::STRUCTURE_MAP);

        // Step 1: Gather all extensions that signify a "Rich Context"
        // from the single source of truth: your config file.
        $dataGenExts = config(self::ConfigPrefix . 'data_generation.extensions', ['.r.blade.php']);
        $autoRenderExts = config(self::ConfigPrefix . 'auto_render.extensions', ['.rtale.blade.php']); // Logically, these are also rich context!
        $manualExts = config(self::ConfigPrefix . 'manual_control.extensions', ['.rich.blade.php']);

        // Collect all rich-context file extensions from config.
        $allRichExtensions = array_merge($dataGenExts, $manualExts, $autoRenderExts);

        // [PERF] Build a Set (flipped array) for O(1) lookup instead of O(n) loop.
        $richExtSet = array_flip($allRichExtensions);

        // THE REDEMPTION: Instead of a clumsy loop, we forge a single, powerful Regex.
        // This combines all our sacred extensions into one decisive question, not a
        // thousand whispers. This is true O(1) thinking for a string-end problem.
        $richExtRegex = '/(' . implode('|', array_map(fn($ext) => preg_quote($ext, '/'), $allRichExtensions)) . ')$/';


        // Set up the "Context Guardian" to check every view being rendered.
        // This View Composer is a powerful spell. It runs for EVERY view being rendered.
        // It checks the file's name. If it ends with '.rich.blade.php' or '.r.blade.php',
        // it activates the BladeCipher's capturing mode. This is the switch that
        // turns our magic on and off automatically for the correct files.
        View::composer('*', function ($view) use ($richExtSet) {

            /** @var \Illuminate\View\View $view */
            $path = $view->getPath();

            // One check to rule them all. Does the path bear the sacred mark?
            $isRichContext = (bool) preg_match($richExtRegex, $path);

            /*
            $isRichContext = false;
            foreach ($allRichExtensions as $ext) {
                // Check if the file has the spiritual-rich seal.
                if (str_ends_with($path, $ext)) {
                    $isRichContext = true;
                    break; // Fast-exit. No need to check further.
                }
            }
            */

            // Activate the stream only if the context is worthy.
            self::activate($isRichContext);
        });
        
        // @Cipher: The ritual of beginning. Opens the stream.
        // This tells the engine: "Start listening. The story is about to unfold."
        Blade::directive('Cipher', fn() => "<?php if(" . self::BUILDER_CLASS . '::$active) ' . self::BUILDER_CLASS . "::stream()->begin(); ?>");

        // @EndCipher: The grand finale. Closes the stream and reaps the harvest.
        // It captures everything that has been woven and hands back the complete SoulHarvestor.
        Blade::directive('EndCipher', fn($var) => "<?php if(" . self::BUILDER_CLASS . '::$active) ' . $var . '} = ' . self::BUILDER_CLASS . "::stream()->end(); ?>");

        // @Harvest: A targeted reaping. Used to mark a specific point in the stream for later retrieval.
        // Not an end, but a significant milestone within the flow. "Remember this moment."
        Blade::directive('Harvest', function (string $expression): string {
            // The expression is the channel name, e.g., "'sidebar'"
            return "<?php if (" . self::BUILDER_CLASS . '::$active) ' . self::BUILDER_CLASS . "::stream()->harvest({$expression}); ?>";
        });

        // --- [NEW] Raw Content Parsing Directives ---
        // These directives capture their entire inner content as a raw string and send it to a dedicated parser.
        self::registerContentParsingDirectives();
        
        // --- Handling raw text inside blocks ---
        Blade::precompiler(function ($string) {
            // This is an advanced technique to capture raw text between your directives.
            // It wraps text nodes so the BladeCipher can process them.
            // Be cautious, this regex can be complex. For now, a manual approach is safer.
            // A simpler way is to let Blade echo text and use ob_start/ob_get_clean inside start/end directives.
            // Let's stick to explicit directives for now to ensure stability.
            // If you need raw text capture, we can engineer a more robust solution.
            return $string;
        });
    }

    // --- Self-closing / Inline Component Directives ---
    private static function registerVoidDirectives(array $map): void
    {
        // This was already correct.
        foreach ($map as $directive => $helper) {
            Blade::directive($directive, function (string $expression) use ($helper): string {
                $helperFullName = self::HELPERS_NAMESPACE . $helper;
                return "<?php if (" . self::BUILDER_CLASS . '::$active) ' . self::BUILDER_CLASS . "::stream()->addComponent({$helperFullName}({$expression})); ?>";
            });
        }
    }

    // --- Block Component Directives ---
    private static function registerWrapperDirectives(array $map): void
    {
        foreach ($map as $directive => $helper) {
            $helperFullName = self::HELPERS_NAMESPACE . $helper;

            Blade::directive($directive, function (string $expression) use ($helperFullName): string {
                // Inline version: @Bold('text')
                if (!empty(trim($expression))) {
                    return "<?php if (" . self::BUILDER_CLASS . '::$active) ' . self::BUILDER_CLASS . "::stream()->addComponent({$helperFullName}({$expression})); ?>";
                }

                // [!!! CORRECTION !!!] Block version: @Bold ... @EndBold
                // We must pass the HELPER NAME (a string) and its ARGS (an array) to startComponent.
                // For wrappers like @Bold, there are no initial arguments.
                $helperNameAsString = var_export($helperFullName, true);
                return "<?php if (" . self::BUILDER_CLASS . '::$active) ' . self::BUILDER_CLASS . "::stream()->startComponent({$helperNameAsString}, []); ?>";
            });

            Blade::directive('End' . $directive, function (): string {
                // This was already correct.
                return "<?php if (" . self::BUILDER_CLASS . '::$active) ' . self::BUILDER_CLASS . "::stream()->endComponent(); ?>";
            });
        }
    }

    // --- Structure directives — template static
    private static function registerStructuralDirectives(array $map): void
    {
        foreach ($map as $directive => $helper) {
            $helperFullName = self::HELPERS_NAMESPACE . $helper;

            Blade::directive($directive, function (string $expression) use ($helperFullName): string {
                // [!!! CORRECTION !!!]
                // We must pass the HELPER NAME (a string) and its ARGS (an array) to startComponent.
                // The $expression from Blade already contains the arguments, so we wrap it in array brackets.
                $helperNameAsString = var_export($helperFullName, true);
                return "<?php if (" . self::BUILDER_CLASS . '::$active) ' . self::BUILDER_CLASS . "::stream()->startComponent({$helperNameAsString}, [{$expression}]); ?>";
            });

            Blade::directive('End' . $directive, function (): string {
                // This was already correct.
                return "<?php if (" . self::BUILDER_CLASS . '::$active) ' . self::BUILDER_CLASS . "::stream()->endComponent(); ?>";
            });
        }
    }

    /**
     * Registers block directives that parse raw content like Markdown or HTML.
     * These operate by capturing the entire block's output into a string and then
     * invoking the appropriate parser from the Parsentinel service.
    */
    private static function registerContentParsingDirectives(): void
    {
        // @RichMD ... @EndRichMD
        // This directive starts output buffering.
        Blade::directive('RichMD', function () {
            return "<?php if (" . self::BUILDER_CLASS . "::\$active) { ob_start(); } ?>";
        });

        // This directive ends buffering, gets the content, passes it to the Markdown parser,
        // and adds the resulting RichEntity to the current composer if it's inside a @Cipher block, otherwise renders it's contetns.
        Blade::directive('EndRichMD', function () {
             // The generated PHP now contains the full context-aware logic.
             return self::generateContentParsingEndDirectiveLogic('RichMD');
        });

        // @RichHTML ... @EndRichHTML
        // This directive starts output buffering.
        Blade::directive('RichHTML', function () {
            return "<?php if (" . self::BUILDER_CLASS . "::\$active) { ob_start(); } ?>";
        });

        // This directive ends buffering, gets the content, passes it to the HTML parser,
        // and adds the resulting RichEntity to the current composer if it's inside a @Cipher block, otherwise renders it's contetns.
        Blade::directive('EndRichHTML', function () {
             // The generated PHP now contains the full context-aware logic.
             return self::generateContentParsingEndDirectiveLogic('RichHTML');
        });
    }

    /**
     * Generates the runtime PHP code for content-parsing @End... directives.
     * This code summons a Parsentinel warden, deciphers the content, and then
     * either adds it to the BladeCipher stream or echoes it, based on the context.
     *
     * @param string $parserType The key for the parser ('RichMD', 'RichHTML').
     * @return string The PHP code string to be embedded in the compiled view.
     */
    private static function generateContentParsingEndDirectiveLogic(string $parserType): string
    {
        // We get the full class names to avoid namespace issues in the compiled view.
        $parsentinelPhoneNumber = Parsentinel::class;
        $bladeCipherClass       = self::BUILDER_CLASS;

        return "<?php
            \$__richContent = ob_get_clean();
            if (isset(\$__richContent) && \$__richContent !== '') {
                // 1. Summon the appropriate warden via Parsentinel.
                \$__warden = {$parsentinelPhoneNumber}::summon('{$parserType}');
                
                // 2. Decipher the raw content into an array of RichEntity objects.
                \$__entities = \$__warden->decipher(\$__richContent);

                // 3. Check if we are inside a divine capture (@Cipher block).
                if ({$bladeCipherClass}::stream()->isInRichContext()) {
                    // 4a. If YES, add each deciphered entity to the BladeCipher stream.
                    foreach (\$__entities as \$__entity) {
                        {$bladeCipherClass}::stream()->addComponent(\$__entity);
                    }
                } else {
                    // 4b. If NO, render each entity directly to the output.
                    foreach (\$__entities as \$__entity) {
                        echo \$__entity->render();
                    }
                }
            }
        ?>";
    }

    public static function introduceTo(array &$provided): void
    {
        array_push(
            $provided,

            self::class, 'blade.compiler'
        );
    }
}
