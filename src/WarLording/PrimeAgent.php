<?php

namespace KrubiK\WarLording;
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

use KrubiK\Krubot;
use KrubiK\Enums\Platform;
use KrubiK\Helpers\PhantomShell;
use KrubiK\Drivers\Contracts\BotDriverInterface;
use Illuminate\Support\Traits\Macroable;

use Fiber;
use Throwable;
use ReflectionMethod;
use RuntimeException;
use BadMethodCallException;

/**
 * Represents asynchronous task lifecycle states for PrimeAgent nonblocking-tasks.
 */
enum AsyncTaskState: string
{
    case DONE    = 'done';
    case FAILED  = 'failed';
    case RUNNING = 'running';
    case UNKNOWN = 'unknown';

    /**
     * Indicates whether the task reached a terminal state.
     */
    public function isTerminal(): bool
    {
        return $this === self::DONE || $this === self::FAILED;
    }

    /**
     * Indicates whether the task is still in progress.
     */
    public function isActive(): bool
    {
        return $this === self::RUNNING;
    }
}

/**
 * The Prime Agent - v6.4
 *
 * A high-level operative acting as the absolute proxy for the underlying Bot Driver.
 * She possesses the authority to direct engage with any platform protocol (Telegram, Rubika, etc.)
 * and execute platfrom commands with full impunity via her Quartessence Protocol.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
 * 
 * @mixin \KrubiK\Contracts\BotDriverInterface
*/
class PrimeAgent implements BotDriverInterface
{
    // Alias the Macroable __call to preserve it, avoiding fatal overrides.
    use Macroable {
        __call as macroCall;
    }

    /**
     * @var BotDriverInterface The actual driver instance being commanded.
     * The active driver instance currently engaged.
     * And public face of our operation.
    */
    protected readonly BotDriverInterface $driver;

    /**
     * @var bool Dictates the operational mode.
     * Enforces API boundaries or enables deep access.
     * true: The Diplomat (Public API only).
     * false: The Spy (PhantomShell access engaged).
    */
    protected readonly bool $legalMode;

    /**
     * @var PhantomShell|null The PhantomShell alliance for covert operations.
     * Lazy-loaded alliance for performance; null-safe.
     * Forged only when the Agent is in 'Spy Mode' to unlock non-public methods, has inherited cache for maximum performance.
     * This property is the key to our deep-access capabilities.
    */
    protected ?PhantomShell $psAlliance = null;

    /**
     * Lightweight in-memory registry for running fibers.
     * key: taskId, value: Fiber
     *
     * @var array<string, Fiber>
    */
    protected array $fiberRegistry = [];

    /**
     * Optional result cache to read completed task results later.
     * key: taskId, value: mixed
     *
     * @var array<string, mixed>
    */
    protected array $fiberResults = [];

    /**
     * Optional error cache to inspect failed task later.
     * key: taskId, value: Throwable
     *
     * @var array<string, Throwable>
    */
    protected array $fiberErrors = [];

    /**
     * PrimeAgent constructor.
     * Establishes a command link with the provided driver.
     *
     * @param BotDriverInterface $driver The resolved driver to be commanded.
     * @param bool $legalMode When true (default), the Agent operates in 'Legal Mode', strictly respecting the driver's public API contract. When false, it engages 'Spy Mode' via the PhantomShell alliance, granting the ability to invoke protected and private methods. This is a high-privilege mode intended primarily for testing and advanced debugging scenarios.
    */
    private function __construct(BotDriverInterface $driver, bool $legalMode = true)
    {
        $this->driver       = $driver;
        $this->legalMode    = $legalMode;
    }

    /**
     * Engages a tactical protocol via alias, sacred enum, direct instance, or defaults.
     * 
     * Polymorphic entry point with union types for achieve Hyper-DX.
     * Handles string, Enum, Instance, or null gracefully, reducing cognitive load.
     *
     * This command protocol establishes a secure connection through The Quartessence:
     * 1.  **Via Alias (string):** Instructs Krubot Core to resolve and lock onto
     *     the target driver from its central registry.
     * 2.  **Via Sacred Enum (Platform):** The enlightened, type-safe path. The New Standard!
     * 3.  **Via Live Instance (BotDriverInterface):** The path of absolute performance. Bypasses the Core entirely,
     *     engaging directly with a provided, live driver instance for maximum performance.
     * 4.  **Via Default (null):** The path of intuition, automatically engaging the
     *     default platform defined in the krubot config.
     *
     * @param string|Platform|BotDriverInterface|null $target The alias, Platform object, live driver, or null for default driver.
     * @param bool $legalMode If false, enables 'Spy Mode' (calls PhantomShell when needed).
     * @param Krubot|null $warlord The Supreme Commander (Dependency Injection). Allows manual injection of a specific Krubot instance, that enabling Inversion of Control (IoC) for isolated testing or multi-bot dimensions.
     * @return self|null The PrimeAgent, fully engaged and authorized, ready to execute orders on that frequency.
    */
    public static function engage(
        string|Platform|BotDriverInterface|null $target = null,
        bool $legalMode = true,
        ?Krubot $warlord = null
    ): ?self
    {
        // Path 4: The Intuitive Default.
        // If the universe provides a void (null), we fill it with the default creation.
        if ($target === null) {
            $target = Platform::def();
        }

        // Path 3: The Ultimate Performance.
        // A live driver instance bypasses all resolution logic.
        elseif ($target instanceof BotDriverInterface) {
            return new self($target, $legalMode);
        }

        // Paths 1 & 2: The Unified Resolution.
        // Thanks to the 'Stringable' interface, both Platform objects and string
        // aliases can be resolved to a canonical alias with zero friction.
        // Check if the target is a verse-codename (alias) that needs resolution.
        $alias = (string) $target;

        // The Hierarchy of Command & The Summoning Ritual.
        // We prioritize the local Warlord instance (DI) to respect the boundaries of the test/battle/scope.
        // If absent, we summon the Global Krubot Singleton via the 'warlord()' helper.
        // Finally, we query the WarLord's Core Intelligence Network to Resolve the driver and establish the connection.
        $driver = ($warlord ?? warlord())?->core($alias);

        // Connection Established ?
        // Then return the agent in an Active State, Armed with the resolved driver.
        return $driver ? (new self($driver, $legalMode)) : null;
    }

    /**
     * Intercepts and delegates all commands, bypassing visibility restrictions.
     * Gives you Raw Access to Platform API.
     *
     * This is the heart of the Agent's espie4s. It first attempts a direct, public
     * call for maximum performance. If that fails, it uses Reflection to force
     * its way into protected or private methods, ensuring no command is ever denied.
     * 
     * This ensures 100% API compatibility without manual implementation.
     * IDEs will understand the contract via `@mixin`.
     *
     * @param string $method The name of the method to invoke on the target.
     * @param array $parameters The parameters for the method.
     * @return mixed The result from the target driver's method. eg: array for sendMessage()
     * @throws BadMethodCallException if it's not defined or inaccesible.
     * 
     * @mixin \KrubiK\Contracts\BotDriverInterface
    */
    public function __call(string $method, array $parameters)
    {
        // [The Agent's Own Macros]
        // First Check if a macro was injected directly into the PrimeAgent matches this call.
        if (static::hasMacro($method))
            return $this->macroCall($method, $parameters);

        // [here is a Beneficial Sniffing Point for \KrubiK\Helpers\AmethystMatrix or to Inject a RateLimiter logic]

        // Performance optimization: Direct call if it's public and callable – avoids Reflection overhead.
        // This is the fastest path for 99% of calls.
        if (method_exists($this->driver, $method) && is_callable([$this->driver, $method])) {
            // Proxies all commands to the engaged driver with zero latency.
            return $this->driver->{$method}(...$parameters);
        }

        // The Gatekeeper of The Envoy-Law.
        // If Legal Mode is active, we respect the driver's privacy boundaries.
        // Access to hidden methods is STRICTLY DENIED.
        if ($this->legalMode) {
            // return -1; // -1 == Method Can't be Called in LegalMode
            // Or return -1 as you preferred, but Exception is more Standard.
            throw new BadMethodCallException(
               sprintf('Access Denied: Method "%s" is not public and PrimeAgent is in Legal Mode.', $method)
           );
        }

        // Lazy initialization: Only create PhantomShell when needed, reducing memory footprint.
        // The Activation of The Phantom Alliance.
        // If we are here, we are in Spy Mode. The Alliance should already be called3...
        // Let's double-check for absolute safety and performance
        if (!isset($this->psAlliance))
            $this->psAlliance = phantomshell($this->driver);

        // If not public, we engage the arcane arts...
        try {

            // Execute the command from the shadows.
            return $this->psAlliance->{$method}(...$parameters); // Done, sometimes Friendship can make Miracles for us.

        } catch (\Throwable $e) {

            // If the method truly does not exist even in the shadows...
            throw new BadMethodCallException(sprintf(
                'Command "%s" does not exist on driver [%s] (even via PhantomShell).',
                $method, get_class($this->driver)
            ), 0, $e );

        }
    }

    /**
     * Empowers the Agent to act as an executioner closure.
     * Example: $agent('sendMessage', ['text' => 'Boom!']);
    */
    public function __invoke(string $command, mixed ...$args): mixed
    {
        return $this->__call($command, $args);
    }

    /**
     * Async Toolkit for PrimeAgent
     *
     * Features:
     * - async(): fire-and-continue (no waiting)
     * - await(): optional blocking wait
     * - awaitWithTimeout(): controlled wait with timeout
     *
     * Notes:
     * - Fiber is cooperative concurrency (not OS thread).
     * - True non-blocking I/O depends on underlying driver/client architecture.
     * 
     * better to set this things in your php.ini
        opcache.enable=1
        opcache.enable_cli=0
        opcache.jit=1255
        opcache.jit_buffer_size=128M
    */

    /**
     * Engages the command asynchronously without waiting for completion (Non-blocking) via PHP Fibers.
     *
     * @param string $method Driver/agent method to execute.
     * @param array<int, mixed> $parameters Positional arguments.
     * @return string taskId for later await/polling.
     *
     * @throws BadMethodCallException
     */
    public function async(string $method, array $parameters = []): string
    {
        $taskId = bin2hex(random_bytes(16));
        $startedAt = hrtime(true);

        $fiber = new Fiber(function () use ($taskId, $method, $parameters, $startedAt): void {
            try {
                $result = $this->{$method}(...$parameters);
                $this->fiberResults[$taskId] = $result;
            } catch (Throwable $e) {
                $this->fiberErrors[$taskId] = $e;
            } finally {
                // Lightweight telemetry hook (Nanosecond precision).
                $elapsedNs = hrtime(true) - $startedAt;
                // metrics()->timing('prime_agent.async.ns', $elapsedNs, ['method' => $method]);

                // Registry cleanup for terminated fiber to reduce memory pressure.
                unset($this->fiberRegistry[$taskId]);
            }
        });

        $this->fiberRegistry[$taskId] = $fiber;
        $fiber->start(); // fire now, no wait

        return $taskId;
    }

    /**
     * Optional wait for task completion
     * // blocking on current execution flow. //
     * Waits for a fiber completion and returns its final result value.
     *
     * @param string $taskId
     * @return mixed
     *
     * @throws RuntimeException
     * @throws Throwable Re-throws underlying task exception if failed.
     */
    public function await(string $taskId): mixed
    {
        // Fast-path: already completed
        if (array_key_exists($taskId, $this->fiberResults)) {
            $result = $this->fiberResults[$taskId];
            unset($this->fiberResults[$taskId]);
            return $result;
        }

        // Fast-path: already failed
        if (array_key_exists($taskId, $this->fiberErrors)) {
            $e = $this->fiberErrors[$taskId];
            unset($this->fiberErrors[$taskId]);
            throw $e;
        }

        $fiber = $this->fiberRegistry[$taskId] ?? null;
        if (!$fiber) {
            throw new RuntimeException("Task [$taskId] not found or already consumed.");
        }

        // Cooperative progression loop
        while (!$fiber->isTerminated()) {
            if ($fiber->isSuspended()) {
                $fiber->resume();
            }
            // If fiber is running and never suspends, this loop exits when terminated.
            // For heavy loops, micro-sleep can reduce CPU spin:
            // usleep(1000);
        }

        // After termination, result/error should be in caches
        if (array_key_exists($taskId, $this->fiberErrors)) {
            $e = $this->fiberErrors[$taskId];
            unset($this->fiberErrors[$taskId]);
            throw $e;
        }

        if (!array_key_exists($taskId, $this->fiberResults)) {
            throw new RuntimeException("Task [$taskId] terminated with no result payload.");
        }

        $result = $this->fiberResults[$taskId];
        unset($this->fiberResults[$taskId]);

        return $result;
    }

    /**
     * Controlled await with timeout in milliseconds.
     * Throws timeout exception if task is not completed in time.
     *
     * @param string $taskId
     * @param int $timeoutMs
     * @param int $tickUs polling/scheduling tick in microseconds (default 1ms).
     * @param bool $returnNullOnFail set to false if you need detailed exception data to handle; or if you wanna return null really;
     * @return mixed
     *
     * @throws RuntimeException
     * @throws Throwable
     */
    public function awaitWithTimeout(string $taskId, int $timeoutMs = 3000, int $tickUs = 1000, bool $returnNullOnFail = true): mixed
    {
        if ($timeoutMs <= 0) {
            throw new RuntimeException('timeoutMs must be > 0');
        }
        if ($tickUs <= 0) {
            throw new RuntimeException('tickUs must be > 0');
        }

        $deadline = hrtime(true) + ($timeoutMs * 1_000_000); // ms -> ns

        // Fast-paths
        if (array_key_exists($taskId, $this->fiberResults)) {
            $result = $this->fiberResults[$taskId];
            unset($this->fiberResults[$taskId]);
            return $result;
        }
        if (array_key_exists($taskId, $this->fiberErrors)) {
            $e = $this->fiberErrors[$taskId];
            unset($this->fiberErrors[$taskId]);
            if($returnNullOnFail)
                return null;
            throw $e;
        }

        $fiber = $this->fiberRegistry[$taskId] ?? null;
        if (!$fiber) {
            if($returnNullOnFail)
                return null;
            throw new RuntimeException("Task [$taskId] not found or already consumed.");
        }

        while (!$fiber->isTerminated()) {
            if (hrtime(true) >= $deadline) {
                if($returnNullOnFail)
                    return null;
                throw new RuntimeException("Task [$taskId] timed out after {$timeoutMs}ms.");
            }

            if ($fiber->isSuspended()) {
                $fiber->resume();
            }

            // Prevent hot spinning under load.
            usleep($tickUs);
        }

        if (array_key_exists($taskId, $this->fiberErrors)) {
            $e = $this->fiberErrors[$taskId];
            unset($this->fiberErrors[$taskId]);
            if($returnNullOnFail)
                return null;
            throw $e;
        }

        if (!array_key_exists($taskId, $this->fiberResults)) {
            if($returnNullOnFail)
                return null;
            throw new RuntimeException("Task [$taskId] terminated with no result payload.");
        }

        $result = $this->fiberResults[$taskId];
        unset($this->fiberResults[$taskId]);

        return $result;
    }

    /**
     * Optional utility: check task state without blocking.
     *
     * @param string $taskId
     * @return AsyncTaskState
     */
    public function taskState(string $taskId): AsyncTaskState
    {
        if (array_key_exists($taskId, $this->fiberResults)) {
            return AsyncTaskState::DONE;
        }

        if (array_key_exists($taskId, $this->fiberErrors)) {
            return AsyncTaskState::FAILED;
        }

        if (array_key_exists($taskId, $this->fiberRegistry)) {
            return AsyncTaskState::RUNNING;
        }

        return AsyncTaskState::UNKNOWN;
    }
    
    // For perfect IDE auto-completion Intelligence, you would explicitly implement methods.
    // However, __call provides full runtime compatibility.
    // Example of explicit implementation:
    public function reply(string $text): mixed
    {
        return $this->driver->reply($text);
    }

    public function getMe(): array
    {
        return $this->driver->getMe();
    }
}
