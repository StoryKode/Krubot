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
use Illuminate\Support\Facades\Cache;
use KrubiK\Helpers\AmethystMatrix as Log;
use Illuminate\Support\Facades\DB;
use KrubiK\Jobs\FetchDriverUpdates;
use KrubiK\DivineMessageSender\Jobs\SendDivineMessageJob;
use KrubiK\DivineMessageSender\Models\DivineDispatchQueue;
use App\Models\User;
use Carbon\Carbon;

// KrubiK-Pulse will be rest soon...
/**
 * Class KrubiKPulse - v9.9 (The Masterpiece / Config-Driven / DB-Core)
 * The Central Nervous System (CNS) of the Bot.
 * 
 * Responsibilities:
 * 1. [INPUT] Fetch Updates (Sync)
 * 2. [PLAN] Trigger Planner (Every 30 mins - Self Managed)
 * 3. [DISPATCH] Process Divine Queue from DB (Real-time Transactional)
 * 4. [EXECUTE] Run Worker (Process Jobs)
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class KrubiKPulse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'krubik:pulse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The Heartbeat: Fetches updates, Triggers Planner, Dispatches DB Queue, and Runs Worker.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = microtime(true); // can be used for logging
        // $this->info('💓 Pulse initiated...'); // Silent mode preferred for pulse

        // --- ATOMIC GUARD PROTOCOL ---
        // 25s lock is good to prevent overlap with the next 30s cron hit.
        // قفل اتمیک برای جلوگیری از تداخل دو ضربان قلب

        // But In Real-Life , The Golden Standard for 30s intervals would be:
        $lock = Cache::lock('krubik:pulse:lock', 60);

        try {
            if ($lock->get()) {
                
                // ---------------------------------------------------------
                // PHASE 1: THE PLANNER (Self-Triggering / Every 30 Mins)
                // ---------------------------------------------------------
                // این متد چک می‌کند اگر ۳۰ دقیقه گذشته باشد، پلنر را اجرا می‌کند
                if (config('krubot.divine_sender.enabled', true)) {
                    $this->triggerDivinePlanner();
                }

                // ---------------------------------------------------------
                // PHASE 2: THE EARS (Fetch Updates)
                // ---------------------------------------------------------
                // $this->info('📡 Fetching Rubika Updates...');
                // FetchRubikaUpdates::dispatchSync();

                // $this->info('📡 Fetching Targeted Updates...');
                if (config('krubot.polling.enabled', true)) {
                    $targets = config('krubot.polling.drivers', ['rubika']);
                    foreach ($targets as $targetDriver) {
                        FetchDriverUpdates::dispatchSync($targetDriver);
                    }
                }

                // ---------------------------------------------------------
                // PHASE 3: THE SOUL (Dispatch Scheduled Messages)
                // ---------------------------------------------------------
                // تنها زمانی اجرا شود که فیچر در کانفیگ روشن باشد
                if (config('krubot.divine_sender.enabled', true)) {
                    $this->dispatchDueDivineMessages();
                }

                // ---------------------------------------------------------
                // PHASE 4: THE MUSCLE (Worker)
                // ---------------------------------------------------------
                // $this->info('⚙️ Processing Queue...');
                $this->call('queue:work', [
                    '--stop-when-empty' => true,
                    '--timeout' => 20, // Keep it tight to fit in 30s window
                    '--tries' => 3,
                    '--memory' => 128
                ]);

                // $duration = round(microtime(true) - $startTime, 2);
                // $this->info("✅ Pulse Cycle Complete in {$duration}s.");

            } else {
                // $this->info('🏃 Overlapping Pulse detected. Skipping.');
                return;
            }
        } catch (\Throwable $e) {
            Log::error("🔥 KrubiKPulse CRITICAL FAILURE: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            // $this->error("Pulse Failed!");
        } finally {
            optional($lock)->release();
            // $this->info('🔑 Lock released.');
        }
    }

    /**
     * Triggers the divine message planner command if the time lock has expired.
     * This replaces the need for a separate Cron Job for the planner.
     */
    private function triggerDivinePlanner(): void
    {
        // Try to acquire a lock for 30 minutes (1800 seconds).
        // If acquired, it means 30 minutes have passed since last run.
        // ما از خاصیت انقضای طبیعی Cache Lock استفاده می‌کنیم تا فرکانس ۳۰ دقیقه‌ای را تضمین کنیم.
        $plannerLock = Cache::lock('krubik:planner_frequency_lock', 1800);

        // نکته مهم: ما اینجا از get() استفاده می‌کنیم اما release() نمی‌کنیم!
        // می‌گذاریم قفل تا ۳۰ دقیقه بماند تا اجرای بعدی مسدود شود.
        if ($plannerLock->get()) {
            $this->info('⏰ Time for Planning! Triggering [krubik:plan-divine-messages]...');
            
            try {
                // Run the planner synchronously inside the pulse
                // این دستور دقیقاً همان فایل PlanDivineMessages.php شما را اجرا می‌کند
                /// $this->call('krubik:plan-divine-messages');
                
            } catch (\Throwable $e) {
                Log::error("Planner Command Failed Inside Pulse: " . $e->getMessage());
            }
            
            // DO NOT RELEASE LOCK HERE. Let it expire in 1800s.
        }
    }

    /**
     * Checks the DB Queue for due messages and dispatches them to the Job Queue.
     * Uses Database Transactions for consistency.
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
                ->limit(50) // Batch size limit for safety / جلوگیری از خفگی در ترافیک بالا
                ->lockForUpdate() // MySQL Row Locking (حیاتی برای همزمانی)
                ->get();

            if ($messages->isEmpty()) {
                return 0;
            }

            $count = 0;
            foreach ($messages as $msg) {
                try {
                    $this->processSingleQueueItem($msg);
                    $count++;
                } catch (\Throwable $e) {
                    Log::error("Failed to process queue item ID {$msg->id}: " . $e->getMessage());
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
            Log::warning("Corrupt DivineDispatchQueue Item ID: {$msg->id}");
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            return; // User might be deleted
        }

        // --- SMART LOCK CALCULATION (DYNAMIC) ---
        // محاسبه زمان انقضای قفل بر اساس کانفیگ زنده
        $lockExpiration = $this->calculateSmartLockExpiration((int)$sectionIndex, now());
        
        // Generate Lock Key
        $sentLockKey = "divine_sent:{$user->id}:" . now()->format('Y-m-d') . ":sec_{$sectionIndex}";

        // Dispatch Job
        // جاب SendDivineMessageJob با پارامترهای کامل دیسپچ می‌شود
        SendDivineMessageJob::dispatch($user, (int)$sectionIndex, $sentLockKey, $lockExpiration);
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
}
