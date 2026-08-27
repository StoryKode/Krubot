<?php

namespace KrubiK\Console;
/*
| Krubot BotEngine: The Architect's Lexicon [×vRC.8×] 🚀📜
|--------------------------------------------------------------------------
| This is **a Playground For Mastery**, a laboratory of ***Software Dev Artistry***;
| not a weapon for production's final battles.
|
| Our Bond: ***"Rebuilding The Rebellion"*** Within S.N.P. (The Foundation of Pure Power & Revel).
| Your Mandate [MIT]: Deconstruct Krubot. Command it. Master it. You are The Architect Now!
|
| *Go build something revolutionary!* 💜⚡️
*/

// PnP in PHP ?! Yeah!
// Bringing Pain_&_Pleasure Concept from Neuro-Linguistic Programming, transfered to PHP.

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

/**
* @author DoKtor K.
* @link https://StoryKo.de/Krubot Official website of engine.
* @version Krubot: ×RC.8×
* @license MIT
*/
class MakeNexusCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
    */
    protected $name = 'krubik:nexus-make';

    /**
     * The console command description.
     *
     * @var string
    */
    protected $description; // We will set this in the constructor

    /**
     * The type of class being generated.
     *
     * @var string
    */
    protected $type = 'Nexus';

    /**
     * Create a new command instance.
     *
     * @return void
    */
    public function __construct()
    {
        // Call parent constructor
        parent::__construct();

        // Set the description using the translation helper
        $this->description = __('krubot::commands.nexus_make_description');
    }

    /**
     * Override the default handle method to inject the Pleasure & Pain principle.
     * This turns a simple file creation into an act of guided creation.
     */
    public function handle()
    {
        // --- The Pleasure Principle: Empowering The Architect ---
        $this->line("\n<fg=magenta>" . __('krubot::commands.nexus_forging') . "</>");

        // Execute the parent's logic to get the job done first.
        // This ensures we celebrate a REAL success.
        parent::handle();
        
        // This is a failsafe. handle() in the parent might return false on error.
        if (false === parent::handle()) {
             return 1; // self::FAILURE // Signal failure
        }
        
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);

        // Load structured messages with dynamic parameters while preserving original styles/emojis
        $this->info(__('krubot::commands.nexus_forged_success', [
            'type' => $this->type,
            'path' => $path
        ]));
        $this->line("<fg=cyan>" . __('krubot::commands.nexus_core_sound') . "</>");

        // --- The Pain Principle: The Fear of an Incomplete Creation ---
        // We create a gentle, guiding pain: the awareness that the Nexus is currently just an empty shell.
        $this->comment("\n" . __('krubot::commands.nexus_reminder_header'));
        $this->comment(__('krubot::commands.nexus_reminder_body'));

        // --- The Path to Pleasure: Offering The Solution ---
        // We immediately offer the cure for the pain we just introduced.
        $className = class_basename($name);
        $handlerName = str_replace('Nexus', 'Handler', $className);

        if ($this->option('handler')) {
            $this->call('krubik:handler-make', ['name' => $handlerName]);
        }
        else if ($this->confirm(("\n<fg=yellow>" . __('krubot::commands.nexus_handler_prompt', ['handlerName' => $handlerName]) . "</>"), true)) {
            $this->call('krubik:handler-make', ['name' => $handlerName]);
        } else {
            // Acknowledging their choice, but reinforcing the 'pain' one last time.
            $this->warn("\n" . __('krubot::commands.nexus_handler_rejected', ['className' => $className]));
        }
    }

    protected function getStub(): string
    {
        return __DIR__ . '/Stubs/nexus.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        // Place new Nexuses in the path defined in the config file.
        // We'll default to 'App\Nexus' if not specified.
        $path = config('krubot.discovery.path');
        
        // If multiple paths, use the first one.
        if (is_array($path)) {
            $path = $path[0] ?? app_path('Nexus');
        }

        // Convert file path to namespace.
        return Str::of($path)
            ->after(app_path())
            ->replace('/', '\\')
            ->prepend($rootNamespace . '\\')
            ->trim('\\');
    }

    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);

        $className = class_basename($name);
        $nameSnake = Str::snake(str_replace('Nexus', '', $className));

        return str_replace(['{{ name_snake }}'], [$nameSnake], $stub);
    }
    
    /**
     * Add a '--handler' option for power users.
     */
    protected function getOptions()
    {
        $options = parent::getOptions();
        $options[] = ['handler', 'H', InputOption::VALUE_NONE, __('krubot::commands.nexus_option_handler')];
        return $options;
    }
}
