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

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process as LaravelProcess;
use Symfony\Component\Process\Process as SymfonyProcess;
use Illuminate\Support\Facades\File; // For .env file manipulation

/**
 * KrubiK::Ascend(▲Ѧ▲) DivineCommmand ∷Ѫ∷ **⟬>⎇⌘ѪɅ⟭** ∷Ѫ∷ ⟨`krubik:ascend`()⟩
 * Class KrubotInstaller
 * @package KrubiK\Console
 *
 * This command orchestrates the full installation process for Krubot,
 * consolidating multiple steps into a single, user-friendly command.
 * It ensures cross-platform compatibility and real-time output streaming.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
 */
class KrubotInstaller extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'krubik:ascend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Provision a full-power, optimized KrubiK Cyber-Citadel with One magical cross-platform command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        // Define the sequence of commands to be executed.
        // Each array represents a command and its arguments.
        $commands = [
            ['php', 'artisan', 'vendor:publish', '--provider=KrubiK\Providers\KrubotServiceProvider'],
            [-1, 'injectEnv', 'Injecting Environmental Matrix into project .env'], // 💉 Interleaved Internal Protocol, must return bool
            ['php', 'artisan', 'krubik:make-migrations'],
            ['php', 'artisan', 'migrate', '--force'], // --force is crucial in production to avoid confirmation prompts
            ['composer', 'dump-autoload', '-o'],
            ['php', 'artisan', 'optimize:clear'],
        ];

        $this->components->info('Initiating KrubiK Ascension Sequence...');

        // Iterate through each command and execute it.
        foreach ($commands as $cmd) {

            if($cmd[0] === -1) {
                // Handle Internal Directives (The -1 Flag) ⚡
                $this->info("Running: K-Installer::<fg=lime>{$cmd[1]}()</>");
                $this->components->task($cmd[2], [$this, $cmd[1]]);
                continue; // Move to the next command safely
            }

            $commandString = implode(' ', $cmd);
            $this->info("Running: <fg=cyan>{$commandString}</>"); // Display command being run

            // Create a new SymfonyProcess for the current command.
            $process = new SymfonyProcess($cmd);
            $process->setTimeout(300); // Set a generous timeout for potentially long-running commands (e.g., composer require)
            $process->setIdleTimeout(60); // Timeout for inactivity

            // Run the process and stream its output live to the console.
            $process->run(function ($type, $buffer) {
                // Determine if the buffer is stdout or stderr.
                // We'll write it directly to the console output.
                $this->output->write($buffer);
            });

            // Check if the command was successful.
            if (!$process->isSuccessful()) {
                $this->error("Command execution failed: <fg=red>{$commandString}</>");
                $this->error("Diagnostic error stream:\n" . $process->getErrorOutput()); // Display stderr output for debugging and failure tracing
                return Command::FAILURE; // Indicate command failure
            }
        }

        // 4. The Final Spark: Awaken the Ultimate Daemon
        $this->daemonizeLazarus();

        // The final command, "resurrecting the polling daemon," is called separately
        // as it's an Artisan command and can be handled by the current Artisan context.
        /*
        $this->info("Calling the mystical Lazarus...");
        $this->callSilent('krubik:lazarus', [
            '--stealth' => true,
        ]);
        */

        $this->components->info('✨ Ascension Complete. KrubiK is now online.');
        $this->info('✨🔮 Krubot installation has been completed successfully with divine flow and full system harmony. Ready to serve... 🚀');
        return Command::SUCCESS; // Indicate command success
    }

    public function injectEnv(): bool
    {
        $envPath = base_path('.env');

        // Check if the .env file exists
        if (!File::exists($envPath)) {
            $this->error('The .env file does not exist. Please create one before injecting Krubot variables.');
            return false;
        }

        $content = File::get($envPath);

        // The core variables to be injected into the .env file.
        // These variables represent the "system's consciousness".
        $newVariables = <<<ENVPLUS

# --- KRUBOT TITANIUM CORE ---
KRUBOT_DRIVER=rubika

RUBIKA_BOT_TOKEN=your_rubika_token_here
RUBIKA_BOT_SALT=KrubiKSalT
BALE_BOT_TOKEN=your_bale_token_here
TELEGRAM_BOT_TOKEN=your_telegram_token_here

# --- THE DISCOVERY ENGINE ---
KRUBOT_NEXUS_DISCOVERY=true
KRUBOT_NEXUS_CACHE=false

# --- ALLOW KRUBOT LISTENING BOT WEBHOOKS ---
KRUBOT_ROUTING_ENABLED=true

# --- THE LAZARUS PROTOCOL (POLLING) ---
KRUBOT_POLLING_ENABLED=true
KRUBOT_LAZARUS_ENABLED=true
KRUBOT_LAZARUS_INTERVAL=3000

# --- THE CONVERSATION ENGINE ---
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_DRIVER=database
CACHE_STORE=database

# --- AMETHYST MATRIX (THE ORACLE) ---
AMETHYST_LOGGING_ENABLED=true
AMETHYST_LOG_CHANNEL=stack
BROADCAST_CONNECTION=log

# --- MISC SETTINGS (OPTIONAL) ---
APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

ENVPLUS;

        // Check if Krubot variables already exist to prevent duplicate injection
        if (str_contains($content, 'KRUBOT_DRIVER') && !$this->option('force-env')) {
            $this->warn('Krubot variables already exist in .env. Use --force-env to re-inject them.');
            return true; // Indicate successful execution, but no changes made
        }

        try {
            // Append the new variables to the .env file
            File::append($envPath, PHP_EOL . $newVariables . PHP_EOL); // Add new lines for clean separation.

            $this->info('Krubot variables injected successfully into the environmental matrix!');

            // Clear config cache to ensure new .env variables are loaded immediately
            $this->comment('Clearing configuration cache for immediate effect...');
            Artisan::call('config:clear');
            $this->info('Configuration cache cleared. The system has absorbed the new consciousness.');

            return true;
        } catch (\Exception $e) {
            $this->error('Failed to inject Krubot variables: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ⚡ Awaken the Lazarus Daemon in a fully detached background process, total cross-platform.
     * 
     * @return void
    */
    protected function daemonizeLazarus(): void
    {
        $this->info('Calling the mystical Lazarus...');

        $ok = $this->components->task('Awakening the Lazarus Daemon Protocol (Phantom Mode)', function (): bool {
            $php = PHP_BINARY;
            $artisan = base_path('artisan');

            // Build a safe argv command (no raw concatenation for primary path)
            $argv = [$php, $artisan, 'krubik:lazarus', '--stealth'];

            try {
                if ($this->isRunningOnWin()) {
                    // Windows detached launch via cmd.exe + start
                    // start "" /B "php.exe" "artisan" krubik:lazarus --stealth
                    $startCommand = 'start "" /B ' . $this->joinEscapedForWindows($argv);

                    if ($this->isFunctionInvokable('popen')) {
                        // popen() can be used for detached processes on Windows.
                        // cmd.exe /D /S /C is used to execute the command string reliably.

                        @pclose(
                            @popen(
                                'cmd /D /S /C ' . $this->quoteForCMD($startCommand),
                            'r')
                        );

                        return true;
                    }

                    // Fallback to Symfony Process for Windows.
                    // 'create_new_console' is crucial for true detachment and Async on Windows.
                    $launcher = new SymfonyProcess(
                        [
                            'cmd.exe',
                            '/D',
                            '/S',
                            '/C',
                            $startCommand,
                        ],
                        base_path()
                    );
                    $launcher->setTimeout(null);
                    $launcher->disableOutput();
                    $launcher->setOptions(['create_new_console' => true]); // Essential for detachment.
                    $launcher->start(); // Launches Asynchronously

                    return true;
                }

                // Unix/Linux/cPanel Detached Process (Fire & Forget)
                // launchs command plus: nohup + redirections + background output
                $shellCommand = 'nohup '
                    . $this->joinEscapedForPosix($argv)
                    . ' < /dev/null > /dev/null 2>&1 &';
                // It's our Ancient POSIX/UNIX magic for total detachment.

                // Fallback mechanism mirroring Lazarus's own survival instincts
                if ($this->isFunctionInvokable('exec')) {
                    // Native OS Level Detachment (Most aggressive and reliable for Daemons)
                    exec($shellCommand);
                    return true;
                } elseif ($this->isFunctionInvokable('shell_exec')) {
                    shell_exec($shellCommand);
                    return true;
                } elseif ($this->isFunctionInvokable('passthru')) {
                    passthru($shellCommand);
                    return true;
                } else {

                    // Modern Symfony & Laravel 12 Process Asynchronous Execution
                    // Modern fallback if shell functions are disabled by CageFS/hosting providers,
                    // we internally rely on Proc_Open via Symfony.
                    LaravelProcess::path(base_path())
                        ->forever() // ♾️ Grants immortal life without timeout (Internally sets timeout to null safely)

                        ->start($shellCommand); // Asynchronous genesis 🚀⚙️
                        // start() runs the process asynchronously and returns immediately.
                        // It does NOT block the thread like run() does.

                    // Option B (for Async spawn) ::
                    // LaravelProcess::path(base_path())->forever()->start(['sh', '-lc', $shellCommand]);

                    return true; // Task passed

                }
            } catch (\Throwable $e) {
                // If the universe denies the spark, we log it, but we do not crash the Ascension.
                Log::error('Failed to awaken Lazarus daemon.', [
                    'exception' => $e->getMessage(),
                ]);
                return false; // Task failed visually, but doesn't halt execution
            }
        });

        if (!$ok)
            $this->warn('Lazarus Daemon failed to spawn. Please start it Manually.');
    }

    /**
     * Check if current OS is Windows.
     */
    private function isRunningOnWin(): bool
    {
        return DIRECTORY_SEPARATOR === '\\' || (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    }

    /**
     * 🛡️ Utility Check: Ensures a native PHP function is actually usable.
     * Check if a native function exists and is not disabled in php.ini.
    */
    private function isFunctionInvokable(string $functionName): bool
    {
        if (! function_exists($functionName)) {
            return false;
        }

        $disabledStr = (string) ini_get('disable_functions');
        if ($disabledStr === '') {
            return true;
        }

        $disabledList = array_map(
            static fn (string $f) => strtolower(trim($f)),
            explode(',', $disabledStr)
        );

        return ! in_array(strtolower($functionName), $disabledList, true);
    }

    /**
     * Join arguments into a POSIX-shell-safe command line.
     * Each argument is escaped to prevent shell injection.
     *
     * @param array<int, string> $argv The arguments to join.
    */
    private function joinEscapedForPosix(array $argv): string
    {
        return implode(' ', array_map('escapeshellarg', $argv));
    }

    /**
     * Join trusted arguments for execution through Windows cmd.exe.
     * Each argument is quoted and internal quotes are escaped.
     *
     * @param array<int, string> $argv The arguments to join.
    */
    private function joinEscapedForWindows(array $argv): string
    {
        return implode(
            ' ',
            array_map(
                static function (string $argument): string {
                    // Values are internally generated, so we primarily focus on safe quoting for cmd.exe.
                    // Double quotes are escaped by doubling them for cmd.exe's quoted context.
                    return '"' . str_replace('"', '""', $argument) . '"';
                },
                $argv
            )
        );
    }

    /**
     * Quote a complete command string for safe execution by cmd.exe /S /C.
     * This ensures the entire command is treated as a single string by cmd.exe.
    */
    private function quoteForCMD(string $command): string
    {
        // Escape existing double quotes by doubling them, then wrap the entire command in quotes.
        return '"' . str_replace('"', '""', $command) . '"';
    }
}
