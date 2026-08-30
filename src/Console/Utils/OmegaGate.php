<?php

declare(strict_types=1);

namespace KrubiK\Console\Utils;

/**
 * OmegaGate - Cross-platform utility to open URLs in the default system browser.
 * 
 * Provides single URL opening (warp) and multi-URL batch opening (clusterWarp).
 * Handles platform-specific quirks and fallbacks gracefully.
 * 
 * Usage:
 *   OmegaGate::warp('https://example.com');        // Open single URL
 *   OmegaGate::clusterWarp('https://a.com', 'https://b.com');  // Open multiple URLs
*/
final class OmegaGate
{
    // prevent generate OmegaGate instance
    private function __construct()
    {
    }

    /**
     * Open a URL using the system's default browser.
    */
    public static function warp(string $url): bool
    {
        if (!self::validUrl($url)) {
            return false;
        }

        try {
            return match (PHP_OS_FAMILY) {
                'Windows' => self::windows($url),
                'Linux'   => self::linux($url),
                // macOS
                'Darwin'  => self::unix([
                    '/usr/bin/open',
                    $url,
                ]),
                default   => self::unixFallback($url),
            };
        } catch (\Throwable) {
          /*
            * Opening the browser is optional.
            * It must never break the Ritual.
          */
            return false;
        }
    }

    /**
     * Open multiple URLs.
    */
    public static function clusterWarp(string|array ...$urls): int
    {
        $opened = 0;

        foreach ($urls as $items) {
            // Support both individual URLs and arrays of URLs, or mix of them.
            $items = is_array($items) ? $items : [$items];

            foreach ($items as $url) {
                if (self::warp($url)) {
                    ++$opened;
                }
            }
        }

        return $opened;
    }
    
    private static function isWin()
    {
        return PHP_OS_FAMILY === 'Windows';
    }

    /**
     * Supports Windows 7 / 8 / 8.1 / 10 / 11.
     */
    private static function windows(string $url): bool
    {

        /*
        |------------------------------------------------------------------
        | Method #1 — Windows Explorer / Shell
        |------------------------------------------------------------------
        | explorer.exe delegates the URL to the user's default browser.
        | We execute it directly, bypassing cmd.exe.
        */
        if (self::spawnDetachedProcess([
            'explorer.exe',
            $url,
        ])) {
            return true;
        }
        
        /*
        |--------------------------------------------------------------------------
        | Primary: Explorer Shell
        |--------------------------------------------------------------------------
        |
        | Works with the default URL handler.
        |
        */

        if (self::spawnDetachedProcess([
            'explorer.exe',
            $url,
        ])) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | Method #2 : Windows ShellExecute URL protocol handler
        |--------------------------------------------------------------------------
        */

        if (self::spawnDetachedProcess([
            'rundll32.exe',
            'url.dll,FileProtocolHandler',
            $url,
        ])) {
            return true;
        }

        /*
        |------------------------------------------------------------------
        | Method #3 — Correct CMD-START syntax
        |------------------------------------------------------------------
        |
        | start "TITLE" "URL"
        |
        | IMPORTANT:
        |
        | An empty title is intentional, it's required when the URL is quoted.
        |
        */
        $safeUrl = self::escapeWindowsCmd($url);

        $command =
            'cmd.exe /D /C ' . 'start "" /B "'
            . $safeUrl .
            '"';

        @pclose(@popen($command, 'r'));

        return true;
    }

    /**
     * Linux / WSL.
     */
    private static function linux(string $url): bool
    {
        if (self::isWsl()) {
            // WSL: preferably delegate to the Windows default browser.
            if (self::commandExists('wslview')) {
                return self::spawnDetachedProcess([
                    'wslview',
                    $url,
                ]);
            }

            if (self::commandExists('powershell.exe')) {
                return self::spawnDetachedProcess([
                    'powershell.exe',
                    '-NoProfile',
                    '-NonInteractive',
                    '-Command',
                    'Start-Process',
                    $url,
                ]);
            }
        }
        
        // Standard Linux desktop.

        /*
        |--------------------------------------------------------------------------
        | Other Unix-like systems
        |--------------------------------------------------------------------------
        */
        foreach ([
            ['xdg-open', $url],
            ['gio', 'open', $url],
            ['sensible-browser', $url],
        ] as $launcher_command) {

            if (!self::commandExists((string) $launcher_command[0])) 
                continue;

            return self::spawnDetachedProcess($launcher_command);
        }

        return false;
        
    }

    /**
     * Generic Unix fallback.
     */
    private static function unixFallback(string $url): bool
    {
        foreach ([
            ['xdg-open', $url],
            ['gio', 'open', $url],
        ] as $command) {

            if (!self::commandExists((string) $command[0])) {
                continue;
            }

            return self::spawnDetachedProcess($command);
        }

        return false;
    }

    /**
     * Platform null device.
     */
    private static function nullDevice(): string
    {
        return self::isWin()
            ? 'NUL'
            : '/dev/null';
    }

    /**
     * Execute a process directly.
     */
    /**
     * Execute a process without routing through the shell.
     *
     * This is particularly important on Windows where exec()/shell_exec()
     * normally involve cmd.exe.
    */
    private static function spawnDetachedProcess(array $command): bool
    {
        if (!function_exists('proc_open')) {
            return false;
        }

        $process = null;

        try {
            $null = self::nullDevice();

            $descriptors = [
                0 => ['file', $null, 'r'],
                1 => ['file', $null, 'w'],
                2 => ['file', $null, 'w'],
            ];

            $options = [
              'create_process_group' => true
            ];

            if (self::isWin()) {
                $options['bypass_shell'] = true;
            }

            $process = @proc_open(
                $command,
                $descriptors,
                $pipes,
                null,
                null,
                $options,
            );

            if (!is_resource($process)) {
                return false;
            }

            /*
            * We intentionally close the process immediately.
            * GUI launchers normally return almost instantly.
            */
            $exitCode = @proc_close($process);

            return $exitCode === 0 || $exitCode === -1;

        } catch (\Throwable) {

            if (is_resource($process)) {
                @proc_terminate($process);
                @proc_close($process);
            }
            
            // Browser launching is best-effort.
            // Never allow it to break the Ritual itself.

            return false;
        }
    }

    /**
     * Validate HTTP(+S?) URL, without opening it.
     */
    private static function validUrl(string $url): bool
    {
        return $url !== ''
            && filter_var($url, FILTER_VALIDATE_URL) !== false
            && preg_match('#^https?://#i', $url) === 1;
    }

    /**
     * Escape a URL for the Windows cmd.exe fallback.
     *
     * NOTE:
     * escapeshellarg() is intentionally NOT used here because its Windows
     * implementation can modify '%' and '!' characters.
    */
    private static function escapeForWindowsCmd(string $value): string
    {
        return str_replace(
            [
                '^',
                '&',
                '|',
                '<',
                '>',
                '(',
                ')',
                '%',
                '!',
            ],
            [
                '^^',
                '^&',
                '^|',
                '^<',
                '^>',
                '^(',
                '^)',
                '^%',
                '^!',
            ],
            $value
        );
    }

    /**
     * Detect Windows Subsystem for Linux.
     */
    private static function isWsl(): bool
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            return false;
        }

        if (is_file('/proc/version')) {

            $version = @file_get_contents('/proc/version');

            return is_string($version)
                && (
                    stripos($version, 'microsoft') !== false
                    || stripos($version, 'wsl') !== false
                );
        }

        return false;
    }
    
    /**
     * Check whether a executable command exists & available in PATH.
    */
    private static function commandExists(string $command): bool
    {
        $output = [];
        $exitCode = 1;

        if (self::isWin()) {
            @exec(
                'where.exe ' . escapeshellarg($command),
                $output,
                $exitCode
            );
        } else {
            @exec(
                'command -v ' . escapeshellarg($command),
                $output,
                $exitCode
            );
        }

        return $exitCode === 0 && !empty($output);
    }
}