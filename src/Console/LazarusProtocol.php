<?php

namespace KrubiK\Console;
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
| What you see here is the **×ReleaseCandidate v0.8×**. Why release it now?
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

use RuntimeException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Cache\Lock as CacheLock;
use Illuminate\Support\Str;
use Throwable;
use Laravel\SerializableClosure\SerializableClosure;
use KrubiK\Helpers\AmethystMatrix; // Import Wise Logger 🔮
use KrubiK\Jobs\FetchDriverUpdates;

use App\Models\User;
use Carbon\Carbon;
use KrubiK\DTOs\LazarusTask;

// Native-Trinity of PHP✸✸Asynchronous
use Fiber;
use Generator;
use Closure;
// The Fates Triumvirate
use SplQueue;
use SplPriorityQueue;
use SplDoublyLinkedList;

use KrubiK\DivineMessageSender\Jobs\SendDivineMessageJob;
use KrubiK\DivineMessageSender\Models\DivineDispatchQueue;

/**
 * ⚰️ THE LAZARUS PROTOCOL: ASCENDED SOVEREIGN EDITION (v8.0 Final / Fiber-Fusion / Spl-Skeleton / Pulse-Merged)
 *
 * "That is not dead which can eternal lie,
 *  And with strange aeons even death may die."
 *
 * This is the ULTIMATE Daemon, a digital lifeform. Designed with the core philosophy of
 * achieving perpetual uptime not by resisting termination, but by commanding it.
 * It fuses the v8 cooperative multitasking core (Fiber && **The Fates Triumvirate**) with the
 * non-duplicative CNS powers of KrubiKPulse, orchestrating its own unbroken chain of existence.
 * 
 * "Did I not tell you that if you believed, you would see the glory of God?"
 *                     -∂ GreatChrist ‖John, 11:40‖
 *
 * 🧬 LAZARUS DNA Analysis:
 * - Role: KrubiK Enterprise "Immortal" Power Engine (Planner + Poller + Worker + Guardian + Divine Dispatcher)
 * - **Existential Mandate:** Achieves immortality not by resisting termination, but by commanding it as a scheduled, strategic "Maintenance Cycle" for rebirth, so achieves perpetual uptime, through a controlled, unbroken chain of self-reincarnation.
 * - Stealth: Pcntl / Exec / Passthru adaptability (Anti-CageFS)
 * - Brain: Config-Driven Hybrid Logic (Polling vs Webhook Support)
 * - Heart: Atomic Locking via MariaDB/Redis (Advisory Locks supported)
 * - Optimization: Smart DB Pinging Protocol (90% Less Overhead)
 * - KrubiK-Pulse Powers: MessagePlanner + Divine DB Dispatch + Smart Lock
 * - Nervous System: Cooperative Multitasking (Utilizing Fibers, an O(1) SplQueue backpressure & PHP Generators)
 * - Akashic Memory (The Covenant): Utilizes the todo() subsystem to persist obligations across process death. Tasks are etched into a database covenant, creating a memory that transcends the lifespan of any single reincarnation.
 * - **The Fates Triumvirate:** A hyper-specialized nervous system core featuring a triad of data structures for ultimate asynchronous sovereignty:
 *     - `SplPriorityQueue` (Urgency/Immediate Tasks)
 *     - `SplDoublyLinkedList` (Backpressure & Load Shedding)
 *     - `SplPriorityQueue` as Min-Heap (Chronological (O(log n)) Future Tasks)
 * - @see https://en.wikipedia.org/wiki/Lazarus_of_Bethany
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class LazarusProtocol extends Command
{
    /**
     * The Signature.
     * We kept ALL options to maintain backward compatibility and full control.
     */
    protected $signature = 'krubik:lazarus
                            {--driver=rubika : The target driver alias (Standard Protocol)}
                            {--tag=primary : Unique instance ID (Sector Control)}
                            {--stealth : Force PID reset via exec (Invisibility Mode for cPanel)}
                            {--force : Ignore locks (Breach Protocol)}
                            {--use-modern-queue=true : Use priorityQueue + deferredTasks + todoHeap for maximum power, speed, performance and accuracy}';

    protected $description = 'The Immortal Phoenix. Orchestrates self-resurrection, fetching, divine dispatch, and processing.';

    // ⚙️ VITAL SIGNS CONFIGURATION (Optimized for Shared Hosting & MariaDB 11.x, You may want to Change Them Sometimes)
    protected const MAX_LIFE_SECONDS = 110;   // 10s buffer before PHP max_execution_time
    protected const MAX_RAM_MB       = 128;   // Memory Ceiling
    protected const LOOP_DELAY_MS    = 1000;  // 1s Throttle (Aggressive but safe)
    protected const DB_PING_INTERVAL = 10;    // Check DB connection every 10 loops (Performance Boost)
    protected const GCC_INTERVAL     = 500;   // Defines the frequency (in ticks) for manual Garbage-Collection to prevent CPU starvation and ensure stability.
    
    // 🧹 HYGIENE CONFIGURATION
    protected const MAX_LOOPS_BEFORE_REBIRTH = 5000; // The "Clean Slate" Protocol

    /**
     * Defines the maximum number of deferred tasks to "awaken" and promote to the active queue in a single scheduler tick.
     *
     * Hint: This acts as a throttle or a "batch size" to prevent a "retry storm" where
     * a large number of waiting tasks could suddenly flood the priority queue,
     * starving new, potentially higher-priority, incoming tasks.
     *
     * @var int
    */
    private const AWAKENING_BATCH_SIZE = 4;

    // 🚦 SCHEDULER PRIORITIES
    private const PRIORITY_IMPERATIVE = 1; // Unstoppable, must run NOW (Kill Switch)
    private const PRIORITY_CRITICAL = 5;   // Critical for survival (Lock Refresh, DB Ping)
    private const PRIORITY_HIGH = 10;      // Main work tasks (Queue Processing, Planner)
    private const PRIORITY_NORMAL = 15;    // Routine checks
    private const PRIORITY_LOW = 20;       // Background/Idle tasks (Sleep)

    /**
     * How far ahead (seconds) to hydrate DB todos into the in-memory Spl heap.
     * Keep this >= MAX_LIFE_SECONDS so one incarnation can see everything
     * that may fire before the next rebirth. Tune on weak hosts if needed.
     *
     * @var int The time window in minutes to load future todos from DB on startup.
    */
    private const TODO_LOAD_WINDOW_TTL = self::MAX_LIFE_SECONDS;

    /** Hard cap per SELECT — overdue backlog is drained across loops, not in one shot. */
    private const TODO_HYDRATE_BATCH_LIMIT = 250;

    /** Re-read DB every N loops so HTTP/Job-scheduled todos enter the hot window. */
    private const TODO_HYDRATE_EVERY_LOOPS = self::DB_PING_INTERVAL;

    /**
     * The name of the database table used to persist todo tasks.
     *
     * @var string
     */
    protected string $todoTableName;


    /** @var array<string, true> ids currently sitting in todoHeap (O(1) membership) */
    private array $todoIndex = [];

    /** status: 0=pending, 1=claimed/running, 3=failed */
    private const TODO_STATUS_PENDING = 0;
    private const TODO_STATUS_RUNNING = 1;
    private const TODO_STATUS_FAILED  = 3;

    /** 
     * @var array<string, true> 
     * A lookup table for lazily cancelled todo IDs. O(1) complexity for cancellation.
    */
    private array $cancelledTodos = [];

    // 🔒 STATE VARIABLES
    protected int $bornAt;
    protected string $lockKey;
    protected int $loopCounter = 0;

    // ⚡ COOPERATIVE ENGINE CONFIGURATION (Fiber / SplQueue / Generator)
    protected const MAX_INTERNAL_QUEUE = 500;     // Backpressure ceiling for SplQueue
    protected const FIBER_STEPS_PER_TICK = 16;    // Max cooperative steps per loop tick
    protected const QUEUE_BURST_BUDGET = 25;      // Max jobs per Artisan drain burst
    protected const MICRO_IDLE_US = 50_000;       // 50ms when work exists (anti tight-loop)
    protected const IDLE_SLICE_MS = 100;          // Slice full idle into responsive windows
    protected const DIVINE_BATCH_LIMIT = 50;      // Safety batch for Divine DB drain (Pulse DNA)

    /** Runtime accept flag — frozen on lock-loss to prevent split-brain work. */
    protected bool $acceptingWork = true;

    /**
     * High-level internal work buffer (O(1) enqueue/dequeue).
     * Units: fetch | divine_plan | divine_dispatch | queue_drain
     *
     * @var SplQueue<array{type:string,payload:mixed,ts:int}>
     */
    protected SplQueue $taskQueue;

    protected bool $useModernQueue = true;

    /**
     * The main task queue. Tasks ready for execution are stored here.
     * Higher priority (lower number) tasks are executed first.
     * @var SplPriorityQueue<int, Fiber>
     */
    private SplPriorityQueue $priorityQueue;

    /**
     * A list for tasks that are waiting for a specific condition (e.g., time) to be met.
     * Each element is an array: [Fiber $fiber, int $wakeUpTimestamp].
     * @var SplDoublyLinkedList<array>
     */
    private SplDoublyLinkedList $deferredTasks;

    /** @var SplPriorityQueue<float, array{id:string,due:float,what:callable,how:array|\Closure|null}>
     * A dedicated min-heap for scheduled one-shot tasks, ordered by due time.
     * This must be separate from the main priorityQueue, which is ordered by task priority.
    */
    private SplPriorityQueue $todoHeap;

    /** @var list<\Fiber<mixed,mixed,mixed,mixed>> */
    protected array $fibers = [];

    /** Burst counter for lightweight/cooperative queue drains. */
    protected int $processedThisBurst = 0;

    private CacheLock $lock;
    private bool $shouldStop = false;

    /**
     * The Entry Point.
    */
    public function handle(): int
    {
        // returns true when `php artisan down` is activated
        if (app()->isDownForMaintenance()) {
            $this->info("Maintenance mode activated. Dying peacefully...");
            return self::SUCCESS; // یا break;
            // نکته مهم: اینجا دیگر reincarnate را صدا نمی‌زند و زنجیره قطع می‌شود.
        }

        // --- [ دژبان ورودی: چک کردن مجوز فعالیت ] ---
    
        // خواندن وضعیت از کانفیگ (که از .env می‌خواند)
        $isLazarusEnabled = config('krubot.lazarus.enabled', true);

        if ($isLazarusEnabled === false) {
            // اگر غیرفعال بود، فقط یک پیام در لاگ می‌گذارد و تمام.
            // هیچ حلقه‌ای شروع نمی‌شود، هیچ فرزندی متولد نمی‌شود.
            $this->warn("⛔ Lazarus is DISABLED in config. Resting...");
            return self::SUCCESS; // خروج موفقیت‌آمیز
            
            // نکته: چون اینجا return می‌کنیم، به کد reincarnate پایین هرگز نمی‌رسد.
            // زنجیره مرگ و زندگی اینجا قطع می‌شود.
        }

        // 1. Initialization & DNA Check
        $this->bornAt = time();
        $driver = $this->option('driver');
        $tag = $this->option('tag');
        $isStealth = $this->option('stealth');

        // Initialize the table name from configuration
        $this->todoTableName = config('krubot.lazarus.todo_table_name', 'lazarus_todos');

        // Cooperative runtime structures (must exist before first tick)
        $this->taskQueue = new SplQueue();

        $useModernQueue = filter_var(
            $this->option('use-modern-queue'), 
            FILTER_VALIDATE_BOOLEAN, 
            FILTER_NULL_ON_FAILURE
        ) ?? true;
        $this->useModernQueue = $useModernQueue;

        if ($useModernQueue) {
            $this->initializeScheduler(); // priorityQueue + deferredTasks + todoHeap را می‌سازد
        }

        $this->fibers = [];
        $this->acceptingWork = true;

        $secretKey = config('krubot.lazarus.todo-secret', null);
        if($secretKey !== null)
            SerializableClosure::setSecretKey(
                $secretKey
            );        

        // 🔒 ATOMIC LOCKING (MariaDB Optimized)
        // We use Cache::lock to guarantee SINGLE INSTANCE execution per tag.
        // On MariaDB, ensure your cache driver is 'database' or 'redis' for true Atomic Locks (GET_LOCK).
        $this->lockKey = "krubik:lazarus_lock:{$tag}";
        
        // 🔒 Bind atomic lock to class property to allow lockRefreshLoop access
        // 60s TTL allows the lock to auto-expire if the server crashes hard.
        $this->lock = Cache::lock($this->lockKey, 60);

        // 2. Overlap Protection (Atomic Sentry)
        if (!$this->option('force') && !$this->lock->get()) {
            // Silent exit is preferred for overlapping cron runs.
            // $this->warn("⚠️  Sector [{$tag}] occupied. Protocol Standing down.");
            return self::SUCCESS;
        }

        // Display Operational Mode (Cyberpunk Style)
        $mode = $isStealth ? '👻 STEALTH (Exec/NewPID)' : '⚡ SPEED (Pcntl/SamePID)';
        $this->info("🔥 Lazarus v8 Ascended Sovereign Online. Tag: [{$tag}] | Mode: {$mode} | PID: " . getmypid());

        // Re-config php on every request
        /// @set_time_limit(self::MAX_LIFE_SECONDS + 15);
        /// @ini_set('memory_limit', '128M');
        
        // 3. Fail-Safe: The Emergency Parachute
        register_shutdown_function(fn() => $this->handleShutdown($driver, $tag, $isStealth));

        // 4. The Loop of Eternity 🌌
        try {

            // Lazarus Configurable Heartbeat
            $interval = config('krubot.lazarus.interval', self::LOOP_DELAY_MS);

            while (true) {
                
                // [A] ANTI-DISCONNECT PROTOCOL 🔌
                // Optimized: Only ping DB every N loops to save IOPS on MariaDB.
                if ($this->loopCounter % self::DB_PING_INTERVAL === 0) {
                    $this->ensureDatabaseConnection();
                }

                // [C] CORE EXECUTION (The Hybrid Engine) 🫀
                // Lazarus Main Operations Here ::
                try {
                    // C-1. Clear FileSystem Cache (Essential for config/log consistency)
                    clearstatcache();
                    
                    // C-2. THE HUNTER: Fetch Updates (Conditional)
                    // This is controlled by the Config Switch.
                    /**** old_kond_SYNCED_mod
                    if (config('krubot.polling.enabled', true)) {
                        // FetchRubikaUpdates::dispatchSync();

                        // 🎯 TARGETED POLLING STRATEGY
                        // Iterate through active drivers defined in config.
                        $targets = config('krubot.polling.drivers', ['rubika']);
                        
                        foreach ($targets as $targetDriver) {
                            FetchDriverUpdates::dispatchSync($targetDriver);
                        }
                    }
                    *****/

                    if ($this->acceptingWork) {
                        // C-1.5 THE BRAIN CLOCK (Pulse DNA / non-duplicative):
                        // Self-managed planner trigger every ~30 minutes via expiring lock.
                        // این متد چک می‌کند اگر ۳۰ دقیقه گذشته باشد، پلنر را enqueue می‌کند
                        if (config('krubot.divine_sender.enabled', true)) {
                            $this->enqueueDivinePlannerIfDue();
                        }

                        // C-2. THE HUNTER: Fetch Updates (Conditional) — Generator produced
                        // This is controlled by the Config Switch.
                        if (config('krubot.polling.enabled', true)) {
                            // FetchRubikaUpdates::dispatchSync();

                            // 🎯 TARGETED POLLING STRATEGY
                            // Iterate through active drivers defined in config (lazy Generator).
                            // $targets = config('krubot.polling.drivers', ['rubika']);
                            // foreach ($targets as $targetDriver) {
                            //      FetchDriverUpdates::dispatchSync($targetDriver);
                            // }
                            foreach ($this->driverTargetGenerator() as $targetDriver) {
                                $this->enqueueTask('fetch', $targetDriver);
                            }
                        }

                        // C-2.5 THE SOUL (Pulse DNA / non-duplicative):
                        // Divine DB queue drain signal (transactional dispatch happens in Fiber).
                        // تنها زمانی enqueue شود که فیچر در کانفیگ روشن باشد
                        if (config('krubot.divine_sender.enabled', true)) {
                            $this->enqueueTask('divine_dispatch', null);
                        }
                    
                        // C-3. THE DEVOURER: Process The Queue (Always On)
                        // Even if polling is OFF (Webhook Mode), we MUST process the queue.
                        // This replaces the need for a separate 'queue:work' daemon.
                        // Enqueue drain unit; Fiber consumer executes processQueue().
                        $this->enqueueTask('queue_drain', null);

                        if ($this->useModernQueue) {

                            // Loop 0 already hydrated in initializeScheduler(); then every N loops.
                            if ($this->loopCounter > 0
                                && $this->loopCounter % self::TODO_HYDRATE_EVERY_LOOPS === 0
                            ) {
                                $this->hydrateFromMatter();
                            }

                            $this->processDueTodos();

                            // Promote deferred tasks back into the priority queue before ticking fibers
                            $this->awakenDeferredQueue(self::AWAKENING_BATCH_SIZE);
                        }

                        // C-4. Cooperative scheduler tick (Fiber multiplex)
                        $this->tickFibers(self::FIBER_STEPS_PER_TICK);
                    }
                    
                    // C-3. THE DEVOURER: Process The Queue (Always On)
                    // Even if polling is OFF (Webhook Mode), we MUST process the queue.
                    // This replaces the need for a separate 'queue:work' daemon.
                    /////// $this->processQueue(); // Good-bye Sync Lazarus...
                    
                } catch (\Throwable $e) {
                    // Mission Priority: Survival. Log and Continue.
                    AmethystMatrix::yell("Lazarus Operation Failed [{$tag}]: " . $e->getMessage());
                    
                    // Force DB reconnect on exception, just in case that was the cause.
                    $this->ensureDatabaseConnection(true);
                }

                // =========================================================
                // ✨ SECTION D: Phoenix-Mode / Scheduled Hygiene (The Counter Check)
                // =========================================================
                $this->loopCounter++;
                
                // Check 1: The Loop Limit (Prevent subtle fragmentation)
                if ($this->loopCounter >= self::MAX_LOOPS_BEFORE_REBIRTH) {
                    $this->info("♻️ Scheduled Rebirth (Loop Limit Reached).");
                    $this->drainTaskQueueGracefully(200);
                    $this->reincarnate($driver, $tag, $isStealth, $this->useModernQueue); // 🧪☣
                    break;
                }

                // Check 2: Vital Signs (RAM & Time limit)
                /*if ($this->shouldReincarnate()) {
                    $this->drainTaskQueueGracefully(200);
                    $this->reincarnate($driver, $tag, $isStealth, $this->useModernQueue); // 🧪☣
                    break;
                }*/
                // =========================================================

                // [E] TACTICAL PAUSE (Configurable Heartbeat)
                /// usleep($interval * 1000);
                $this->cooperativeIdle((int) $interval);
            }
        } finally {
            // Polite cleanup
            ///optional($this->lock)->release();

            // Use the property $this->lock instead, with a null-safe check.
            if (isset($this->lock)) {
                $this->lock->release();
            }
        }

        return self::SUCCESS;
    }

    /**
     * Checks for the cache-based kill switch and terminates if found.
     * @return bool Returns true if termination signal was received.
     */
    private function checkKillSwitch(): bool
    {
        // --- [ بخش جدید: دکمه مرگ ] ---
        // چک کردن کش برای دستور قتل
        // [0] KILL SWITCH PROTOCOL 💀 [0]
        $killSwitchKey = config('krubot.lazarus.kill-kommand', 'krubik:kill-lazarus');
        if (Cache::has($killSwitchKey)) {
            $this->warn("💀 Modern-Queue / Cache-based Kill Switch Detected & Activated! I am dying voluntarily... Bye.");

            // پاک کردن کلید تا اگر بعدا خواستیم روشن کنیم، دوباره نمیرد
            Cache::forget($killSwitchKey);
            $this->acceptingWork = false; // Stop accepting new work
            $this->shouldStop = true;    // Signal main loop to stop
            return true; // مرگ فوری و تمیز
        }
        return false;
    }

    /**
     * Refreshes the instance lock or terminates if the lock is lost.
     */
    private function refreshLockOrFail(): void
    {
        // REFRESH LOCK 🛡️
        // We re-acquire (extend) the lock to prove we are still alive.
        // If we lose the lock here, we must abort to prevent split-brain.
        if (!$this->lock->get()) {
            $this->acceptingWork = false; // Freeze producers/consumers immediately
            $this->shouldStop = true;
            AmethystMatrix::yell("Lazarus [{$this->option('tag')}]: Lock lost during modern cycle! Aborting.");
        } else {
            // Attempt to refresh the lock to extend its lifetime
            $this->lock->refresh();
        }
    }

    /**
     * Checks reincarnation conditions and initiates rebirth if necessary.
     * @return bool Returns true if reincarnation was initiated.
     */
    private function checkAndReincarnate(): bool
    {
        if ($this->shouldReincarnate()) {
            $this->info("♻️ Scheduled Rebirth triggered from modern scheduler.");
            $this->drainTaskQueueGracefully(200);
            $this->reincarnate(
                $this->option('driver'),
                $this->option('tag'),
                $this->option('stealth'),
                $this->useModernQueue
            );
            $this->shouldStop = true; // Signal main loop to stop
            return true;
        }
        return false;
    }

    /**
     * 🧬 GENERATOR: Lazy driver discovery with SplQueue backpressure.
     * Yields one driver alias at a time. Side-effect free discovery only.
     *
     * @return Generator<int, string, mixed, void>
     */
    private function driverTargetGenerator(): Generator
    {
        // 🎯 TARGETED POLLING STRATEGY
        // Iterate through active drivers defined in config.
        $targets = config('krubot.polling.drivers', ['rubika']);

        foreach ($targets as $targetDriver) {
            // Backpressure: stop discovering if internal queue is saturated
            $currentSize = $this->useModernQueue
                ? $this->priorityQueue->count()
                : $this->taskQueue->count();

            if ($currentSize >= self::MAX_INTERNAL_QUEUE) {
                return;
            }

            yield (string) $targetDriver;
        }
    }
    
    /**
     * The new, unified method for core lifecycle and health checks.
     * This method encapsulates the critical survival checks into a single, high-priority task.
     * It returns true if the process should stop, otherwise false.
     *
     * @return bool
    */
    private function performHyperDXHeartbeatCheck(): bool
    {
        // This is the Double-DX implementation, combining three critical checks.
        // The order is intentional:
        // 1. Kill Switch: An external command to stop immediately.
        // 2. Lock Loss: In a distributed system, losing the lock means another instance is active.
        // 3. Reincarnation: Self-monitoring for age or resource limits.

        if ($this->checkKillSwitch()) {
            return true;
        }

        $this->refreshLockOrFail();
        if ($this->shouldStop) { // refreshLockOrFail sets this flag on failure
            return true;
        }

        if ($this->checkAndReincarnate()) {
            return true;
        }

        return false;
    }

    private function taskGenerator(): Generator
    {
        // Imperative Task: The Kill Switch
        // PRIORITY_IMPERATIVE: This must be checked first, above all else.
        yield [
            fn() => $this->checkKillSwitch(), 
            self::PRIORITY_IMPERATIVE
        ];

        // Critical Tasks: Survival Instincts
        // PRIORITY_CRITICAL: These are essential for survival and stability.
        yield [
            fn() => $this->refreshLockOrFail(), 
            self::PRIORITY_CRITICAL
        ];
        yield [
            fn() => $this->checkAndReincarnate(), 
            self::PRIORITY_CRITICAL
        ];

        // High Priority Tasks: The Main Purpose
        yield [
            fn() => $this->enqueueDivinePlannerIfDue(), 
            self::PRIORITY_HIGH
        ];
        yield [
            fn() => $this->processQueue(), // We Handle Platfrom Messages Here 
            self::PRIORITY_HIGH
        ];
    }

    private function initializeScheduler(): void
    {
        $this->priorityQueue = new SplPriorityQueue();
        $this->priorityQueue->setExtractFlags(SplPriorityQueue::EXTR_DATA);

        $this->deferredTasks = new SplDoublyLinkedList();
        $this->deferredTasks->setIteratorMode(
            SplDoublyLinkedList::IT_MODE_FIFO | SplDoublyLinkedList::IT_MODE_KEEP
        );

        // Dedicated min-heap by due-time. Do NOT reuse $this->priorityQueue:
        // that structure is task-priority ordered, so a future high-priority
        // item would hide an already-due low-priority item.
        $this->todoHeap = new SplPriorityQueue();
        $this->todoHeap->setExtractFlags(SplPriorityQueue::EXTR_DATA);

        // Load persistent todos from the database on startup.
        $this->todoIndex = [];
        $this->recoverOrphanedTodoClaims(); // power-loss: status=1 → 0
        $this->hydrateFromMatter();        // hot window + overdue batch

        // Use the generator to create tasks and enqueue them with their defined priority.
        foreach ($this->taskGenerator() as [$task, $priority]) {
            $this->enqueueModernTask($task, $priority);
        }
    }

    /**
     * After a hard kill / power loss, in-flight rows stay status=1 forever.
     * Reset them once per incarnation so they re-enter the pending set.
     * Do NOT call this from the periodic hydrate (would steal in-flight work).
    */
    private function recoverOrphanedTodoClaims(): void
    {
        try {
            DB::table($this->todoTableName)
                ->where('status', self::TODO_STATUS_RUNNING)
                ->update(['status' => self::TODO_STATUS_PENDING]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Loads todos from the database that are due within the configured time window.
     * 
     * This critical lifecycle method "wakes up" the scheduler by populating its
     * in-memory, time-ordered min-heap with tasks from the database, after a restart.
     * 
     * Acts as a SpaceTime bridge between the persisted world (database) and the live,
     * in-memory world, summoning pending persistent tasks that fall 
     * within our active chronological window. It elegantly reconstructs serialized
     * closures and payloads, orchestrating them into a high-octane Min-Heap 
     * based on their precise temporal signatures (-$dueTs).
     *
     * @return void
     * @throws \Throwable Caught and reported internally to prevent spacetime collapse.
    */
    private function hydrateFromMatter(): void
    {
        // As requested, load todos from the near future to keep the in-memory
        // heap small and performant. The window is configurable.
        $loadWindow = self::TODO_LOAD_WINDOW_TTL;
        $dueBefore = now()->addSeconds($loadWindow);
        
        /// $this->info("🧠 Loading persisted todos due in the next {$loadWindow} seconds...");

        // We only select pending tasks (status = 0).
        // This query fetches tasks that are either already due or will be due very soon.
        $query = DB::table($this->todoTableName)
            ->where('status', self::TODO_STATUS_PENDING)
            ->where('due_at', '<=', $dueBefore)
            ->orderBy('due_at', 'asc')
            ->limit(self::TODO_HYDRATE_BATCH_LIMIT);

        // Skip rows already sitting in this process's heap.
        if ($this->todoIndex !== []) {
            $query->whereNotIn('id', array_keys($this->todoIndex));
        }
        $todos = $query->get();
            
        if ($todos->isEmpty()) {
            /// $this->info("✅ No imminent todos found in persistent storage.");
            return;
        }

        $decodeDepth = (int) config('krubot.lazarus.json_decode_depth', 2048);    

        foreach ($todos as $todo) {

            $id = (string) $todo->id;
    
            if (isset($this->todoIndex[$id], $this->cancelledTodos[$id])) {
                continue;
            }

            try {
                $payload = json_decode(
                    $todo->payload,
                    true,
                    $decodeDepth,
                    JSON_THROW_ON_ERROR
                );
    
                // Resurrect 'what'
                $what = $payload['what_type'] === 'closure'
                    ? unserialize($payload['what'])->getClosure()
                    : $payload['what'];
    
                // Resurrect 'how'
                $how = null;
                if ($payload['how_type'] === 'closure') {
                    $how = unserialize($payload['how'])->getClosure();
                } else { // 'array' or 'null'
                    $how = $payload['how'];
                }

                // Keep the same microsecond domain as todo() / processDueTodos().
                $dueTs = (float) Carbon::parse($todo->due_at)->format('U.u');

                $this->todoHeap->insert(

                    [
                        'id'   => $todo->id,
                        'due'  => $dueTs,
                        'what' => $what,
                        'how'  => $how,
                    ],

                    // The priority in the heap is the negative timestamp.
                    // This makes the SplPriorityQueue behave like a Min-Heap.
                    -$dueTs
                );

                $this->todoIndex[$id] = true;
            } catch (\Throwable $e) {
                report($e);
            }
        }
        
        /// $this->info("🚀 Loaded {$todos->count()} todos into the memory heap.");
    }

    /**
     * Promotes Ready tasks from the deferred queue to the main priority queue.
     *
     * This method acts as the gatekeeper between tasks that are waiting for a specific time
     * or condition (deferred) and tasks that are ready to be executed (in the priority queue).
     * It intelligently manages backpressure by respecting a budget and the available capacity
     * of the priority queue, preventing the system from being overwhelmed. This is a critical
    * component for ensuring fairness and stability in an asynchronous task scheduler.
     *
     * @param int  $budget       The maximum number of tasks to promote in this cycle. This is a form of
     *                           throttling to prevent "retry storms" and to allow new, higher-priority
     *                           work to be processed.
     * @param bool $flushOnIdle  When true, ignores the 'wake_at' time of tasks and promotes them all
     *                           aggressively as long as there is budget. This is typically used
     *                           during idle periods of the event loop to clear out the deferred queue.
     *
     * @return void
     */
    private function awakenDeferredQueue(int $budget, bool $flushOnIdle = false): void
    {
        // [Optimization]: Early exit if there's no work to be done.
        // This is a cheap check that avoids unnecessary work like getting the current time
        // or instantiating a new list.
        if ($this->deferredTasks->isEmpty()) {
            return;
        }
        
        // --- Capacity & Budget Management ---
        // Calculate the number of "slots" available in the main execution queue.
        // This is a crucial backpressure mechanism. We must not promote more tasks
        // than the priority queue can handle, regardless of our budget.
        $availableSlots = self::MAX_INTERNAL_QUEUE - $this->priorityQueue->count();
    
        // The actual number of tasks we are allowed to promote is the *minimum* of our
        // external budget and the internal capacity. This honors both constraints.
        $promotionLimit = min($budget, $availableSlots);
    
        // [Optimization]: Another early exit. If we have no budget or no room,
        // there's absolutely nothing to do.
        if ($promotionLimit <= 0) {
            return;
        }
        
        // --- Initialization ---
        // Capture the current high-resolution timestamp ONCE.
        // Doing this outside the loop is a micro-optimization that prevents repeated
        // system calls and ensures all tasks in this cycle are judged against the same "now".
        $now = microtime(true);
    
        // This will be our new deferred tasks list. We build this new list and then swap it
        // at the end, which is an atomic-like operation that is cleaner and more
        // efficient than trying to remove items from the middle of the list we're iterating.
        $keptTasks = new SplDoublyLinkedList();
    
        // Set iterator mode for consistency, although we are using shift/push which
        // are not affected by this mode. It's a good practice for code clarity.
        $keptTasks->setIteratorMode(
            SplDoublyLinkedList::IT_MODE_FIFO | SplDoublyLinkedList::IT_MODE_KEEP
        );
    
        // --- Main Promotion Loop ---
        // This loop is the heart of the function. It processes tasks one by one from the
        // front of the deferred queue until the promotion budget is exhausted.
        // Using `shift()` on an SplDoublyLinkedList is an O(1) operation, making this
        // a highly efficient way to process a FIFO queue.
        while ($promotionLimit > 0 && !$this->deferredTasks->isEmpty()) {
            // Dequeue the next task from the front.
            $item = $this->deferredTasks->shift();
    
            // --- Polymorphic Task Evaluation ---
            // A task can be either a Fiber or an array. We need to handle both cases.
            $isReady  = false;
            $priority = self::PRIORITY_NORMAL; // Default priority.
            $payload  = null;                 // The actual callable/Fiber to be enqueued.
    
            if ($item instanceof Fiber) {
                // [Business Logic]: Fibers are special. They represent suspended computations.
                // When a Fiber is in the deferred queue, it's considered a "retry" attempt.
                // We always consider them ready for re-scheduling, ignoring any time-based constraints.
                $isReady = true;
                $payload = $item;
                // Fibers typically run at a standard priority for retries.
            } else {
                // [Business Logic]: For standard array-based tasks, readiness depends on time.
                // Use null-coalescing for robust access to the wake-up time. Fallback to
                // 'deferred_at' or 0 prevents errors if the task data is malformed.
                $wakeAt = (float) ($item['wake_at'] ?? $item['deferred_at'] ?? 0);
    
                // A task is ready if:
                // 1. The system is idle and we're explicitly flushing (`$flushOnIdle`).
                // 2. Its scheduled wake-up time has passed.
                if ($flushOnIdle || ($wakeAt <= $now)) {
                    $isReady  = true;
                    // The actual executable payload is the 'callable' key. Fallback to the item itself.
                    $payload  = $item['callable'] ?? $item;
                    $priority = (int) ($item['priority'] ?? self::PRIORITY_NORMAL);
                }
            }
    
            // --- Action: Promote or Keep ---
            if ($isReady) {
                // The task is ready! Promote it to the main priority queue for execution.
                // Note: We use a negative priority because SplPriorityQueue is a max-heap,
                // so a higher priority number means it gets dequeued sooner. By negating our
                // logical priority, we align with the max-heap's behavior.
                $this->priorityQueue->insert($payload, -$priority);
                
                // Consume one unit from our promotion budget.
                $promotionLimit--;
            } else {
                // The task is not ready yet (its time has not come).
                // Push it onto our new 'kept' list to be reconsidered in a future cycle.
                $keptTasks->push($item);
            }
        }
    
        // --- Finalization & Cleanup ---
        // [Performance Optimization]: If the promotion loop ended because the `$promotionLimit`
        // was exhausted, there might be tasks left in the original `$deferredTasks` list.
        // Instead of iterating over them, we can bulk-move them far more efficiently.
        // This `while` loop drains the remainder of the old list and appends it to the new one.
        // This is significantly faster than the `for` loop approach for large lists.
        while (!$this->deferredTasks->isEmpty()) {
            $keptTasks->push($this->deferredTasks->shift());
        }
    
        // [Atomicity]: Atomically replace the old deferred tasks list with our newly constructed one.
        // This single assignment is thread-safe (in a non-preemptive context like this) and ensures
        // the system state remains consistent.
        $this->deferredTasks = $keptTasks;
    }
    

    /**
     * Insert a callable into the priority queue with the given priority.
     * Lower numeric value = higher urgency (negated on insert).
    */
    private function enqueueModernTask(callable $task, int $priority, float $delay = 0.0): void
    {
        if (!$this->acceptingWork) return;

        if ($this->priorityQueue->count() >= self::MAX_INTERNAL_QUEUE) {
            // Priority queue is full — push to back of deferred list
            // instead of silently dropping the work.
            $now = microtime(true);
            $this->deferTask($task, $priority, $delay, 'queue_full');
            return;
        }

        $this->priorityQueue->insert($task, -$priority);
    }

    /**
     * Push a callable onto the deferred list for later promotion.
     *
     * Pass $front = true to put it at the head of the deferred list
     * (e.g. a fiber that was interrupted mid-work and must be retried
     * before any newly deferred items).
    */
    private function deferTask(callable $task, int $priority, float $delay = 0.0, string $reason = 'manual', bool $front = false): void
    {
        $now = microtime(true) + $delay;
        $entry = [
            'callable'    => $task,
            'priority'    => $priority,
            'deferred_at' => $now,
            'wake_at'     => $now,
            'reason'      => $reason,
        ];

        if ($front) {
            $this->deferredTasks->unshift($entry); // Head insertion — SplDoublyLinkedList power
        } else {
            $this->deferredTasks->push($entry);    // Tail insertion
        }
    }

    /**
     * Enqueue a high-level work unit into SplQueue (O(1)).
     * Drops gracefully under backpressure to protect RAM before rebirth threshold.
     */
    private function enqueueTask(string $type, mixed $payload): void
    {
        if (!$this->acceptingWork) {
            return;
        }

        $currentSize = $this->useModernQueue
            ? $this->priorityQueue->count()
            : $this->taskQueue->count();

        if ($currentSize >= self::MAX_INTERNAL_QUEUE) {
            return;
        }

        if ($this->useModernQueue) {

            // Map task type to priority for modern queue
            $priority = match ($type) {
                'fetch'          => self::PRIORITY_HIGH,
                'divine_plan'    => self::PRIORITY_HIGH,
                'divine_dispatch'=> self::PRIORITY_NORMAL,
                'queue_drain'    => self::PRIORITY_NORMAL,
                'todo_callback'  => self::PRIORITY_CRITICAL,
                default          => self::PRIORITY_LOW,
            };

            $callable = function () use ($type, $payload) {
                $this->executeTaskByType($type, $payload);
            };

            if ($currentSize >= self::MAX_INTERNAL_QUEUE) {
                // Prefer survival over unbounded growth
                
                // Instead of silently dropping: defer it for next flush cycle
                $this->deferTask($callable, $priority, 0.0, 'enqueue_overflow');
                return;
            }

            // Insert as Fiber into priorityQueue for modern path
            $fiber = new Fiber($callable);

            // We use the priority as the value to sort by in the queue.
            $this->priorityQueue->insert($fiber, -$priority); // lower number = higher priority
        }
        else {

            if ($currentSize >= self::MAX_INTERNAL_QUEUE) {
                // Lazarus: I Prefer survival over unbounded growth... ( ...failed, bkz:: )
                return; // Legacity problems!
            }

            $this->taskQueue->enqueue([
                'type'    => $type,
                'payload' => $payload,
                'ts'      => time(),
            ]);
        }
    }

    /**
     * 🧠 COOPERATIVE SCHEDULER
     * One tick != one full job storm. Fibers suspend between units.
     */
    private function tickFibers(int $maxSteps = 16): void
    {
        if ($this->fibers === []) {
            // Role-separated cooperative workers (Trinity+ roles)
            $this->fibers[] = new Fiber(fn () => $this->fiberTaskConsumer());
            $this->fibers[] = new Fiber(fn () => $this->fiberMaintenanceWorker());
        }

        $steps = 0;

        foreach ($this->fibers as $idx => $fiber) {
            if ($steps >= $maxSteps) {
                break;
            }

            if ($fiber->isTerminated()) {
                unset($this->fibers[$idx]);
                continue;
            }

            if (!$fiber->isStarted()) {
                $fiber->start();
                $steps++;
                continue;
            }

            if ($fiber->isSuspended()) {
                $fiber->resume();
                $steps++;
            }
        }

        // Reindex after possible unset
        $this->fibers = array_values($this->fibers);

        // Respawn minimal set if a fiber died mid-cycle
        if ($this->fibers === [] && $this->acceptingWork) {
            $this->fibers[] = new \Fiber(fn () => $this->fiberTaskConsumer());
            $this->fibers[] = new \Fiber(fn () => $this->fiberMaintenanceWorker());
        }
    }

    private function executeTaskByType(string $type, mixed $payload): void
    {
        match ($type) {
            // Keep fiber alive; outer loop also has survival logging
            'fetch'           => FetchDriverUpdates::dispatchSync((string) $payload),

            // Pulse PHASE 1 unit (Planner)
            'divine_plan'     => $this->runDivinePlannerCommand(),

            // Pulse PHASE 3 unit (Soul / DB queue)
            'divine_dispatch' => $this->dispatchDueDivineMessages(),

            // C-3 equivalent unit (Muscle)
            'queue_drain'     => $this->processQueue(),

            'todo_callback'   => $this->executeTodo($payload),

            default           => null,
        };
    }

    /**
     * Fiber worker: consumes SplQueue units one-by-one with mandatory suspend points.
     */
    private function fiberTaskConsumer(): void
    {
        while (true) {
            if (!$this->acceptingWork) {
                Fiber::suspend();
                continue;
            }

            if ($this->useModernQueue) {
                $this->awakenDeferredQueue(self::AWAKENING_BATCH_SIZE);

                // Modern path: drain priorityQueue by priority order
                if ($this->priorityQueue->isEmpty()) {
                    Fiber::suspend();
                    continue;
                }
    
                $unit = $this->priorityQueue->extract();

                /** @var \Fiber $unit */
                $fiber = $unit instanceof Fiber ? $unit : null; // USELESS CONVERSION :| IDE-THEISM
    
                try {
                    if ($fiber) {
                        if (!$fiber->isStarted()) {
                            $fiber->start();
                        } elseif ($fiber->isSuspended()) {
                            $fiber->resume();
                        }
                    }
                    elseif (is_callable($unit)) {
                        $unit();
                    }
                } catch (\Throwable $e) {
                    // Keep fiber alive; outer loop also has survival logging
                    AmethystMatrix::yell('Lazarus Modern Fiber Failed: ' . $e->getMessage());
                    $this->ensureDatabaseConnection(true);

                    // The callable itself may succeed after reconnection.
                    $retry = $fiber
                        ? fn() => $fiber->isStarted() ? $fiber->resume() : $fiber->start() // Re-wrap: fiber is spent; re-enqueue original work
                        : (
                            is_callable($unit) ? $unit
                            : (fn() => null)
                        );

                    // Defer it to front of the deferred list for immediate retry next flush.
                    $this->deferTask(
                        $retry,
                        self::PRIORITY_CRITICAL,
                        0.0,
                        'fiber_error_retry',
                        true // front = true: retry before any other deferred item
                    );
                }
    
            } else {
                // Legacy path: FIFO SplQueue

                if ($this->taskQueue->isEmpty()) {
                    Fiber::suspend();
                    continue;
                }

                /** @var array{type:string,payload:mixed,ts:int} $task */
                $task = $this->taskQueue->dequeue();

                try {
                    $this->executeTaskByType($task['type'], $task['payload']);
                    
                } catch (\Throwable $e) {
                    // Keep fiber alive; outer loop also has survival logging
                    AmethystMatrix::yell('Lazarus Fiber Unit Failed [' . $task['type'] . ']: ' . $e->getMessage());
                    $this->ensureDatabaseConnection(true);
                }
            }

            // Cooperative yield after EVERY unit (prevents starvation)
            Fiber::suspend();
        }
    }

    /**
     * Fiber worker: paced hygiene that must not monopolize the hot path.
     * Kill-switch is also observed on the main loop; this adds responsiveness windows.
     */
    private function fiberMaintenanceWorker(): void
    {
        while (true) {
            // Lightweight opportunity for future metrics / adaptive throttling hooks
            // (DB ping + lock extend remain owned by the deterministic main loop for safety)
            \Fiber::suspend();
        }
    }

    /**
     * Cooperative idle: do not blind-sleep the whole process when work remains.
     */
    private function cooperativeIdle(int $intervalMs): void
    {
        if ($this->useModernQueue) {
            $this->awakenDeferredQueue(self::AWAKENING_BATCH_SIZE);
        }

        // If internal work remains, only micro-idle (anti busy-spin)
        $isEmpty = $this->useModernQueue
            ? ($this->priorityQueue->isEmpty() && $this->deferredTasks->isEmpty())
            : $this->taskQueue->isEmpty();

        if (!$isEmpty) {
            usleep(self::MICRO_IDLE_US);
            // Opportunistic extra tick instead of full sleep
            $this->tickFibers(8);
            return;
        }

        // Idle mode: slice sleep so kill-switch / rebirth stay responsive
        $slice = self::IDLE_SLICE_MS;
        $left  = max(0, $intervalMs);

        while ($left > 0) {
            usleep(min($slice, $left) * 1000);
            $left -= $slice;

            // Keep fibers responsive even during system idle periods
            $this->tickFibers(4); 

            // opportunity window: kill switch
            if (Cache::has(config('krubot.lazarus.kill-kommand', 'krubik:kill-lazarus'))) {
                return;
            }

            // opportunity window: newly produced work (e.g. external enqueues later)
            $stillEmpty = $this->useModernQueue
                ? ($this->priorityQueue->isEmpty() && $this->deferredTasks->isEmpty())
                : $this->taskQueue->isEmpty();

            if (!$stillEmpty) {
                return;
            }
        }
    }

    /**
     * Best-effort short drain before rebirth so in-memory units are not hard-dropped blindly.
     */
    private function drainTaskQueueGracefully(int $maxMs = 200): void
    {
        $deadline = microtime(true) + ($maxMs / 1000);

        while (microtime(true) < $deadline) {
            // First: promote any deferred tasks so they get a chance to run
            if ($this->useModernQueue) {
                $this->awakenDeferredQueue(self::AWAKENING_BATCH_SIZE, true);
            }

            $isEmpty = $this->useModernQueue
                ? ($this->priorityQueue->isEmpty() && $this->deferredTasks->isEmpty())
                : $this->taskQueue->isEmpty();
    
            if ($isEmpty) {
                break;
            }
    
            $this->tickFibers(4);
        }
    }

    /**
     * 🧠 INTERNAL: The Worker Logic
     * Executes the queue worker in short bursts.
    */
    private function processQueue(): void
    {
        // We use 'call' instead of 'callSilent' if you want debug output,
        // but for production, 'callSilent' is cleaner.
        Artisan::call('queue:work', [
            '--stop-when-empty' => true, // CRITICAL: Do not block loop!
            '--timeout'         => 20,
            '--memory'          => 128,
            '--tries'           => 3,
            '--sleep'           => 0,    // Machine Gun Mode (No sleep between jobs)
            '--max-jobs'        => self::QUEUE_BURST_BUDGET,
        ]);
    }

    /**
     * 🔌 INTERNAL: Database Defibrillator
     * @param bool $force If true, forces a reconnection immediately.
    */
    private function ensureDatabaseConnection(bool $force = false)
    {
        try {
            // If forced, or if PDO is missing/dead
            if ($force || !DB::connection()->getPdo()) {
                 throw new \Exception("Force Reconnect");
            }
            
            // Lightweight ping (Only runs every 10 loops)
            DB::connection()->getPdo()->query('SELECT 1');
            
        } catch (\Throwable $e) {
            try {
                DB::reconnect();
            } catch (\Throwable $z) {
                // If reconnect fails, we don't crash yet. The next loop cycle might fix it.
                AmethystMatrix::gaze($z, "Lazarus DB Defibrillation-Reconnect Failed");
            }
        }
    }

    /**
     * ⚖️ JUDGEMENT FUNC: Should we die to live again?
     */
    protected function shouldReincarnate(): bool
    {
        // 1. Time Limit (Reset PID/Timer to avoid Host Kill)
        if ((time() - $this->bornAt) >= self::MAX_LIFE_SECONDS) {
            return true;
        }

        // 2. Memory Limit (Prevent OOM Kills)
        // Explicitly trigger GC to free up cyclic references before measuring
        if ($this->loopCounter % self::GCC_INTERVAL === 0)
            if (function_exists('gc_collect_cycles'))
                gc_collect_cycles();

        $mem = memory_get_usage(true) / 1024 / 1024;
        if ($mem >= self::MAX_RAM_MB) {
            AmethystMatrix::observe("Lazarus: Memory Limit Reached ({$mem}MB). Initiating Rebirth.");
            return true;
        }

        // 3. Manual Kill Switch (File Based Termination ⚡🔌)
        if (file_exists(storage_path('krubik_stop'))) {
            AmethystMatrix::whisper("🛑 File-based Kill switch detected. Lazarus terminating gracefully.");
            /// exit(0);
            // We throw an exception to let the try-finally block catch it and clean up the cache-lock
            throw new RuntimeException('Lazarus Stopped via File-Switch');
        }

        return false;
    }

    /**
     * 🔥 THE PHOENIX RITUAL: Reincarnation Strategy
     * Preserved fully from v3.6
     */
    protected function reincarnate(string $driver, string $tag, bool $stealthMode, bool $useModernQueue = true): void
    {
        // 🔓 Force Release lock so the child can take it immediately
        Cache::lock($this->lockKey)->forceRelease();

        $php = PHP_BINARY;
        $artisan = base_path('artisan');
        
        $args = [
            'krubik:lazarus',
            "--driver={$driver}",
            "--tag={$tag}",
            "--use-modern-queue={$useModernQueue}"
        ];
        if ($stealthMode) $args[] = '--stealth';

        // --- STRATEGY A: WARP SPEED (PCNTL) ---
        if (!$stealthMode && function_exists('pcntl_exec')) {
            pcntl_exec($php, array_merge([$artisan], $args));
        }

        // --- STRATEGY B: PHANTOM (SPAWN/EXEC) ---
        $cmdString = implode(' ', $args);
        $fullCmd = "{$php} {$artisan} {$cmdString} > /dev/null 2>&1 &";

        if (!$this->spawnProcess($fullCmd)) {
            AmethystMatrix::yell("Lazarus Failed to Spawn! Relying on Cron-Pulse fallback.");
        }
    }

    /**
     * 🛠️ TOOL: Smart Process Spawning
     * Tries every trick in the book to bypass 'disable_functions'.
    */
    protected function spawnProcess(string $cmd): bool
    {
        try {
            if ($this->functionEnabled('exec')) {
                exec($cmd);
                return true;
            } 
            elseif ($this->functionEnabled('passthru')) {
                passthru($cmd);
                return true;
            } 
            elseif ($this->functionEnabled('proc_open')) {
                proc_open($cmd, [], $pipes);
                return true;
            }
        } catch (\Throwable $e) {
            AmethystMatrix::gaze($e, "Spawn Error");
        }
        return false;
    }

    /**
     * 🔍 CHECK: Is function usable?
     */
    protected function functionEnabled(string $func): bool
    {
        if (!function_exists($func)) return false;
        $disabled = explode(',', (string) ini_get('disable_functions'));
        return !in_array($func, array_map('trim', $disabled));
    }

    /**
     * 🚑 EMERGENCY: Handle Fatal Crashes
     */
    protected function handleShutdown(string $driver, string $tag, bool $isStealth): void
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            AmethystMatrix::notice("💀 Lazarus Flatlined! Rebooting...", $error);
            $this->reincarnate($driver, $tag, $isStealth, $this->useModernQueue);
        }
    }


    ////////// Pulse-PlusPlus ::
    // =====================================================================
    // 🫀 PULSE DNA (NON-DUPLICATIVE POWERS) — Planner + Divine Soul Dispatch
    // =====================================================================

    /**
     * Triggers the divine message planner command if the time lock has expired.
     * This replaces the need for a separate Cron Job for the planner.
     * Non-blocking variant: enqueues a fiber unit instead of always running inline.
     */
    private function enqueueDivinePlannerIfDue(): void
    {
        // Try to acquire a lock for 30 minutes (1800 seconds).
        // If acquired, it means 30 minutes have passed since last run.
        // ما از خاصیت انقضای طبیعی Cache Lock استفاده می‌کنیم تا فرکانس ۳۰ دقیقه‌ای را تضمین کنیم.
        $plannerLock = Cache::lock('krubik:planner_frequency_lock', 1800);

        // نکته مهم: ما اینجا از get() استفاده می‌کنیم اما release() نمی‌کنیم!
        // می‌گذاریم قفل تا ۳۰ دقیقه بماند تا اجرای بعدی مسدود شود.
        if ($plannerLock->get()) {
            $this->info('⏰ Time for Planning! Triggering [krubik:plan-divine-messages]...');

            // Enqueue as cooperative unit (avoids stalling fetch/queue fibers)
            $this->enqueueTask('divine_plan', null);

            // DO NOT RELEASE LOCK HERE. Let it expire in 1800s.
        }
    }

    /**
     * Runs planner synchronously inside the consumer fiber when unit is dequeued.
     */
    private function runDivinePlannerCommand(): void
    {
        try {
            // Run the planner synchronously inside the pulse/lazarus consumer
            // این دستور دقیقاً همان فایل PlanDivineMessages.php شما را اجرا می‌کند
            $this->call('krubik:plan-divine-messages');
            // NOTE: command call intentionally mirrors Pulse (kept commented there).
            // Activate when planner command is production-ready:
            // Artisan::call('krubik:plan-divine-messages');
        } catch (\Throwable $e) {
            AmethystMatrix::yell('Planner Command Failed Inside Lazarus: ' . $e->getMessage());
        }
    }

    /**
     * Checks the DB Queue for due messages and dispatches them to the Job Queue.
     * Uses Database Transactions for consistency.
     * Strengthened with Generator iteration for cooperative-friendly unit processing.
     */
    private function dispatchDueDivineMessages(): void
    {
        $now = now();

        // Transaction ensures that if we read a row, no other process reads it.
        // تراکنش دیتابیس برای جلوگیری از Race Condition
        $dispatchedCount = DB::transaction(function () use ($now) {

            // 1. Select & Lock rows that are due
            // رکوردهایی که زمان ارسالشان رسیده یا گذشته است
            $messages = DivineDispatchQueue::query()
                ->where('scheduled_at', '<=', $now)
                ->orderBy('scheduled_at')
                ->limit(self::DIVINE_BATCH_LIMIT) // Batch size limit for safety / جلوگیری از خفگی در ترافیک بالا
                ->lockForUpdate() // MySQL Row Locking (حیاتی برای همزمانی)
                ->get();

            if ($messages->isEmpty()) {
                return 0;
            }

            $count = 0;

            // Generator-driven single-item processing (lazy, interruptible mentally/structurally)
            foreach ($this->divineMessageGenerator($messages) as $msg) {
                try {
                    $this->processSingleQueueItem($msg);
                    $count++;
                } catch (\Throwable $e) {
                    AmethystMatrix::yell("Failed to process queue item ID {$msg->id}: " . $e->getMessage());
                }
            }

            // 2. Delete processed rows
            // بعد از دیسپچ موفق، رکوردها را از صف دیتابیس پاک می‌کنیم
            DivineDispatchQueue::query()
                ->whereIn('id', $messages->pluck('id'))
                ->delete();

            return $count;
        });

        if ($dispatchedCount > 0) {
            $this->info("🕊️ Dispatched {$dispatchedCount} divine messages to Job Queue.");
        }
    }

    /**
     * Lazy iterator over due divine messages (keeps processing contract explicit).
     *
     * @param  \Illuminate\Support\Collection<int, DivineDispatchQueue>  $messages
     * @return \Generator<int, DivineDispatchQueue, mixed, void>
     */
    private function divineMessageGenerator($messages): Generator
    {
        foreach ($messages as $msg) {
            yield $msg;
        }
    }

    /**
     * Decodes payload and dispatches the Job for a single queue item.
     * Calculates Smart Lock Expiration dynamically from CONFIG.
     */
    private function processSingleQueueItem(DivineDispatchQueue $msg): void
    {
        $payload = $msg->payload;

        // Handle JSON decoding if Eloquent casting didn't catch it
        if (!is_array($payload)) {
            $payload = json_decode($payload ?? '[]', true) ?: [];
        }

        $userId = $payload['user_id'] ?? $msg->user_id;
        $sectionIndex = $payload['section_index'] ?? $msg->section_index;

        if (!$userId || $sectionIndex === null) {
            AmethystMatrix::observe("Corrupt DivineDispatchQueue Item ID: {$msg->id}");
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            return; // User might be deleted
        }

        // --- SMART LOCK CALCULATION (DYNAMIC) ---
        // محاسبه زمان انقضای قفل بر اساس کانفیگ زنده
        $lockExpiration = $this->calculateSmartLockExpiration((int) $sectionIndex, now());

        // Generate Lock Key
        $sentLockKey = "divine_sent:{$user->id}:" . now()->format('Y-m-d') . ":sec_{$sectionIndex}";

        // Dispatch Job
        // جاب SendDivineMessageJob با پارامترهای کامل دیسپچ می‌شود
        SendDivineMessageJob::dispatch($user, (int) $sectionIndex, $sentLockKey, $lockExpiration);
    }

    /**
     * Calculates the precise moment the lock should expire (Start of Next Section).
     * Reads sections directly from CONFIG.
     */
    private function calculateSmartLockExpiration(int $currentSection, Carbon $now): \DateTimeInterface
    {
        // 1. Load Sections from Config (Hydration)
        $sections = config('krubot.divine_sender.allowed_hours_sections', []);

        // Safety Fallback: If config is empty
        if (empty($sections)) {
            return $now->copy()->addDay()->setHour(9)->setMinute(0)->setSecond(0);
        }

        // 2. Dynamic Next Section Logic
        $nextSectionIndex = $currentSection + 1;

        // Scenario A: There is a next section configured for today
        if (isset($sections[$nextSectionIndex])) {
            // Get the first hour of that section (e.g., 14 from [14])
            $nextStartHour = $sections[$nextSectionIndex][0];

            return $now->copy()
                ->setHour($nextStartHour)
                ->setMinute(0)
                ->setSecond(0);
        }

        // Scenario B: No next section today (Cycle Complete) -> Loop to First Section Tomorrow
        // Get the very first section key (usually 0)
        $firstSectionIndex = array_key_first($sections);
        $firstSectionStartHour = $sections[$firstSectionIndex][0]; // e.g., 9

        return $now->copy()
            ->addDay()
            ->setHour($firstSectionStartHour)
            ->setMinute(0)
            ->setSecond(0);
    }

    /// +++ ///

    /**
     * Schedule a one-shot Fiber task. Hot path is insert-only (O(log n)).
     *
     * HOW is stored raw and resolved only when the Fiber actually runs:
     *  - array   => snapshot, passed into WHAT via argument unpack
     *  - Closure => must return that same array shape at execution time
     *  - null    => WHAT is invoked with zero arguments
     *
     * @param  \DateTimeInterface|\DateInterval|int|string              $when
     * @param  callable                                                 $what
     * @param  array<int|string, mixed>|\Closure():array<int|string, mixed>|null  $how
     * @return LazarusTask
     * @throws InvalidArgumentException HOW must be an array or a Closure that returns an array;
     */
    public function todo(
        \DateTimeInterface|\DateInterval|int|string $when,
        callable $what,
        array|\Closure|null $how = null    // Snapshot Ability: Capture an ID or any data at the moment of scheduling.
    ): LazarusTask {

        // Fix: guard — todoHeap requires modern queue mode
        if (!$this->useModernQueue || !isset($this->todoHeap)) {
            $this->info('todo() rejected: modern queue is disabled.');
            return LazarusTask::rejected();
        }

        // Check if the internal heap has reached its capacity limit.
        if ($this->todoHeap->count() >= self::MAX_INTERNAL_QUEUE) {
            $this->info('todo() rejected: internal heap is full.');

            return LazarusTask::rejected();
        }

        // Normalize the flexible 'when' input into a standard DateTime object.
        $due = advanceDateTime($when);
        $dueTs = (float) $due->format('U.u');
        $id = Str::uuid()->toString(); /// bin2hex(random_bytes(16)); // More entropy for safety

        {{{ // HELP FOR LLM ::
            Schema::create('lazarus_todos', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->timestamp('due_at', 6); // با دقت میکروثانیه
                $table->text('payload');
                $table->tinyInteger('status')->comment('future-proofing')->default(0);
                
                $table->index(['status', 'due_at']); // ایندکس جادویی!
            });
        }}}

        try {
            // Prepare payload for serialization.

            // The new, more intelligent payload structure
            $payload = [
                'what'      => $what instanceof \Closure ? serialize(new SerializableClosure($what)) : $what,
                'what_type' => $what instanceof \Closure ? 'closure' : 'callable',
                'how'       => $how instanceof \Closure ? serialize(new SerializableClosure($how)) : $how,
                'how_type'  => $how instanceof \Closure ? 'closure' : (is_array($how) ? 'array' : 'null'),
            ];

            // تمام. یک INSERT اتمی، سریع و بدون Race Condition.
            DB::table($this->todoTableName)->insert([
                'id' => $id,
                'due_at' => $due,
                'payload' => json_encode($payload, JSON_THROW_ON_ERROR),
                'status' => self::TODO_STATUS_PENDING,
            ]);
        } catch (Throwable $e) {
            // If the DB insert fails, we can't proceed. Report it and reject the task.
            AmethystMatrix::gaze($e, "Lazarus failed to persist a new todo task.");
            report($e);
            return LazarusTask::rejected();
        }

        // Hot cache only: live heap + due within this incarnation's lookahead.
        $withinWindow = $dueTs <= ((float) now()->format('U.u') + self::TODO_LOAD_WINDOW_TTL);

        if (
            $this->useModernQueue
            && isset($this->todoHeap)
            && $withinWindow
            && !isset($this->todoIndex[$id])
            && $this->todoHeap->count() < self::MAX_INTERNAL_QUEUE
        ) {
            $this->todoHeap->insert([
                'id'   => $id,
                'due'  => $dueTs,
                'what' => $what,
                'how'  => $how,
            ], -$dueTs);

            $this->todoIndex[$id] = true;
        }

        // Return a fluent handle for potential cancellation or inspection.
        return new LazarusTask($id, $due, $this);
    }

    /**
     * The main orchestrator for executing a single todo task.
     * It accepts LIVE callables and parameters, not serialized strings.
     * This combines a refactoring spirit with our robust architecture.
     *
     * @param  array{id:string,due:float,what:callable,how:array|\Closure|null}  $todo
     */
    private function executeTodo(array $todo): void
    {
        $id = (string) $todo['id'];

        // Source of truth: vanished row = cancelled / already done / never persisted.
        $claimed = DB::table($this->todoTableName)
            ->where('id', $id)
            ->where('status', self::TODO_STATUS_PENDING)
            ->update(['status' => self::TODO_STATUS_RUNNING]);

        if ($claimed !== 1) {
            unset($this->todoIndex[$id], $this->cancelledTodos[$id]);
            return;
        }
        try {
            // 1. Resolve Parameters (Handles Closure, array, and null)
            $parameters = $this->resolveParameters($todo['how'] ?? null);

            // 2. Execute Action (Handles the 'what' as Closure or callable with Named Arguments)
            $this->executeAction($todo['what'], $parameters);

            $this->comment("✅ Execution completed for [{$todo['id']}]");
            
            // Mark as completed in the source of truth.
            DB::table($this->todoTableName)->where('id', $todo['id'])->delete();
            /// DB::table($this->todoTableName)->where('id', $todo['id'])->update(['status' => 2]);

        }
        catch (Throwable $e) {

            // Mark as failed for inspection but prevent retrying forever.
            DB::table($this->todoTableName)->where('id', $todo['id'])->update(['status' => self::TODO_STATUS_FAILED]); // @Todo: add `retries` field

            $this->error("🔥 Execution FAILED for todo [{$todo['id']}]", ['error' => $e->getMessage()]);
            report($e);

        }
        finally {
            unset($this->todoIndex[$id]);
        }
    }

    /**
     * Resolves the parameters for the action, executing a Closure if necessary.
     *
     * @param Closure|array|null $how
     * @return array The final parameters for the action.
     */
    private function resolveParameters(Closure|array|null $how): array
    {
        if ($how instanceof Closure) {
            $this->comment('🧠 Dynamic parameter resolution activated...');
            return (array) $how(); // Cast to array just in case it returns non-array
        }

        return $how ?? []; // If it's an array, return it. If null, return empty array.
    }

    /**
     * Executes the final action using call_user_func_array, preserving named arguments.
     * THIS IS THE CORRECT, POWERFUL, AND SAFE WAY.
     *
     * @param callable $what The action to execute.
     * @param array $parameters The resolved parameters (associative for named args).
     */
    private function executeAction(callable $what, array $parameters): void
    {
        $this->comment('🔨 Calling via call_user_func_array (with named argument support)...');
        
        // NO array_values()! We preserve the keys to support named arguments.
        // This single line handles all cases: with/without parameters, named/positional.
        call_user_func_array($what, $parameters);
    }

    /**
     * Drains due tasks from the todoHeap and enqueues them for Fiber execution.
     */
    private function processDueTodos(): void
    {
        // Quick exit if there's nothing to do.
        if ($this->todoHeap->isEmpty()) {
            return;
        }

        $nowTs = (float) now()->format('U.u');

        // Loop as long as there are items and the top item is due.
        while (!$this->todoHeap->isEmpty()) {
            /** @var array{id:string,due:float,what:callable,how:array|\Closure|null} $peek */
            $peek = $this->todoHeap->top();
            
            // Since the heap is time-ordered, we can safely break if the top item
            // is not due yet. All subsequent items will also be in the future.
            if ($peek['due'] > $nowTs) {
                break;
            }
            
            // This is a due item, extract it from the heap.
            $this->todoHeap->extract();
            
            // Check our lazy cancellation list. If the ID is present, skip
            // execution and clean up the cancellation entry.
            if (isset($this->cancelledTodos[$peek['id']])) {
                unset($this->cancelledTodos[$peek['id']]);
                continue;
            }
            
            // The task is valid and due. Enqueue it into the main task queue
            // to be picked up by a Fiber.
            $this->enqueueTask('todo_callback', $peek);
        }
    }

    /**
     * Lazily cancels a scheduled 'todo' task by its ID.
     *
     * @param string $id
     * @return bool
     */
    public function cancelTodo(string $id): bool
    {
        // Ignore invalid IDs.
        if ($id === '') {
            return false;
        }
        
        // Add the ID to the cancellation list. This is an O(1) operation.
        // The actual task is not removed from the heap, but will be ignored
        // when it's processed in processDueTodos().
        $this->cancelledTodos[$id] = true;
        unset($this->todoIndex[$id]);

        // Remove from database
        DB::table($this->todoTableName)->where('id', $id)->delete();

        return true;
    }
}
