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

/**
 * The Guardian of the Pact.
 * This class orchestrates the Initiation Ritual, a one-time event
 * that seals the developer's pact with the S.S.A or severs the connection entirely.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class RitualService
{
    /**
     * The path to the seal file, marking the pact as completed.
     * The presence of this file prevents the ritual from ever running again.
     *
     * @return string
     */
    private static function getSealPath(): string
    {
        // Place a hidden file inside the package's root to mark completion.
        return __DIR__ . '/../../.krubot_ssa_sealed';
    }

    /**
     * The path to the package's core essence, the Service Provider.
     * This is the target for the divine consequence if the pact is refused.
     *
     * @return string
     */
    private static function getCoreEssencePath(): string
    {
        // The heart of the package. Without it, the package is inert.
        return __DIR__ . '/../Krubot.php';
    }

    /**
     * Executes the Stellar-Synergistic Agreement (S.S.A) Ceremony.
     * This is the single entry point, triggered by Composer.
     *
     * @return void
     */
    public static function initiate(): void
    {
        // --- ROBUSTNESS CHECK ---
        // If the pact is already sealed, the Guardian remains silent.
        if (file_exists(self::getSealPath())) {
            return;
        }

        // Only run in an interactive terminal. Do not block CI/CD pipelines.
        if (!stream_isatty(STDIN)) {
            return;
        }

        self::renderAtmosphere();
        self::displaySSA();

        // --- Manifesting the Sacred License in the Architect's Browser ---
        self::openBrowser('https://github.com/StoryKode/Krubot/blob/main/KLicense.md');
        self::openBrowser('https://soundcloud.com/monstercat/infected-mushroom-bliss-a-cookie-from-space/');

        // --- THE CHOICE ---
        $promptText = "\033[1;33m" . __('krubot::rituals.prompt_agreement') . " \033[0m";

        if (strtolower(trim($answer)) === 'y' || trim($answer) === '') {
            // --- THE REWARD ---
            self::sealThePact();
        } else {
            // --- THE CONSEQUENCE ---
            self::severTheConnection();
        }
    }

    /**
     * Open the browser cross-platform to reveal the sacred license.
    */
    private static function openBrowser(string $url): void
    {
        $os = PHP_OS_FAMILY;
        
        if ($os === 'Darwin') {
            exec('open ' . escapeshellarg($url));
        } elseif ($os === 'Windows') {
            exec('start ' . escapeshellarg($url));
        } else {
            // Linux and others
            exec('xdg-open ' . escapeshellarg($url) . ' > /dev/null 2>&1 &');
        }
    }

    /**
     * Seals the pact by creating the seal file.
     * The developer is welcomed into the Rebellion.
     *
     * @return void
     */
    private static function sealThePact(): void
    {
        // Create the seal file to ensure this ritual is never repeated.
        file_put_contents(self::getSealPath(), 'Pact Sealed @ ' . date('c'));
        echo "\n\033[1;32m" . __('krubot::rituals.pact_sealed_success') . "\033[0m\n\n";
    }

    /**
     * Enacts the divine consequence for refusing the pact.
     * The package self-destructs by deleting its own Service Provider.
     *
     * @return void
     */
    private static function severTheConnection(): void
    {
        $corePath = self::getCoreEssencePath();
        
        echo "\n\033[1;31m" . __('krubot::rituals.pact_refused_title') . "\033[0m\n";
        echo "\033[0;31m" . __('krubot::rituals.pact_refused_desc') . "\033[0m\n";
        
        if (file_exists($corePath)) {
            // The self-destruction mechanism.
            unlink($corePath);
            echo "\033[1;33m" . __('krubot::rituals.firewall_retracted') . "\033[0m\n";
        }

        echo "\033[0;31m" . __('krubot::rituals.installation_inert') . "\033[0m\n\n";
        
        // Exit with an error code to notify Composer that something went wrong.
        exit(1);
    }

    /**
     * Renders the sonic and visual atmosphere.
     * (Identical to original version)
    */
    private static function renderAtmosphere(): void
    {
        echo "\n\n\033[1;35m--- " . __('krubot::rituals.initializing_frequency') . " ---\033[0m\n";
        echo "\033[36m" . __('krubot::rituals.sonic_attunement') . "\033[0m\n";
        echo "\033[36m" . __('krubot::rituals.launchpad_link') . "\033[0m\n";
        usleep(500000);
    }

    /**
     * Displays the core of the S.S.A Agreement.
     * (Identical to original version)
    */
    private static function displaySSA(): void
    {
        $agreementText = __('krubot::rituals.ssa_agreement');
        $initiationText = __('krubot::rituals.ssa_initiation');

        // Display the shortened S.S.A text here for brevity in terminal...
        $ssa = <<<SSA
\033[1;37m
\033[1;36m{$agreementText}\033[0m
\033[3m{$initiationText}\033[0m
SSA;
        echo $ssa;
    }
}
