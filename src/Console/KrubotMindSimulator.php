<?php
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

namespace KrubiK\Console;

// --- Core & Framework Imports ---
use Illuminate\Console\Command;
use KrubiK\Krubot;
use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use Illuminate\Support\Str;

// --- KrubiK Attribute Imports ---
use KrubiK\Attributes\WarLord; // Import the sacred scripture to help the Scroll.
use KrubiK\Attributes\Action;
use KrubiK\Attributes\Middleware;
use KrubiK\Attributes\Name;
use KrubiK\Attributes\OnCommand;
use KrubiK\Attributes\OnRegEx;
use KrubiK\Attributes\OnText;
use KrubiK\Attributes\OnType;
use KrubiK\Attributes\RestrictTo;
use KrubiK\Attributes\WebApp;
use KrubiK\Attributes\WebAction;
use KrubiK\Attributes\WebPage;

// --- Laravel Prompts - The HyperDX Engine ---
use function Laravel\Prompts\{
    select, info, note, warning, outro, spin, text, confirm
};

use Symfony\Component\VarDumper\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

/**
* @author DoKtor K.
* @link https://StoryKo.de/Krubot Official website of engine.
* @version Krubot: ×RC.8×
* @license MIT
*/
class KrubotMindSimulator extends Command
{
    /**
     * The signature of the command.
     * API is preserved, but enhanced with powerful options for true HyperDX.
     *
     * 
     * The new --mode option allows switching between three distinct inspection levels:
     * 'simple': A fast, legacy table view.
     * 'complex': A detailed, non-interactive dump of all Nexus manifests.
     * 'interactive': The full, live simulation experience (default).
     * 
     * The default execution mode is now set to 'pro' (alias for 'complex').
     * 
     * "Pro Mode" optimizes the KrubotMindSimulator for power users who require a full, non-interactive
     * manifest dump for immediate analysis. The tool prioritizes data over interaction.
     * 
     * @var string
    */
    protected $signature = 'nexus:inspect
                            {--mode=pro : Mode (simple|basic|legacy, details|complex|pro, live|advanced|interactive)}
                            {--lang=en : Set the Interface language (en, fa)}
                            {--theme=dark : Set the Color theme (dark, light)}
                            {--reveal|oracle|symfony|dump : Force Unleash a raw, deep introspection of manifests via the Symfony VarDumper}
                            {--simple|p : Shortcut to force simple/basic mode. Overrides --mode}
                            {--pro|p : Shortcut to force complex/pro/details mode. Overrides --mode}
                            {--live|p : Shortcut to force live/interactive mode. Overrides --mode}';

    /**
     * The console command aliases.
     * 
     * @var array<int, string>
    */
    protected $aliases = ['krubot:mind-simulator', 'krubik:nexus-list'];

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Interactively inspect, profile, and simulate Krubot\'s routing logic for any Nexus.';
    //protected $description = 'Interactively inspect registered Nexuses, their routes, attributes, and final computed state.';
    //protected $description = 'Inspect, profile, and simulate Krubot Nexuses with professional-grade detail.';

    /**
     * Localization strings for multi-language support.
     */
    private const I18N = [
        'en' => [
            'header' => "KrubiK Nexus Mind-Simulator v12.ULTIMATE",
            'no_nexus' => "No Nexuses are registered in the Krubot instance.",
            'select_nexus' => "Which Nexus do you wish to probe?",
            'select_nexus_hint' => "Select a Nexus to dissect its neural pathways (routes).",
            'exit' => "Exit Simulator",
            'inspecting_nexus' => "Probing <fg={primary};options=bold>{shortName}</>. Select a handler:",
            'back_to_list' => "← Return to Nexus List",
            'reflection_fail' => "Could not analyze Nexus '{className}': {message}",
            'profile_for' => "Full Route Profile for: <fg={primary}>{shortName}::{methodName}</>",
            'sim_prompt' => "Simulate this route's behavior?",
            'sim_input_prompt' => "Enter a test input to match against this route's trigger:",
            'sim_input_placeholder' => "e.g., {placeholder}",
            'simulating' => "Simulating Krubot's response...",
            'match_success' => "✅ MATCH SUCCESS!",
            'match_fail' => "❌ MATCH FAILED.",
            'match_reason' => "Reason: The provided input '{input}' does not match the trigger pattern '{pattern}'.",
            'session_concluded' => "Simulation session concluded.",

            'complex_mode_header' => "Mode: Complex Report (Full Manifest Dump)",
            'nexus_manifest_header' => "NEXUS MANIFEST: {shortName} ({nexusClass})",
            'complex_report_complete' => "Complex report generation complete.",
            'could_not_analyze' => "Could not analyze Nexus '{className}': {message}",
            'no_handlers_found' => "No valid handlers found in this Nexus.",
            'return_to_list' => "← Return to Nexus List"

        ],
        'fa' => [
            'header' => "شبیه‌ساز ذهن نکسوس کروبات نسخه ۱۲.نهایی",
            'no_nexus' => "هیچ نکسوسی در نمونه کروبات ثبت نشده است.",
            'select_nexus' => "کدام نکسوس را می‌خواهید کاوش کنید؟",
            'select_nexus_hint' => "یک نکسوس را برای کالبدشکافی مسیرهای عصبی آن (روت‌ها) انتخاب کنید.",
            'exit' => "خروج از شبیه‌ساز",
            'inspecting_nexus' => "کاوش <fg={primary};options=bold>{shortName}</>. یک کنترل‌کننده را انتخاب کنید:",
            'back_to_list' => "← بازگشت به لیست نکسوس‌ها",
            'reflection_fail' => "تحلیل نکسوس '{className}' با خطا مواجه شد: {message}",
            'profile_for' => "پروفایل کامل مسیر برای: <fg={primary}>{shortName}::{methodName}</>",
            'sim_prompt' => "رفتار این مسیر را شبیه‌سازی می‌کنید؟",
            'sim_input_prompt' => "یک ورودی آزمایشی برای تطبیق با تریگر این مسیر وارد کنید:",
            'sim_input_placeholder' => "مثال: {placeholder}",
            'simulating' => "در حال شبیه‌سازی پاسخ کروبات...",
            'match_success' => "✅ تطبیق موفقیت‌آمیز بود!",
            'match_fail' => "❌ تطبیق ناموفق بود.",
            'match_reason' => "دلیل: ورودی ارائه شده '{input}' با الگوی تریگر '{pattern}' مطابقت ندارد.",
            'session_concluded' => "جلسه شبیه‌سازی پایان یافت.",

            'complex_mode_header' => "حالت: گزارش جامع (نمایش کامل مانیفست)",
            'nexus_manifest_header' => "مانیفست نکسوس: {shortName} ({nexusClass})",
            'complex_report_complete' => "تولید گزارش جامع با موفقیت انجام شد.",
            'could_not_analyze' => "امکان تحلیل نکسوس '{className}' وجود نداشت: {message}",
            'no_handlers_found' => "هیچ کنترل‌کننده معتبری در این نکسوس یافت نشد.",
            'return_to_list' => "← بازگشت به لیست نکسوس‌ها"

        ],
    ];
    
    /**
     * Dynamically loaded translations.
     * @var array
     */
    private array $lang = [];

    /**
     * Dynamically loaded color theme.
     * @var array
     */
    private array $theme = [];

    /**
     * Maps trigger attributes to their descriptive metadata.
     *
     * @var array<class-string, array{label: string, icon: string}>
     */
    private const TRIGGER_MAP = [
        OnCommand::class => ['label' => 'CMD', 'icon' => '⚡️', 'placeholder' => '/start'],
        OnText::class    => ['label' => 'TXT', 'icon' => '📄', 'placeholder' => 'Hello World'],
        OnRegEx::class   => ['label' => 'RGX', 'icon' => '🔬', 'placeholder' => 'user-123-status'],
        OnType::class    => ['label' => 'TYP', 'icon' => '📎', 'placeholder' => 'photo'],
        Action::class    => ['label' => 'ACT', 'icon' => '🔘', 'placeholder' => 'confirm_order'],
        WebApp::class    => ['label' => 'APP', 'icon' => '🌐', 'placeholder' => 'main'],
        WebPage::class   => ['label' => 'PAGE','icon' => '📄', 'placeholder' => '/dashboard'],
        WebAction::class => ['label' => 'W-ACT','icon'=> '🔌', 'placeholder' => '/api/submit'],
    ];

    /**
     * Execute the console command.
     * 
     * The traffic controller for routing command execution to the appropriate mode.
     * Summons the WarLord to grant operational powers to the handlers.
     */
    public function handle(#[WarLord] Krubot $miracler): int
    {

        // --- Advanced Traffic Controller with Alias Support ---
        // --- Routes the Flow to the correct handler based on mode ---

        // --- Phase 1: Discern the True Intent ---
        // We establish a clear hierarchy to determine the operational mode.
        // Direct, specific flags (-l, -p, -s) are considered a higher-order command,
        // overriding the more general --mode option. This ensures user intent is precisely manifested.
        $mode = strtolower((string) $this->option('mode'));

        if ($this->option('live')) {
            $mode = 'interactive';
        } elseif ($this->option('pro')) {
            $mode = 'pro';
        } elseif ($this->option('simple')) {
            $mode = 'simple';
        }
        // If no shortcut flags are present, $mode retains its value from --mode.

        // --- Phase 2: Channel the Execution Flow ---
        // With the true mode now manifest, we route the cosmic energy
        // to the appropriate handler. The WarLord grants the power.

        switch ($mode) {
            case 'basic':
            case 'legacy':
            case 'simple':
                return $this->handleSimpleMode($miracler);
            
            case 'live':
            case 'advanced':
            case 'interactive':
                // The full interactive experience
                return $this->handleInteractiveMode($miracler);
            
            case 'pro':
            case 'complex':
            case 'detail':
            case 'details':
            default: // 'pro' is our default and ultimate fallback
                    // We'll pass control to our new handler for complex mode
                    return $this->handleComplexModeTEST($miracler);
        }
    }

    /**
     * Handles the "simple" mode execution.
     * Provides a quick, non-interactive table view of all registered Nexuses.
     *
     * @param Krubot $miracler The main Krubot application instance.
     * @return int The command exit code.
    */
    private function handleSimpleMode(#[WarLord] Krubot $miracler): int
    {
        $this->info("🚀 KrubiK Nexus Inspector [Simple Mode]");

        $nexuses = spin(
            fn() => $miracler->getIntegratedNexuses(),
            'Scanning for registered Nexuses...'
        );

        if (empty($nexuses)) {
            $this->warn("No Nexuses are registered in the Krubot instance.");
            return self::SUCCESS;
        }

        $this->comment("Found " . count($nexuses) . " registered Nexuses:");

        $tableData = collect($nexuses)->map(function ($nexus, $index) {
            return ['#' => $index + 1, 'Nexus Class' => $nexus];
        })->toArray();

        $this->table(
            ['#', 'Nexus Class'],
            $tableData
        );
        
        $this->info("Inspection complete.");

        return self::SUCCESS;
    }

    /**
     * Handles the 'live' or 'advanced' or 'interactive' mode execution.
     * 
     * The full interactive Krubot-Simulation mode.
     */
    private function handleInteractiveMode(#[WarLord] Krubot $miracler): int
    {
        $this->initializeDisplay();
        $this->displayHeader();

        $nexuses = $miracler->getIntegratedNexuses();
        if (empty($nexuses)) {
            warning($this->trans('no_nexus'));
            return self::SUCCESS;
        }

        while (true) {
            $nexusOptions = $this->buildNexusSummaries($nexuses);
            $nexusOptions['exit'] = $this->trans('exit');

            $selectedNexus = select(
                label: $this->trans('select_nexus'),
                options: $nexusOptions,
                scroll: 10,
                hint: $this->trans('select_nexus_hint')
            );

            if ($selectedNexus === 'exit') break;
            
            $this->inspectNexus($selectedNexus);
        }

        outro($this->trans('session_concluded'));
        return self::SUCCESS;
    }
    
    /**
     * Handles the 'complex' or 'details' or 'pro' mode execution.
     * 
     * Dumps a detailed, non-interactive report of all routes for all Nexuses.
     */
    private function handleComplexMode(#[WarLord] Krubot $miracler): int
    {
        // Initialize display context (theme and language). This is the source of power.
        $this->initializeDisplay(); 
        $this->displayHeader();
        
        // Extract theme colors into tactical, single-letter variables for code clarity and brevity.
        $h = $this->theme['highlight']; // Highlight color, for headers and important elements.
        $s = $this->theme['source'];    // Source/Secondary color, for separators and subtle info.
    
        $this->comment($this->trans('complex_mode_header'));
    
        $nexuses = $miracler->getIntegratedNexuses();
        if (empty($nexuses)) {
            warning($this->trans('no_nexus'));
            return self::SUCCESS;
        }
    
        foreach ($nexuses as $nexusClass) {
            try {
                $shortName = (new ReflectionClass($nexusClass))->getShortName();
                
                // Render the main header using the dynamic 'highlight' theme color.
                $headerTitle = $this->trans('nexus_manifest_header', ['shortName' => $shortName, 'nexusClass' => $nexusClass]);
                $this->line("\n<fg={$h};options=bold>==================================================</>");
                $this->line("<fg={$h};options=bold>  {$headerTitle}</>");
                $this->line("<fg={$h};options=bold>==================================================</>");
    
                $manifest = $this->generateNexusManifest($nexusClass);
    
                // The dispatching is now handled by the themed displayManifest method.
                $this->displayManifest($manifest, $nexusClass);
    
            } catch (ReflectionException $e) {
                warning($this->trans('reflection_fail', ['className' => $nexusClass, 'message' => $e->getMessage()]));
            }
        }
        
        outro($this->trans('complex_report_complete'));
        return self::SUCCESS;
    }

    /**
     * Output Strategy Dispatcher.
     * This method either delegates to the command's standard, formatted output methods
     * or steps aside completely to let the Oracle speak directly to the console.
     *
     * @param array $manifest The generated data manifest for a Nexus.
     * @param string $nexusClass The fully qualified class name of the Nexus.
    */
    private function displayManifest(array $manifest, string $nexusClass): void
    {
        // Extract the 'source' color for the separator.
        $s = $this->theme['source'];

        // The check for 'reveal' automatically handles all its aliases ('oracle', 'symfony', 'dump').
        if ($this->option('reveal')) {
            // Stand aside and let the Oracle unleash the truth. No theme needed for raw data.
            self::unleashRevelation($manifest);
        } else {
            // Standard Mode: Present a curated, human-friendly profile.
            if (empty($manifest['methods'])) {
                $this->warn("  {$this->trans('no_handlers_found')}");
                return;
            }

            foreach ($manifest['methods'] as $handler) {
                // Delegate the complex display to the dedicated profile renderer.
                $this->displayRouteProfile($handler, $nexusClass, $manifest['class_level']);
                // The separator line is now themed.
                $this->line("  <fg={$s}>------------------------------------------------</>");
            }
        }
    }

    /**
     * The Architect's Oracle, Unleashed.
     * This is a direct-action weapon. It forges a conduit from the variable's core
     * straight to the standard output. There is no buffer, no capture, no delay—only
     * pure, immediate revelation. This is the epitome of efficiency for console debugging.
     *
     * @internal This method is a fire-and-forget tool. It terminates by printing directly
     * to the console stream, returning nothing.
     *
     * @param mixed $variable The entity to be instantly revealed.
     * @return void
     */
    public static function unleashRevelation(mixed $variable): void
    {
        // PHASE 1: Introspection.
        // A safe, immutable snapshot of the variable's state is generated.
        $cloner = new VarCloner();
        $clonedData = $cloner->cloneVar($variable);

        // PHASE 2: Direct Revelation.
        // A CliDumper is instantiated without a target stream, forcing its output
        // directly to STDOUT (the console). The prophecy is spoken instantly.
        (new CliDumper())->dump($clonedData);
    }

    /***{
        // Use the same initialization for consistent theming and language.
        $this->initializeDisplay(); 
        $this->displayHeader();
        
        $this->comment("Mode: Complex Report (Full Manifest Dump)");

        $nexuses = $miracler->getIntegratedNexuses();
        if (empty($nexuses)) {
            warning($this->trans('no_nexus'));
            return self::SUCCESS;
        }

        foreach ($nexuses as $nexusClass) {
            try {
                $shortName = (new ReflectionClass($nexusClass))->getShortName();
                $this->line("\n<fg=yellow;options=bold>==================================================</>");
                $this->line("<fg=yellow;options=bold>  NEXUS MANIFEST: {$shortName} ({$nexusClass})</>");
                $this->line("<fg=yellow;options=bold>==================================================</>");

                $manifest = $this->generateNexusManifest($nexusClass);

                if (empty($manifest['methods'])) {
                    $this->warn("  No valid handlers found in this Nexus.");
                    continue;
                }
                
                // Display each route's profile sequentially.
                foreach ($manifest['methods'] as $handler) {
                    $this->displayRouteProfile($handler, $nexusClass, $manifest['class_level']);
                    $this->line('  ------------------------------------------------'); // Separator
                }

            } catch (ReflectionException $e) {
                warning($this->trans('reflection_fail', ['className' => $nexusClass, 'message' => $e->getMessage()]));
            }
        }
        
        outro("Complex report generation complete.");
        return self::SUCCESS;
    }*** /

    / **
     * The main inspection logic for a single Nexus class.
     *
     * @param string $className
     * /
    private function inspectNexusComplexVeryyyyOld(string $className): void
    {
        while (true) {
            try {
                $manifest = $this->generateNexusManifest($className);
                $shortName = (new \ReflectionClass($className))->getShortName();

                $handlerOptions = [];
                foreach ($manifest['methods'] as $methodName => $handler) {
                    $handlerOptions[$methodName] = "{$handler['trigger_icon']} <fg=white>{$methodName}</> <fg=gray>[{$handler['trigger_label']}] -> {$handler['trigger_pattern']}</>";
                }
                $handlerOptions['back'] = '← Return to Nexus List';

                $selectedMethod = select(
                    label: "Inspecting <fg=cyan;options=bold>{$shortName}</>. Select a handler:",
                    options: $handlerOptions,
                    scroll: 15
                );

                if ($selectedMethod === 'back') {
                    return;
                }

                $this->displayRouteProfile($manifest['methods'][$selectedMethod], $className, $manifest['class_level']);

            } catch (ReflectionException $e) {
                warning("Could not analyze Nexus '{$className}': {$e->getMessage()}");
                return;
            }
        }
    }

    /**
     * Handles the 'complex' or 'pro' execution mode.
     * Dumps a complete, non-interactive, deep structural analysis of all found nexuses.
     * This mode summons the Amethyst Oracle to reveal the full manifest.
     * /
    private function handleComplexModeAmethyst(#[WarLord] Krubot $krubot): int
    {
        $this->initializeDisplay(); // Initializes language and theme
        $this->displayHeader();

        $this->info($this->trans('messages.complex_mode.intro'));

        $manifest = spin(
            fn() => $this->generateNexusManifest($krubot),
            $this->trans('messages.spinner.generating_manifest')
        );

        if (empty($manifest)) {
            $this->warn($this->trans('messages.no_nexus_found'));
            return self::SUCCESS;
        }

        $this->newLine();
        $this->line($this->theme['headline']('🔮 Amethyst Revelation of the Nexus Manifest 🔮'));
        $this->newLine();

        //
        // 😈 ===== THIS IS THE ENGAGEMENT YOU COMMANDED ===== 😈
        //
        // Instead of a simple loop or table, we summon the oracle.
        // We ask the Queen to cast a crystal-clear revelation upon the entire manifest structure.
        $revelation = Sorcery::amethystRevelation($manifest);

        // Display the sacred revelation directly to the console.
        // The output is already beautifully formatted by the Symfony VarDumper engine.
        $this->line($revelation);

        $this->info($this->trans('messages.complex_mode.outro'));

        return self::SUCCESS;
    } **** */

    /**
     * The main interactive inspection logic for a single Nexus class.
    */
    private function inspectNexusComplex(string $className): void
    {
        // Extract theme colors into tactical variables.
        $p = $this->theme['primary'];   // Primary color for main entities.
        $cl = $this->theme['class'];    // Specific color for class/method names.
        $s = $this->theme['source'];    // Secondary color for auxiliary text.
        
        while (true) {
            try {
                $manifest = $this->generateNexusManifest($className);
                $shortName = (new \ReflectionClass($className))->getShortName();

                $handlerOptions = [];
                foreach ($manifest['methods'] as $methodName => $handler) {
                    // Build each option line with themed components.
                    $handlerOptions[$methodName] = "{$handler['trigger_icon']} <fg={$cl}>{$methodName}</> <fg={$s}>[{$handler['trigger_label']}] -> {$handler['trigger_pattern']}</>";
                }
                $handlerOptions['back'] = $this->trans('return_to_list');

                // The select prompt label is now themed and translated.
                $selectedMethod = select(
                    label: $this->trans('inspecting_nexus', ['shortName' => "<fg={$p};options=bold>{$shortName}</>"]),
                    options: $handlerOptions,
                    scroll: 15
                );

                if ($selectedMethod === 'back') {
                    return;
                }

                $this->displayRouteProfile($manifest['methods'][$selectedMethod], $className, $manifest['class_level']);

            } catch (ReflectionException $e) {
                warning($this->trans('could_not_analyze', ['className' => $className, 'message' => $e->getMessage()]));
                return;
            }
        }
    }


    /**
     * The main inspection and simulation loop for a single Nexus.
     */
    private function inspectNexus(string $className): void
    {
        while (true) {
            try {
                $manifest = spin(
                    fn() => $this->generateNexusManifest($className),
                    'Dissecting Nexus neural pathways...'
                );
                
                $shortName = (new ReflectionClass($className))->getShortName();

                $handlerOptions = [];
                foreach ($manifest['methods'] as $methodName => $handler) {
                    $handlerOptions[$methodName] = "{$handler['trigger_icon']} <fg=white>{$methodName}</> <fg=gray>[{$handler['trigger_label']}] -> {$handler['trigger_pattern']}</>";
                }
                $handlerOptions['back'] = $this->trans('back_to_list');

                $selectedMethod = select(
                    label: $this->trans('inspecting_nexus', ['shortName' => $shortName]),
                    options: $handlerOptions,
                    scroll: 15
                );

                if ($selectedMethod === 'back') return;

                $handlerProfile = $manifest['methods'][$selectedMethod];
                $this->displayRouteProfile($handlerProfile, $className, $manifest['class_level']);
                
                // --- THE SIMULATION ENGINE ---
                if (confirm($this->trans('sim_prompt'))) {
                    $this->runSimulation($handlerProfile);
                }

            } catch (ReflectionException $e) {
                warning($this->trans('reflection_fail', ['className' => $className, 'message' => $e->getMessage()]));
                return;
            }
        }
    }
    
    /**
     * Runs the simulation for a given handler's trigger.
     */
    private function runSimulation(array $handler): void
    {
        $input = text(
            label: $this->trans('sim_input_prompt'),
            placeholder: $this->trans('sim_input_placeholder', ['placeholder' => $handler['trigger_placeholder']]),
            required: true
        );

        $result = spin(function () use ($handler, $input) {
            sleep(1); // Simulate processing latency for better UX
            $pattern = $handler['trigger_pattern'];
            
            // Mimic Krubot's `processUpdate` matching logic
            switch ($handler['trigger_class_short']) {
                case 'OnCommand':
                case 'OnText':
                case 'Action':
                case 'WebApp':
                case 'WebPage':
                case 'WebAction':
                case 'OnType':
                    return strtolower($input) === strtolower($pattern);
                case 'OnRegEx':
                    // Auto-wrap pattern if it's not already delimited
                    if (!preg_match('/^\/.*\/[a-zA-Z]*$/', $pattern)) {
                        $pattern = '/' . $pattern . '/';
                    }
                    return (bool) preg_match($pattern, $input);
                default:
                    return false;
            }
        }, $this->trans('simulating'));

        if ($result) {
            info($this->trans('match_success'));
        } else {
            warning($this->trans('match_fail'));
            note($this->trans('match_reason', ['input' => $input, 'pattern' => $handler['trigger_pattern']]));
        }
    }

    // ... (All other methods like displayRouteProfile, generateNexusManifest, etc., remain largely the same) ...
    // ... (We will only add the initialization and translation methods here for brevity) ...

    /**
     * Initializes language and theme settings based on command options.
     */
    private function initializeDisplay(): void
    {
        $langCode = $this->option('lang') === 'fa' ? 'fa' : 'en';
        $this->lang = self::I18N[$langCode];

        $lightTheme = [
            'primary' => 'blue',
            'secondary' => 'dark-gray',
            'highlight' => 'magenta',
            'source' => 'gray',
            'success' => 'green',
            'class' => 'gray',
            'method' => 'gray',
        ];
        $darkTheme = [
            'primary' => 'cyan',
            'secondary' => 'gray',
            'highlight' => 'yellow',
            'source' => 'gray',
            'success' => 'green',
            'class' => 'gray',
            'method' => 'gray',
        ];
        $this->theme = $this->option('theme') === 'light' ? $lightTheme : $darkTheme;
    }

    /**
     * Gets a translation string, replacing placeholders and colors.
     */
    private function trans(string $key, array $replacements = []): string
    {
        $string = $this->lang[$key] ?? $key;
        $replacements = array_merge($this->theme, $replacements);
        
        foreach ($replacements as $placeholder => $value) {
            $string = str_replace("{{$placeholder}}", $value, $string);
            $string = str_replace("{".$placeholder."}", $value, $string);
        }
        return $string;
    }
    
    /**
     * Displays the command header with dynamic theming.
     */
    private function displayHeader(): void
    {
        $color = $this->theme['primary'];
        $header = <<<EOT
<fg={$color};options=bold>
  _   _ _   _ _   _ _     _   _           ___           _          _
 | \ | | | | | \ | (_) __| | | |_ ___    / _ \ _ __ ___| |__   ___| |___
 |  \| | | | |  \| | |/ _` | | __/ _ \  | | | | '__/ __| '_ \ / _ \ / __|
 | |\  | |_| | |\  | | (_| | | || (_) | | |_| | | | (__| | | |  __/ \__ \
 |_| \_|\___/|_| \_|_|\__,_|  \__\___/   \___/|_|  \___|_| |_|\___|_|___/
</>
EOT;
        $this->line($header);
        info($this->trans('header'));
    }

    /**
     * Creates rich, summary-based options for the Nexus selection prompt.
     *
     * @param array<string> $nexuses
     * @return array<string, string>
     */
    private function buildNexusSummaries(array $nexuses): array
    {
        $summaries = [];
        foreach ($nexuses as $nexusClass) {
            try {
                $manifest = $this->generateNexusManifest($nexusClass);
                $handlerCount = count($manifest['methods']);
                $typeCounts = array_count_values(array_column($manifest['methods'], 'trigger_label'));
                
                $countsStr = collect($typeCounts)->map(fn($count, $label) => "{$count} {$label}")->implode(', ');
                $shortName = (new ReflectionClass($nexusClass))->getShortName();

                $summaries[$nexusClass] = "🚀 <fg=white>{$shortName}</> ({$handlerCount} handlers: {$countsStr})";
            } catch (ReflectionException $e) {
                $summaries[$nexusClass] = "⚠️ {$nexusClass} (Reflection Failed)";
            }
        }
        return $summaries;
    }

    /**
     * Displays the full, computed profile of a single route/handler.
     */
    private function displayRouteProfile(array $handler, string $className, array $classLevel): void
    {
        $shortName = (new ReflectionClass($className))->getShortName();
        note($this->trans('profile_for', ['shortName' => $shortName, 'methodName' => $handler['method_name']]));

        $finalMiddlewares = array_merge($classLevel['middlewares'], $handler['middlewares']);
        $finalPlatforms = array_unique(array_merge($classLevel['platforms'], $handler['platforms']));
        
        $p = $this->theme['primary'];
        $h = $this->theme['highlight'];
        $s = $this->theme['source'];
        $sc = $this->theme['success'];
        $cl = $this->theme['class'];
        $mt = $this->theme['method'];

        // --- Trigger & Handler ---
        $this->line("  <fg={$p}>Trigger:</>     {$handler['trigger_icon']} <fg={$h};options=bold>{$handler['trigger_pattern']}</> ({$handler['trigger_class_short']})");
        $this->line("  <fg={$p}>Handler:</>     <fg=white>{$className}::{$handler['method_name']}()</>");
        if ($handler['name']) {
            $this->line("  <fg={$p}>Route Name:</>  <fg=magenta>{$handler['name']}</>");
        }
        if ($handler['source_location']) {
            $this->line("  <fg={$p}>Source:</>      <fg={$s}>{$handler['source_location']}</>");
        }
        
        // --- Middleware Stack ---
        $this->line("\n  <fg={$p}>⚙️ Middleware Stack (Computed):</>");
        if (empty($finalMiddlewares)) {
            $this->line("    <fg={$s}>None</>");
        } else {
            foreach ($classLevel['middlewares'] as $mw) $this->line("    <fg={$sc}>✓</> {$mw} <fg={$cl}>[Class-Level]</>");
            foreach ($handler['middlewares'] as $mw) $this->line("    <fg={$sc}>✓</> {$mw} <fg={$mt}>[Method-Level]</>");
        }

        // --- Platform Restrictions ---
        $this->line("\n  <fg={$p}>🛡️ Platform Restrictions (Computed):</>");
        if (empty($finalPlatforms)) {
            $this->line("    <fg={$sc}>All Platforms (Universal Access)</>");
        } else {
            foreach ($finalPlatforms as $platform) $this->line("    <fg={$p}>✓</> {$platform}");
        }
        
        $this->line(""); // Spacing
    }

    /**
     * A self-contained reflection engine that rebuilds the Nexus manifest.
     * This mimics the core logic of `integrateNexus` for inspection purposes.
     *
     * @return array<string, mixed>
     * @throws ReflectionException
     */
    private function generateNexusManifest(string $className): array
    {
        $reflection = new ReflectionClass($className);
        if (!$reflection->isInstantiable()) {
            throw new ReflectionException("Nexus class {$className} is not instantiable.");
        }

        $manifest = ['class_level' => [], 'methods' => []];

        // --- Phase A: Analyze Class-Level Attributes ---
        $classMiddlewares = [];
        foreach ($reflection->getAttributes(Middleware::class) as $attr) {
            $classMiddlewares = array_merge($classMiddlewares, $attr->newInstance()->middlewares);
        }
        $classPlatforms = [];
        foreach ($reflection->getAttributes(RestrictTo::class) as $attr) {
            $classPlatforms = array_merge($classPlatforms, $attr->newInstance()->getResolvedPlatforms());
        }

        /// $nexusNamePrefix = ($attr = $reflection->getAttributes(Name::class)[0] ?? null) ? $attr->newInstance()->name : null;
        $nameAttrs = $reflection->getAttributes(Name::class);
        $attr = $nameAttrs[0] ?? null;
        $nexusNamePrefix = $attr ? $attr->newInstance()->name : null;
        
        $manifest['class_level'] = [
            'middlewares' => $classMiddlewares,
            'platforms' => array_unique($classPlatforms),
            'name_prefix' => $nexusNamePrefix,
        ];
        
        // --- Phase B: Analyze Method-Level Attributes ---
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $triggerAttr = $this->findTriggerAttribute($method);
            if (!$triggerAttr) // Not a handler method
                continue;
            
            // Extract Trigger Info
            $triggerInstance = $triggerAttr->newInstance();
            $triggerClass = $triggerAttr->getName();
            $handlerData = [
                'method_name' => $method->getName(),
                'source_location' => str_replace(base_path(), '', $method->getFileName()) . ':' . $method->getStartLine(),
                'trigger_class_short' => (new ReflectionClass($triggerClass))->getShortName(),
                'trigger_label' => self::TRIGGER_MAP[$triggerClass]['label'] ?? 'N/A',
                'trigger_icon' => self::TRIGGER_MAP[$triggerClass]['icon'] ?? '❓',
                'trigger_placeholder' => self::TRIGGER_MAP[$triggerClass]['placeholder'] ?? 'test_input',
            ];
            $handlerData['trigger_pattern'] = match($triggerClass) {
                OnCommand::class => $triggerInstance->command,
                OnText::class, OnRegEx::class => $triggerInstance->pattern,
                OnType::class => is_array($triggerInstance->type) ? implode('|', $triggerInstance->type) : $triggerInstance->type,
                Action::class => $triggerInstance->name,
                WebApp::class, WebPage::class, WebAction::class => $this->formatWebTrigger($triggerInstance),
                default => 'Unknown'
            };
            
            // Extract Method-Specific Metadata
            $methodMiddlewares = [];
            foreach ($method->getAttributes(Middleware::class) as $attr) {
                $methodMiddlewares = array_merge($methodMiddlewares, $attr->newInstance()->middlewares);
            }
            $methodPlatforms = [];
            foreach ($method->getAttributes(RestrictTo::class) as $attr) {
                $methodPlatforms = array_merge($methodPlatforms, $attr->newInstance()->getResolvedPlatforms());
            }

            /// $routeName = ($attr = $method->getAttributes(Name::class)[0] ?? null) ? $attr->newInstance()->name : null;
            $methodNameAttrs = $method->getAttributes(Name::class);
            $methodAttr = $methodNameAttrs[0] ?? null;
            $routeName = $methodAttr ? $methodAttr->newInstance()->name : null;

            if ($routeName && str_starts_with($routeName, '.')) {
                $routeName = $nexusNamePrefix . substr($routeName, 1);
            }
            
            $handlerData['middlewares'] = $methodMiddlewares;
            $handlerData['platforms'] = array_unique($methodPlatforms);
            $handlerData['name'] = $routeName;
            
            $manifest['methods'][$method->getName()] = $handlerData;
        }

        return $manifest;
    }

    /**
     * Finds the first valid trigger attribute on a method.
     * 
     * @return \ReflectionAttribute|null
     */
    private function findTriggerAttribute(ReflectionMethod $method): ?\ReflectionAttribute
    {
        foreach (array_keys(self::TRIGGER_MAP) as $attributeClass) {
            $attributes = $method->getAttributes($attributeClass);
            if (!empty($attributes)) return $attributes[0];
        }
        return null;
    }

    /**
     * Formats a descriptive string for web-based triggers.
     */
    private function formatWebTrigger(object $instance): string
    {
        $methods = implode('|', $instance->methods ?? ['ANY']);
        $path = $instance->path ?? $instance->name;
        return "[{$methods}] {$path}";
    }
}
