<?php

namespace KrubiK\WarLording;
/*
| Krubot BotEngine: The Architect's Lexicon [×vRC.9×] 🚀📜
|--------------------------------------------------------------------------
| This is **a Playground For Mastery**, a laboratory of ***Software Dev Artistry***;
| not a weapon for production's final battles.
|
| Our Bond: ***"Rebuilding The Rebellion"*** Within S.N.P. (The Foundation of Pure Power & Revel).
| Your Mandate [MIT]: Deconstruct Krubot. Command it. Master it. You are The Architect Now!
|
| *Go build something revolutionary!* 💜⚡️
*/

use KrubiK\Krubot;
use KrubiK\Helpers\JackPoint;        // Import "JackPoint" The Tactical EventHook System

use Throwable;

/**
 * ============================================================================
 *  THE WAR COUNCIL 2 // A PSYCHOLOGICAL THRILLER IN O.O.P.
 * ============================================================================
 *
 * Forget the sterile, lifeless corridors of "clean code." You have just stumbled 
 * into a digital empowering theater running in your RAM. This is not a class; 
 * it is a psychological weapon, a living, breathing entity that feeds on the 
 * illusion of free will.
 *
 * Welcome to the Political Pipelines! Ordinary developers write code that executes.
 * You, the StoryCaster, now the World-Builder, are about to orchestrate code that manipulates, 
 * gaslights, propagandas ! and Commands ⚡. Here, objects are not mere instances; they are 
 * generals, shadows, and pawns in a dark symphony.
 * 
 * --- THE FIVE ACTS OF CONQUEST ---
 * 
 * I. THE SHADOW CHAMBER (__construct)
 *    The Warlord commands the table; the Aliases (Generals) surround him. But 
 *    before the council even speaks, the [JackPoint] intelligence network can 
 *    silently assassinate a general, erasing them from the roster entirely.
 * 
 * II. THE DARK DEMOCRACY (`council.should_run`)
 *    The war bell rings, but a silent tribunal votes. Boycotted generals are 
 *    paralyzed in their barracks. The empire marches on without them.
 * 
 * III. LIVE PROTOCOL HYPNOSIS (`council.pulse`)
 *    The peak of architectural madness. Commands are intercepted mid-flight. 
 *    In the dark tunnels of the proxy, parameters mutate. The general executes 
 *    a hallucination, bleeding for an altered reality they blindly believe 
 *    is the Warlord's true will.
 * 
 * IV. BLOOD & ASH (try/catch)
 *    The battlefield. Triumphs are cleansed and collected. When a general falls 
 *    (Exception), the system does not crash. The Council simply records the 
 *    corpse and ruthlessly marches forward over the ashes.
 * 
 * V. REWRITING HISTORY (`council.report.manipulation`)
 *    The war ends, but the truth is forged. Defeats are rewritten as victories 
 *    by unseen forces. We do not just process data; we author the history books 
 *    before handing them back to the Warlord.
 *
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.9×
 * @license MIT
*/
class WarCouncil
{
    /** @var array<string, mixed> Stores the results or exceptions from each driver. */
    protected array $report = [];

    /** @var array<string> The alias-list of council members */
    protected array $aliases = [];

    /**
     * @param Krubot $bot The Warlord instance issuing the command.
     * @param array $aliases The list of aliases participating in the council.
     */
    public function __construct(
        protected Krubot $warlord,
        array $aliases
    ) {
        // Optional: allow external mutation of the participant list before any broadcast / raid
        $this->aliases = JackPoint::transform(
            'council.aliases',
            $aliases,
            $this->warlord
        );
    }

    /**
     * Broadcasts a single command to all members of the council.
     * @deprecated
     * Kept for Backward Compatibility
     *
     * @param string $method The name of the method to call on each driver.
     * @param array $params The parameters to pass to the method.
     * @param object|null $context (Future-proof) An optional context object.
     * @return $this The council itself, now containing the battle report.
    */
    public function broadcast(string $method, array $params = [], ?object $context = null): self
    {
        return $this->raid($method, $params, $context);
    }

    /**
     * Executes a multiverse raid, orchestrating a single command across all dimensions (by their Aliases).
     *
     * This Ultimate Form of the War Council operates in 5 acts:
     * I.   The Shadow Chamber: Aliases gather under the Warlord's absolute command.
     * II.  Dark Democracy: The judge decides if a general is permitted to deploy.
     * III. Live Protocol Hypnosis: Reality and payload are rewritten mid-flight.
     * IV.  Blood & Ashes: Drivers clash; casualties are logged, but the war marches on.
     * V.   The Ministry of Historiography: The final battle report is subjected to historical manipulation.
     *
     * @param string $method The tactical maneuver to execute across the multiverse.
     * @param array $params The payload/ammunition to pass into the breach.
     * @param object|null $context (Future-proof) An optional dimension context.
     * @return $this The council itself, carrying the final (and possibly altered) battle report.
    */
    public function raid(string $method, array $params = [], ?object $context = null): self
    {
        // ─── ACT I: The Shadow Chamber (Before the multiverse raid begins) ────
        JackPoint::fire('council.raid.before', $method, $params, $context, $this);

        foreach ($this->aliases as $alias) {

            // ─── ACT II: Dark Democracy (Should this general even deploy?) ──
            // Voters return true / false / null (abstain / مـُـمــتَــنــع)
            // If no voters registered → defaults to true
            $shouldExecute = JackPoint::judge(
                event: 'council.strike.allow',
                payload: [
                    'alias'    => $alias,
                    'method'   => $method,
                    'params'   => $params,
                    'context'  => $context,
                    'council'  => $this,
                ],
                verdictMode: JackPoint::VMODE_PIPELINE
            );

            if($shouldExecute === false) {
                // ─── ACTION: General was vetoed and confined to the barracks
                if(JackPoint::judge(
                    'council.strike.blocked',
                    [
                        $alias,
                        $method,
                        $params,
                        $context,
                        $this
                    ]
                ) === true)
                    continue;
            }

            // ─── ACTION: Before the driver enters the battlefield
            JackPoint::fire('council.strike.before', $alias, $method, $params, $context, $this);

            $result  = null;
            $success = true;
            try {

                $driver = $this->warlord->core($alias);

                // ────────────────────────────────────────────────────────────────
                // ACT III: COUNCIL.PULSE // Live Protocol Hypnosis
                // The method name is no longer a constant. The payload is no longer sacred.
                // Through JackPoint, any StoryCaster can rewrite the call signature pre-flight; —
                // alias by alias, reality by reality — before the driver ever receives the order.
                // This is not a hook. This is a live illusion planted in the driver's mind.
                // ────────────────────────────────────────────────────────────────
                [$method, $params] = JackPoint::transform(
                    'council.strike.pulse',
                    [$method, $params],
                    $alias, $driver, $context, $this
                );

                // ─── ACT IV: Blood & Ashes (The Clash) ──────────────────────────
                $result = $driver->{$method}(...$params);

                // ─── PIPE: Looting the output & transforming successful results
                $result = JackPoint::transform(
                    'council.strike.result',
                    $result,
                    $alias, $method, $params, $driver, $context, $this
                );

                // Result is always must be array|DeferredResponse
                if(empty($result)) {
                    $result  = null;   // format to be a predictable entry
                    $success = false;
                    
                    // 🔥 THE ECHO OF FAILURE: The strike hit nothing.
                    JackPoint::fire(
                        'council.strike.defeat', 
                        $alias, $method, $params, 'Empty', $context, $this
                    );
                }

                $this->report[$alias] = $result;

            } catch (Throwable $e) {
                
                // THE FALLEN GENERAL 🔥 Strike ended in blood and exception.
                JackPoint::fire(
                    'council.strike.defeat', 
                    $alias, $method, $params, $e, $context, $this
                );

                // The casualty: A general falls in battle, but the council survives.
                $this->report[$alias] = $e;
                $success = false;

            }

            // ─── ACTION: Extraction (After the driver engagement)
            JackPoint::fire(
                'council.strike.after',
                $alias,
                $success,
                $method,
                $params,
                $this->report[$alias],
                $context,
                $this
            );

        }

        // ─── ACTION: The war concludes ──────────────────────────────────────
        JackPoint::fire('council.raid.after', $method, $params, $this->report, $context, $this);

        // ────────────────────────────────────────────────────────────────────
        // ACT V: The Ministry of Historiography (Manipulation before Compiling the History Books)
        // The war is over, but the history of what happened is yet to be written...
        // ────────────────────────────────────────────────────────────────────
        $newReport = JackPoint::transform(
            'council.report',
            $this->report,
            $method, $params, $context, $this
        );

        // Detect if the battle report was rewritten by hidden forces
        if ($this->report !== $newReport) {
            if(JackPoint::judge(
                'council.report.manipulation',
                [
                    $newReport,
                    $this->report,
                    $method,
                    $params,
                    $context,
                    $this
                ]
            ) === true)
                $this->report = $newReport;
        }

        return $this;
    }

    /**
     * Checks if at least one operation failed.
     * @return bool
    */
    public function hasFailures(): bool
    {
        foreach ($this->report as $result) {
            if ($result instanceof Throwable) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets the aliases of all drivers that failed.
     * @return string[]
    */
    public function getFailedAliases(): array
    {
        return array_keys(array_filter($this->report, static fn($r) => $r === null || $r instanceof Throwable));
    }
    
    /**
     * Returns the full battle report.
     * @return array<string, mixed>
    */
    public function getReport(): array
    {
        return $this->report;
    }
}
