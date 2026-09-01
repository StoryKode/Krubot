<?php

namespace KrubiK\Drivers;

/*
|--------------------------------------------------------------------------
| Krubot CLIDriver — The Terminal Warlord 🖥️⚡️
|--------------------------------------------------------------------------
| Drop-in CLI Platform Driver for Krubot.
| Speaks STDIN/STDOUT. Breathes ANSI. Lives for artisan commands,
| developer testing, and LLM-powered automation pipelines.
|
| Implements MultiverseEnforcer + StandardDriverInterface
| Mirrors BaleDriver's architecture. No surprises. Pure power.
|
| Usage:
|   php artisan krubot:cli panel          → triggers #[OnCommand('panel')]
|   php artisan krubot:cli menu           → triggers #[OnCommand('menu')]
|   echo '{"action":"order","id":5}' | php artisan krubot:cli
|   php artisan krubot:cli --json='{"action":"buy_gem"}'
|--------------------------------------------------------------------------
*/

use KrubiK\Drivers\Contracts\MultiverseEnforcer;
use KrubiK\Drivers\Arcane\NeonVitality;

use KrubiK\Keyboard\Keyboard as KrubiKInlineKeyboard;
use KrubiK\Keyboard\ReplyKeyboard as KrubiKReplyKeyboard;

/**
 * CLIDriver — The Terminal Warlord.
 *
 * Turns artisan commands / STDIN into Krubot-compatible "updates",
 * and renders every reply as beautiful ANSI-colored terminal output.
 *
 * Designed for:
 *   - php artisan krubot:cli <command> [options]
 *   - Piped JSON payloads (LLM automation, CI pipelines)
 *   - Developer testing without a real Telegram/Bale bot
 *   - Scheduled CLI jobs using Krubot handlers
 */
class CLIDriver implements MultiverseEnforcer
{
    use NeonVitality;

    // ── ANSI color palette ───────────────────────────────────────────────────
    protected const ANSI = [
        'reset'   => "\033[0m",
        'bold'    => "\033[1m",
        'dim'     => "\033[2m",
        'italic'  => "\033[3m",
        'under'   => "\033[4m",

        // Foreground
        'black'   => "\033[30m",
        'red'     => "\033[31m",
        'green'   => "\033[32m",
        'yellow'  => "\033[33m",
        'blue'    => "\033[34m",
        'magenta' => "\033[35m",
        'cyan'    => "\033[36m",
        'white'   => "\033[37m",

        // Bright foreground
        'bred'    => "\033[91m",
        'bgreen'  => "\033[92m",
        'byellow' => "\033[93m",
        'bblue'   => "\033[94m",
        'bmagenta'=> "\033[95m",
        'bcyan'   => "\033[96m",
        'bwhite'  => "\033[97m",

        // Background
        'bg_black'  => "\033[40m",
        'bg_blue'   => "\033[44m",
        'bg_green'  => "\033[42m",
        'bg_red'    => "\033[41m",
        'bg_yellow' => "\033[43m",
        'bg_cyan'   => "\033[46m",
        'bg_white'  => "\033[47m",
    ];

    protected array $config;

    /**
     * The parsed incoming CLI "payload" (simulates a Telegram update).
     */
    protected array $payload = [];

    /**
     * ARGV-parsed options.
     */
    protected array $cliOptions = [];

    /**
     * The command name (e.g., 'panel', 'menu', 'order').
     */
    protected string $commandName = '';

    /**
     * Synthetic bot user pulled from config.
     */
    protected array $botUser = [];

    /**
     * Synthetic "sender" — the developer running the command.
     */
    protected array $senderUser = [];

    /**
     * Accumulated output lines (flushed at end or on demand).
     */
    protected array $outputBuffer = [];

    /**
     * Whether ANSI colors are supported in this terminal.
     */
    protected bool $ansiEnabled = true;

    /**
     * Terminal width (columns).
     */
    protected int $termWidth = 80;

    /**
     * Track interactive session state.
     */
    protected bool $interactiveMode = false;

    // =========================================================================
    // 🔌 BOOT
    // =========================================================================

    public function __construct(array $config)
    {
        $this->config = $config;

        $this->driverAlias = $config['alias'] ?? 'cli';

        $this->botUser = [
            'id'         => 0,
            'is_bot'     => true,
            'first_name' => $config['bot_name'] ?? 'KrubotCLI',
            'username'   => $config['bot_username'] ?? 'krubot_cli',
        ];

        // Detect terminal capabilities
        $this->ansiEnabled = $this->detectAnsiSupport();
        $this->termWidth   = $this->detectTerminalWidth();

        // Parse CLI arguments and STDIN
        $this->cliOptions = $this->parseArgv();
        $this->payload    = $this->resolveIncomingPayload();
        $this->senderUser = $this->resolveSenderUser();

        $this->commandName = $this->payload['command'] ?? $this->payload['text'] ?? '';

        $this->igniteNeon();

        // Print the Krubot CLI banner on boot
        $this->printBanner();
    }

    // =========================================================================
    // ⚡️ THE CORE — makeRequest (mirrors BaleDriver's contract)
    // =========================================================================

    /**
     * The universal dispatcher.
     *
     * In Telegram/Bale land → fires HTTP call.
     * In CLI land → renders beautiful terminal output.
     *
     * @param string $method  e.g. 'sendMessage', 'sendPhoto', 'answerCallbackQuery'
     * @param array  $params  The payload to dispatch
     * @return array          Normalized response (mirrors Telegram success shape)
     */
    public function makeRequest(string $method, array $params = []): array
    {
        $finalMethod = $method;
        $finalParams = $params;

        // ── RichMan handling (mirrors BaleDriver) ─────────────────────────
        if (isset($finalParams['text']) && $finalParams['text'] instanceof \KrubiK\RichMan) {
            $richMan = $finalParams['text'];
            $finalParams['text']  = $richMan->toText(); // CLI = plain text
            $finalParams['_rich'] = true;
            unset($finalParams['isRich'], $finalParams['rich_blocks']);
        } elseif (isset($finalParams['isRich']) && $finalParams['isRich'] === true) {
            if (isset($finalParams['rich_blocks']) && is_array($finalParams['rich_blocks'])) {
                $finalParams['text'] = $this->convertRichBlocksToAnsi($finalParams['rich_blocks']);
            }
            $finalParams['text'] = $finalParams['text'] ?? '';
            unset($finalParams['isRich'], $finalParams['isRtl'], $finalParams['rich_blocks']);
        }

        // ── Normalize keyboard / keypad ───────────────────────────────────
        $normalizedParams = $this->normalizePayload($finalParams);

        // ── Render to terminal ────────────────────────────────────────────
        $this->renderToTerminal($finalMethod, $normalizedParams);

        // ── Return synthetic success response ─────────────────────────────
        $pseudoId = $this->generatePseudoMessageId();

        return [
            'ok'     => true,
            'result' => array_merge(['message_id' => $pseudoId], $normalizedParams),
        ];
    }

    // =========================================================================
    // 🧠 PAYLOAD NORMALIZER (mirrors BaleDriver::normalizePayload)
    // =========================================================================

    protected function normalizePayload(array $params): array
    {
        // Unify 'keypad' → 'reply_markup'
        if (isset($params['keypad'])) {
            $params['reply_markup'] = $params['keypad'];
            unset($params['keypad']);
        }

        if (isset($params['reply_markup'])) {
            $markup = $params['reply_markup'];

            if ($markup instanceof KrubiKInlineKeyboard) {
                $params['reply_markup'] = $this->transformInlineKeyboard($markup);
            } elseif ($markup instanceof KrubiKReplyKeyboard) {
                $params['reply_markup'] = $markup->toArray();
            } elseif (is_string($markup)) {
                $decoded = json_decode($markup, true);
                $params['reply_markup'] = $decoded ?: $markup;
            }
        }

        return $params;
    }

    /**
     * Translate KrubiK inline keyboard to CLI-renderable structure.
     */
    protected function transformInlineKeyboard(KrubiKInlineKeyboard $keyboard): array
    {
        $data    = $keyboard->toArray();
        $rows    = $data['rows'] ?? [];
        $cliRows = [];

        foreach ($rows as $row) {
            $buttons = $row['buttons'] ?? $row;
            $cliRow  = [];

            foreach ($buttons as $btn) {
                $cliBtn = [
                    'text' => $btn['text'],
                    'col'  => $btn['col'] ?? 6,
                ];

                if (!empty($btn['url']) || (($btn['type'] ?? '') === 'Link')) {
                    $cliBtn['type'] = 'url';
                    $cliBtn['url']  = $btn['url'] ?? ($btn['link_data']['url'] ?? '#');
                } elseif (!empty($btn['action_id'])) {
                    $cliBtn['type']        = 'callback';
                    $cliBtn['action_id']   = $btn['action_id'];
                    $cliBtn['action_data'] = $btn['action_data'] ?? [];
                } else {
                    $cliBtn['type']      = 'callback';
                    $cliBtn['action_id'] = 'NO_ACTION';
                }

                $cliRow[] = $cliBtn;
            }

            $cliRows[] = $cliRow;
        }

        return ['inline_keyboard' => $cliRows];
    }

    // =========================================================================
    // 🖥️ TERMINAL RENDERER — The Visual Soul of CLIDriver
    // =========================================================================

    /**
     * Master renderer: decides how to display each Telegram method type.
     */
    protected function renderToTerminal(string $method, array $params): void
    {
        $this->printDivider('─');

        // Dispatch to method-specific renderers
        match (true) {
            str_starts_with($method, 'send')          => $this->renderMessage($method, $params),
            str_starts_with($method, 'edit')          => $this->renderEdit($method, $params),
            str_starts_with($method, 'answer')        => $this->renderAnswer($method, $params),
            str_starts_with($method, 'delete')        => $this->renderDelete($method, $params),
            default                                    => $this->renderRaw($method, $params),
        };

        // Render keyboard if present
        if (isset($params['reply_markup']['inline_keyboard'])) {
            $this->renderKeyboard($params['reply_markup']['inline_keyboard']);
        }

        $this->newline();
    }

    /**
     * Render sendMessage / sendPhoto / sendDocument / etc.
     */
    protected function renderMessage(string $method, array $params): void
    {
        $icon = match ($method) {
            'sendMessage'  => '💬',
            'sendPhoto'    => '🖼️ ',
            'sendVideo'    => '🎬',
            'sendAudio'    => '🎵',
            'sendDocument' => '📄',
            'sendSticker'  => '🎭',
            default        => '📨',
        };

        $chatId = $params['chat_id'] ?? 'web';
        $this->line(
            $this->c('dim', "  {$icon} [{$method}]") .
            $this->c('dim', " → chat:{$chatId}")
        );
        $this->newline();

        $text = $params['text'] ?? $params['caption'] ?? null;
        if ($text !== null) {
            $this->renderTextBlock($text, $params['parse_mode'] ?? null);
        }

        // Extra fields
        foreach (['photo', 'video', 'audio', 'document'] as $mediaKey) {
            if (isset($params[$mediaKey])) {
                $this->line($this->c('cyan', "  📎 {$mediaKey}: ") . $params[$mediaKey]);
            }
        }
    }

    protected function renderEdit(string $method, array $params): void
    {
        $this->line($this->c('yellow', "  ✏️  [{$method}]"));
        $this->newline();
        if (isset($params['text'])) {
            $this->renderTextBlock($params['text'], $params['parse_mode'] ?? null);
        }
    }

    protected function renderAnswer(string $method, array $params): void
    {
        $text = $params['text'] ?? ($params['message'] ?? '');
        $this->line($this->c('cyan', "  ✅ [{$method}]: ") . $text);
    }

    protected function renderDelete(string $method, array $params): void
    {
        $mid = $params['message_id'] ?? '?';
        $this->line($this->c('red', "  🗑️  [{$method}]: message_id={$mid}"));
    }

    protected function renderRaw(string $method, array $params): void
    {
        $this->line($this->c('magenta', "  🔧 [{$method}]"));
        $this->line($this->c('dim', json_encode($params, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)));
    }

    /**
     * Render the message text body.
     * Handles Markdown, HTML, and plain text formatting in terminal.
     */
    protected function renderTextBlock(string $text, ?string $parseMode): void
    {
        $lines = explode("\n", $text);

        foreach ($lines as $line) {
            $rendered = $this->applyMarkdownToAnsi($line, $parseMode);
            // Word-wrap to terminal width
            $wrapped  = $this->wordWrap($rendered, $this->termWidth - 4);
            foreach (explode("\n", $wrapped) as $wrappedLine) {
                $this->line("    {$wrappedLine}");
            }
        }
    }

    /**
     * Render inline keyboard as a grid of buttons.
     */
    protected function renderKeyboard(array $rows): void
    {
        $this->newline();
        $this->line($this->c('dim', "  ┌── Keyboard ─────────────────────────────────────┐"));

        foreach ($rows as $row) {
            $rendered = [];
            foreach ($row as $btn) {
                $label   = $btn['text'] ?? '';
                $type    = $btn['type'] ?? 'callback';
                $action  = $btn['action_id'] ?? ($btn['url'] ?? '');
                $icon    = match ($type) {
                    'url'      => '🔗',
                    'web_app'  => '🌐',
                    'callback' => '⚡',
                    default    => '•',
                };
                $rendered[] = $this->c('bblue', "  │ {$icon} ") .
                              $this->c('bwhite', str_pad($label, 20)) .
                              $this->c('dim', " [{$action}]");
            }

            foreach ($rendered as $btnLine) {
                $this->line($btnLine);
            }

            if ($row !== end($rows)) {
                $this->line($this->c('dim', "  ├─────────────────────────────────────────────────┤"));
            }
        }

        $this->line($this->c('dim', "  └─────────────────────────────────────────────────┘"));
    }

    // =========================================================================
    // 📥 INCOMING PAYLOAD RESOLVER
    // =========================================================================

    /**
     * Resolve the incoming "update" from:
     *   1. ARGV positional args:  php krubot:cli panel
     *   2. --json='{}' option:   php krubot:cli --json='{"action":"order","id":5}'
     *   3. STDIN pipe:           echo '{"command":"panel"}' | php krubot:cli
     *   4. Interactive prompt:   (if no args, launch REPL)
     */
    protected function resolveIncomingPayload(): array
    {
        // 1. --json option
        if (!empty($this->cliOptions['json'])) {
            $decoded = json_decode($this->cliOptions['json'], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        // 2. STDIN pipe (non-interactive)
        if (!stream_isatty(STDIN)) {
            $raw     = stream_get_contents(STDIN);
            $decoded = json_decode(trim($raw), true);
            if (is_array($decoded)) {
                return $decoded;
            }
            // Plain text command
            if (!empty(trim($raw))) {
                return ['command' => trim($raw), 'text' => '/' . trim($raw)];
            }
        }

        // 3. Positional argv (e.g., php artisan krubot:cli panel --user_id=42)
        if (!empty($this->cliOptions['_command'])) {
            return array_merge(
                ['command' => $this->cliOptions['_command'], 'text' => '/' . $this->cliOptions['_command']],
                $this->cliOptions
            );
        }

        // 4. Interactive REPL mode
        $this->interactiveMode = true;
        return $this->launchInteractivePrompt();
    }

    /**
     * Parse ARGV into structured options.
     */
    protected function parseArgv(): array
    {
        $argv = $_SERVER['argv'] ?? [];
        // Drop script name
        array_shift($argv);

        $options  = [];
        $positional = [];

        foreach ($argv as $arg) {
            if (str_starts_with($arg, '--')) {
                $part = substr($arg, 2);
                if (str_contains($part, '=')) {
                    [$key, $val] = explode('=', $part, 2);
                    $options[$key] = $val;
                } else {
                    $options[$part] = true;
                }
            } elseif (str_starts_with($arg, '-')) {
                $options[substr($arg, 1)] = true;
            } else {
                $positional[] = $arg;
            }
        }

        if (!empty($positional)) {
            $options['_command'] = implode(' ', $positional);
        }

        return $options;
    }

    /**
     * Resolve sender identity.
     * In CLI context: system user + config or --user flag.
     */
    protected function resolveSenderUser(): array
    {
        // Explicit --user-id flag
        $userId = $this->cliOptions['user_id'] ?? $this->cliOptions['user-id'] ?? null;

        // Payload may also carry a user block
        if (!empty($this->payload['user']) && is_array($this->payload['user'])) {
            return $this->payload['user'];
        }

        return [
            'id'         => $userId ? (int) $userId : $this->deriveSystemUserId(),
            'is_bot'     => false,
            'first_name' => $this->cliOptions['first_name'] ?? (get_current_user() ?: 'Developer'),
            'username'   => $this->cliOptions['username'] ?? 'dev_' . (get_current_user() ?: 'user'),
            'platform'   => 'cli',
        ];
    }

    /**
     * Interactive REPL: prompt user for command input if no ARGV/STDIN given.
     */
    protected function launchInteractivePrompt(): array
    {
        $this->line($this->c('byellow', "\n  🎮 Krubot CLI Interactive Mode"));
        $this->line($this->c('dim', "  Type a command (e.g. 'panel', 'menu') or JSON payload."));
        $this->line($this->c('dim', "  Type 'exit' to quit. Type 'help' for available commands.\n"));

        while (true) {
            $this->raw($this->c('bgreen', "  krubot» ") . $this->c('reset', ''));
            $input = trim(fgets(STDIN));

            if (in_array(strtolower($input), ['exit', 'quit', 'q'], true)) {
                $this->line($this->c('dim', "\n  👋 Goodbye!\n"));
                exit(0);
            }

            if (strtolower($input) === 'help') {
                $this->renderHelp();
                continue;
            }

            // Try JSON
            $decoded = json_decode($input, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            // Plain command
            if (!empty($input)) {
                return ['command' => $input, 'text' => '/' . $input];
            }
        }
    }

    // =========================================================================
    // 🖨️ TERMINAL OUTPUT PRIMITIVES
    // =========================================================================

    /**
     * Print a styled line to STDOUT.
     */
    protected function line(string $text): void
    {
        echo $text . PHP_EOL;
    }

    /**
     * Print without newline.
     */
    protected function raw(string $text): void
    {
        echo $text;
    }

    /**
     * Print empty line.
     */
    protected function newline(): void
    {
        echo PHP_EOL;
    }

    /**
     * Print a divider line.
     */
    protected function printDivider(string $char = '─', ?string $color = null): void
    {
        $line = str_repeat($char, $this->termWidth);
        $this->line($color ? $this->c($color, $line) : $this->c('dim', $line));
    }

    /**
     * Apply ANSI color.
     * @param string $color  Key from self::ANSI
     */
    protected function c(string $color, string $text): string
    {
        if (!$this->ansiEnabled) {
            return $text;
        }
        return (self::ANSI[$color] ?? '') . $text . self::ANSI['reset'];
    }

    /**
     * Print the Krubot CLI banner.
     */
    protected function printBanner(): void
    {
        $this->newline();
        $this->printDivider('═', 'bblue');
        $this->line($this->c('bmagenta', "  ⚡  K R U B O T  ") . $this->c('dim', "CLI Driver"));
        $this->line($this->c('dim', "  Driver: ") . $this->c('bwhite', $this->driverAlias) .
                    $this->c('dim', "  |  Bot: ") . $this->c('cyan', $this->botUser['username'] ?? 'krubot'));
        $this->line($this->c('dim', "  User:   ") . $this->c('bwhite', $this->senderUser['first_name'] ?? 'Developer') .
                    $this->c('dim', "  (id:") . $this->c('yellow', (string) ($this->senderUser['id'] ?? 0)) . $this->c('dim', ")"));
        $this->printDivider('═', 'bblue');
        $this->newline();
    }

    /**
     * Render help information.
     */
    protected function renderHelp(): void
    {
        $this->newline();
        $this->line($this->c('byellow', "  Available usage patterns:"));
        $this->newline();

        $cmds = [
            'panel'                          => 'Trigger #[OnCommand(\'panel\')] handler',
            'menu'                           => 'Trigger #[OnCommand(\'menu\')] handler',
            '{"action":"order","id":5}'      => 'Fire #[Action(\'order\')] with id=5',
            '{"command":"menu","user_id":99}'=> 'Run command as a specific user',
            '--user_id=42 panel'             => 'Set CLI user then run command',
            'exit'                           => 'Exit interactive mode',
        ];

        foreach ($cmds as $cmd => $desc) {
            $this->line(
                $this->c('bgreen', "    " . str_pad($cmd, 40)) .
                $this->c('dim', $desc)
            );
        }

        $this->newline();
    }

    /**
     * Apply basic Markdown/HTML → ANSI conversion for terminal rendering.
     */
    protected function applyMarkdownToAnsi(string $text, ?string $parseMode): string
    {
        if (!$this->ansiEnabled) {
            // Strip markup
            return strip_tags(preg_replace(['/\*\*(.*?)\*\*/', '/\*(.*?)\*/', '/`(.*?)`/'], '$1', $text));
        }

        // Markdown-like (MarkdownV2 / Krubot's own **bold**, *italic*, `code`)
        $text = preg_replace_callback('/\*\*(.*?)\*\*/', fn($m) => $this->c('bold', $m[1]), $text);
        $text = preg_replace_callback('/\*(.*?)\*/',     fn($m) => $this->c('italic', $m[1]), $text);
        $text = preg_replace_callback('/`(.*?)`/',       fn($m) => $this->c('cyan', $m[1]), $text);
        $text = preg_replace_callback('/~~(.*?)~~/',     fn($m) => $this->c('dim', $m[1]), $text);

        // HTML tags
        if ($parseMode === 'html') {
            $text = preg_replace_callback('/<b>(.*?)<\/b>/s',  fn($m) => $this->c('bold', $m[1]), $text);
            $text = preg_replace_callback('/<i>(.*?)<\/i>/s',  fn($m) => $this->c('italic', $m[1]), $text);
            $text = preg_replace_callback('/<code>(.*?)<\/code>/s', fn($m) => $this->c('cyan', $m[1]), $text);
            $text = preg_replace_callback('/<pre>(.*?)<\/pre>/s',   fn($m) => $this->c('dim', $m[1]), $text);
            $text = strip_tags($text);
        }

        return $text;
    }

    /**
     * Convert rich_blocks array to ANSI-styled terminal string.
     */
    protected function convertRichBlocksToAnsi(array $blocks): string
    {
        $output = '';
        foreach ($blocks as $block) {
            $text = $block['text'] ?? '';
            $output .= match ($block['type'] ?? 'text') {
                'bold'      => $this->c('bold', $text),
                'italic'    => $this->c('italic', $text),
                'code'      => $this->c('cyan', $text),
                'separator' => str_repeat('─', min($this->termWidth - 4, 40)),
                default     => $text,
            } . "\n";
        }
        return trim($output);
    }

    /**
     * Word-wrap preserving existing ANSI escape sequences.
     * ANSI codes are invisible — we measure printable length only.
     */
    protected function wordWrap(string $text, int $width): string
    {
        // Strip ANSI to measure visible length
        $stripped = preg_replace('/\033\[[0-9;]*m/', '', $text);

        if (mb_strlen($stripped) <= $width) {
            return $text;
        }

        // Basic word-wrap on stripped text (for now; full ANSI-aware wrap is complex)
        return wordwrap($stripped, $width, "\n    ", true);
    }

    /**
     * Derive a stable system user ID from the OS username.
     */
    protected function deriveSystemUserId(): int
    {
        return abs(crc32(get_current_user() . gethostname()));
    }

    /**
     * Detect if the current terminal supports ANSI escape codes.
     */
    protected function detectAnsiSupport(): bool
    {
        // Windows without ANSICON or Windows Terminal
        if (PHP_OS_FAMILY === 'Windows') {
            return isset($_SERVER['ANSICON']) ||
                   str_contains($_SERVER['TERM'] ?? '', 'xterm') ||
                   str_contains($_SERVER['ConEmuANSI'] ?? '', 'ON');
        }

        // Unix: check TERM env
        $term = $_SERVER['TERM'] ?? $_SERVER['COLORTERM'] ?? '';
        return !empty($term) && $term !== 'dumb';
    }

    /**
     * Detect terminal width.
     */
    protected function detectTerminalWidth(): int
    {
        // tput cols
        if (PHP_OS_FAMILY !== 'Windows' && function_exists('shell_exec')) {
            $cols = (int) shell_exec('tput cols 2>/dev/null');
            if ($cols > 0) {
                return $cols;
            }
        }

        return (int) ($_SERVER['COLUMNS'] ?? 80);
    }

    // =========================================================================
    // 🪧 BOT IDENTITY (StandardDriverInterface)
    // =========================================================================

    public function getMe(): array
    {
        return $this->botUser;
    }

    // =========================================================================
    // 👤 USER & CHAT ACCESSORS
    // =========================================================================

    public function getUser(): array
    {
        return $this->senderUser;
    }

    public function getChat(): array
    {
        return [
            'id'   => $this->senderUser['id'] ?? 0,
            'type' => 'cli',
            'title' => 'CLI Session',
        ];
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function param(string $key, mixed $default = null): mixed
    {
        return $this->payload[$key] ?? $this->cliOptions[$key] ?? $default;
    }

    // =========================================================================
    // 🔔 STUB IMPLEMENTATIONS (no-ops in CLI context)
    // =========================================================================

    public function deleteWebhook(): bool
    {
        return true;
    }

    public function getWebhookInfo(): array
    {
        return ['url' => '', 'has_custom_certificate' => false, 'pending_update_count' => 0];
    }

    public function getWebhookUpdate(): array
    {
        return $this->payload;
    }

    public function sendMessage(array $params): array
    {
        return $this->makeRequest('sendMessage', $params);
    }

    // =========================================================================
    // 🛠 MISC
    // =========================================================================

    protected function generatePseudoMessageId(): int
    {
        static $counter = 0;
        return ++$counter;
    }
}
