<?php

namespace KrubiK\DivineMessageSender\Dispatchers;
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
use Illuminate\Support\Arr;
use Ixlluminate\Support\Carbon;
use KrubiK\Krubot;
use App\Models\User;
use App\Models\DivineMessage;

/**
 *        Class DivineInterventionDispatcher
 * Version 2.68 (Lazy RNG & 2D Matrix Architecture)
 * 
 * The battle-tuned engine for hacking the Reticular Activating System (RAS) of users
 * by delivering perfectly timed, non-predictable, and context-aware spiritual/motivational nudges.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class DivineInterventionDispatcher extends Command
{
    /**
     * The name and signature of the console command.
     * Recommended cron execution: everyTenMinutes()
     *
     * @var string
    */
    protected $signature = 'krubik:dispatch-divine-nudge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatches spiritual/financial nudges based on randomized divine timing (10-min slots) and sales KPI. && Calculates KPI progress and dispatches context-aware spiritual nudges based on time-section and achievement-bucket with Lazy RNG per section.';

    /**
     * Configuration for allowed hours grouped by sections.
     * A user can receive ONLY ONE message per section per day.
     * This structure is dynamic; adding Section 3 (e.g., for midnight) requires no changes to the core logic.
     * @var array<int, int[]>
     */
    private const ALLOWED_HOURS_SECTIONS = [
        0 => [9, 10, 11],       // Section 0: Morning Awakening (The Start)
        1 => [14],              // Section 1: Midday Check (The Persistence)
        2 => [17, 18, 19, 20],  // Section 2: Evening Accounting (The Closing)
    ];

    /**
     * Allowed minute slots for execution.
     * These MUST align with the Laravel Scheduler frequency (e.g., ->everyTenMinutes()).
     * @var int[]
     */
    private const ALLOWED_MINUTE_SLOTS = [0, 10, 20, 30, 40, 50];

    /********
     * 
     * disabled and moved to DB for Professional Management
     * 
     * 2D Matrix for Messages.
     * Dimensions: [SectionIndex][PerformanceBucketIndex]
     *
     * Buckets Logic (Floor(Percent / 20)):
     * 0 => 0-19%   (Crisis/Hope)
     * 1 => 20-39%  (Warm Up)
     * 2 => 40-59%  (Halfway)
     * 3 => 60-79%  (Pushing)
     * 4 => 80-100%+ (Victory/Gratitude)
     * @var array<int, array<int, string[]>>
     * /
    private const MESSAGE_BUCKETS = [
        // ==========================================
        // SECTION 0: MORNING (The Awakening)
        // ==========================================
        0 => [
            0 => [ "☀️ بسم‌الله. هنوز صفحه امروز سفیده. یه جوری بنویسش که شب حال کنی.", "قهرمان! عدد صفر یعنی بی‌نهایت پتانسیل. استارت بزن، خدا برکت میده.", "اول صبحی فقط یه قدم کوچیک بردار. همون یه قدم، معجزه می‌کنه.", ],
            1 => [ "عالی شروع کردی! موتورت گرم شده، حالا دنده رو عوض کن.", "سحرخیز بودی، کامروا هم باش. سرعتت خوبه، ولی مقصد دورتره.", "باریکلا! یه تکون دیگه بدی به نصف هدف رسیدی قبل از ناهار.", ],
            2 => [ "چه کردی اول صبحی؟! نصف راه رو رفتی. دمت گرم.", "سرعتت عالیه. امروز روز توئه، شک نکن.", "ماشالله به این اراده. یه نفس عمیق بکش و ادامه بده.", ],
            3 => [ "طوفانی شروع کردی! امروز رکورد می‌زنی، من مطمئنم.", "بوی موفقیت میاد. همین فرمون رو بگیر، عصر پادشاهی.", "چیزی نمونده تا تیکِ سبزِ تارگت. بجنگ پهلوون.", ],
            4 => [ "بابا تو دیگه کی هستی؟! تارگت رو اول صبح لوله کردی!", "شکر نعمت فراموش نشه. الان وقتشه دست یکی دیگه رو هم بگیری.", "امروز رو تاریخ‌ساز کردی. حالا با خیال راحت کیفیت رو ببر بالا.", ],
        ],
        // ==========================================
        // SECTION 1: MIDDAY (The Persistence)
        // ==========================================
        1 => [
            0 => [ "ظهر شده و هنوز خبری نیست؟ عیبی نداره. نیمه دوم بازی مهم‌تره.", "ناامیدی کار شیطانه. یه وضو، یه چای، یه 'یا علی'. بلند شو.", "هنوز وقت هست. بازار تازه داره گرم می‌شه. تو هم گرم شو.", ],
            1 => [ "خوبه، ولی راضی نباش. پتانسیل تو خیلی بیشتر از این حرفاست.", "خستگی ممنوع! الان وقت شل کردن نیست، وقت فشار آوردنه.", "یه تکون به خودت بدی، آمار از این رو به اون رو میشه.", ],
            2 => [ "نصف روز، نصف هدف. تعادلت خوبه ولی ما دنبال قهرمانی هستیم.", "وسط راهی. نه برگرد، نه وایسا. فقط گاز بده.", "خدا قوت! یه همت دیگه کنی، از سرازیری رد میشی.", ],
            3 => [ "آفرین! بوی کباب موفقیت داره میاد. یکم دیگه باد بزن.", "اعدادت دارن می‌خندن. کم نیار که آخراشه.", "عالی پیش رفتی. نذار خستگی ظهر، سرعتت رو بگیره.", ],
            4 => [ "ناهار رو با طعم پیروزی بخور! دمت گرم واقعا.", "امروز رو ترکوندی. بقیه روز رو ریلکس کن یا رکورد بزن.", "خدا برکت داده به وقتت. شکر یادت نره.", ],
        ],
        // ==========================================
        // SECTION 2: EVENING (The Accounting)
        // ==========================================
        2 => [
            0 => [ "روز تموم شده؟ نه تا وقتی که تو نخوای. معجزه دقیقه ۹۰ رو دیدی؟", "شاید امروز روزت نبوده، ولی شبِت می‌تونه باشه. یه حرکت بزن.", "فدا سرت. ولی قبل خواب یه دونه فروش بزن که وجدانت آروم بخوابه.", ],
            1 => [ "هنوز چراغ‌ها روشنه. یه تلاش آخر می‌تونه آبروداری کنه.", "کم نیار. قهرمان‌ها توی خستگی‌های آخر وقت ساخته میشن.", "یه یا علی دیگه بگو. خدا بیدارترینه.", ],
            2 => [ "بد نبود، ولی عالی هم نبود. فردا رو باید بهتر بسازی.", "خدا قوت. پرونده امروز رو با یه حس خوب ببند.", "تلاشت مقدس بود. نتیجه دست خداست. برو استراحت کن.", ],
            3 => [ "حیفه! چیزی نمونده تا صد. یه تلفن دیگه، یه پیگیری دیگه...", "خستگی در میره، ولی افتخار میمونه. تمومش کن کارو.", "عالی بودی. یه فشار کوچیک دیگه، فقط برای دل خودت.", ],
            4 => [ "شب بخیر قهرمان. سرت رو بالا بگیر و راحت بخواب.", "گل کاشتی! رزق حلالت گوارای وجود.", "امشب ستاره‌ها به تلاشت چشمک می‌زنن. دمت گرم.", ],
        ],
    ];
    */

    /***
     * The main execution logic for the command.
     * Implements Lazy RNG by only processing users for the currently active time section.
     * /
    // this version of func is aligned with MESSAGE_BUCKETS that removed in ver 2.467
    public function handle(): void
    {
        $now = now();
        $currentHour = (int) $now->format('H');
        $currentMinute = (int) $now->format('i');

        $this->info("🕊️  Divine Intervention Dispatcher started at {$now->toTimeString()}");

        // Iterate through all defined sections to find the active one.
        foreach (self::ALLOWED_HOURS_SECTIONS as $sectionIndex => $possibleHours) {
            
            // [CRITICAL LAZY RNG LOGIC]
            // We strictly check if the current execution hour is within the bounds of this section.
            // If it's 09:XX, we completely ignore sections for 14:XX and 17:XX-20:XX.
            // This ensures the RNG "dice" for later sections is NOT rolled until we are in their time territory.
            if (!in_array($currentHour, $possibleHours, true)) {
                continue;
            }

            $this->info("🚀 Section {$sectionIndex} is active. Processing users...");

            // Now that we are confirmed to be within an active section's time window, process the users.
            User::where('rubika_state', '!=', 0)
                ->select(['id', 'rubika_user_id']) // Select only what's needed for performance.
                ->chunkById(500, function ($users) use ($sectionIndex, $possibleHours, $currentHour, $currentMinute, $now) {
                    foreach ($users as $user) {
                        try {
                            $this->processUserForActiveSection($user, $sectionIndex, $possibleHours, $currentHour, $currentMinute, $now);
                        } catch (\Throwable $e) {
                            Log::error("DivineNudge failed for UserID {$user->id}. Reason: {$e->getMessage()}", [
                                'exception' => $e
                            ]);
                        }
                    }
                });
            
            // Since an hour can only belong to one section, we can break after finding the active one.
            break;
        }

        $this->info("✅ Dispatch cycle finished.");
    }

    /**
     * Processes a single user for the currently active time section.
     * This is where the fate of the user for this section is checked or decided.
     * /
    private function processUserForActiveSection(User $user, int $sectionIndex, array $possibleHours, int $currentHour, int $currentMinute, Carbon $now): void
    {
        // Cache Key 1: Lock to prevent double-sending. Is the mission for this section already done today?
        $sentLockKey = "divine_sent:{$user->id}:{$now->format('Y-m-d')}:sec_{$sectionIndex}";

        if (Cache::has($sentLockKey)) {
            return; // Already enlightened. Move on.
        }

        // Cache Key 2: The user's fate for this section. What is their assigned time slot?
        // This is where the dice is rolled, but only if it hasn't been rolled before for this section today.
        $schedule = $this->getDivineSchedule($user->id, $sectionIndex, $possibleHours, $now);

        // Check if this execution is the "Golden Moment" destined for the user.
        // Logic: Current Hour must match Target Hour AND Current Minute must be inside the 10-minute slot.
        $isGoldenMoment = ($currentHour === $schedule['hour']) &&
                          ($currentMinute >= $schedule['minute'] && $currentMinute < ($schedule['minute'] + 10));

        if (!$isGoldenMoment) {
            return; // Not their time yet. Or they missed the slot. Wait for the next cycle.
        }

        // --- TRIGGER ---
        $this->info("✨ Golden Moment found for User {$user->id} in Section {$sectionIndex}. Dispatching...");
        
        // 1. Generate and dispatch the message in real-time.
        $this->dispatchDivineMessage($user, $sectionIndex);

        // 2. Lock this section for the user until the end of the day to ensure single execution.
        Cache::put($sentLockKey, true, $now->endOfDay());
    }



    /******
     * Handles real-time KPI calculation, message selection from the 2D matrix, and dispatching.
     * /
    private function dispatchDivineMessage(User $user, int $sectionIndex): void
    {
        // 1. Calculate Real-Time KPI (The "Effort" Factor)
        // Assumes these methods exist on your User model or a related service.
        $goal = (int) $user->getTodaysGoal(); // e.g., 20000 (thousand Toman)
        $achieved = (int) $user->getTodaysAcheivedSales(); // e.g., 15000 (thousand Toman)

        // Prevent division by zero.
        $percentage = ($goal > 0) ? (($achieved / $goal) * 100) : 0;

        // 2. Select the payload from the 2D matrix.
        $message = $this->selectMessagePayload($sectionIndex, $percentage);

        // 3. Send via Krubot.
        try {
            // Instantiate Bot on the fly (or use Dependency Injection if you have a Singleton).
            $bot = new Krubot(config('krubik.bot_token'));
            
            $finalText = "📊 *گزارش وضعیت معنوی/مالی شما*\n\n" . $message;

            // Assumes 'rubika_user_id' is the GUID field in your User model.
            $bot->chat($user->rubika_user_id)
                ->message($finalText)
                ->send();
                
            $this->info("✅ Message dispatched to {$user->rubika_user_id} [Sec:{$sectionIndex} | KPI:{$percentage}%] -> '{$message}'");

        } catch (\Throwable $e) {
            Log::warning("Failed to send divine message to UserID {$user->id}. Reason: {$e->getMessage()}");
        }
    }
    ******/

    /******* Start New Version Funcs (Load Texts from DB instead of hard-coding them in php file) ******/
        // ... (imports: use App\Models\DivineMessage; use Illuminate\Support\Facades\Cache;)

    /**
     * The main execution logic for the command.
     * Implements Lazy RNG and Localization.
     */
    public function handle(): void
    {
        $now = now();
        $currentHour = (int) $now->format('H');
        $currentMinute = (int) $now->format('i');

        $this->info(__('divine_log_start', ['time' => $now->toTimeString()]));

        foreach (self::ALLOWED_HOURS_SECTIONS as $sectionIndex => $possibleHours) {
            
            // Lazy RNG Check
            if (!in_array($currentHour, $possibleHours, true)) {
                continue;
            }

            $this->info(__('divine_log_section_active', ['section' => $sectionIndex]));

            User::where('rubika_state', '!=', 0)
                ->select(['id', 'rubika_user_id'])
                ->chunkById(10, function ($users) use ($sectionIndex, $possibleHours, $currentHour, $currentMinute, $now) {
                    foreach ($users as $user) {
                        try {
                            $this->processUserForActiveSection($user, $sectionIndex, $possibleHours, $currentHour, $currentMinute, $now);
                        } catch (\Throwable $e) {
                            Log::error(__('divine_log_failed', ['id' => $user->id, 'reason' => $e->getMessage()]), [
                                'exception' => $e
                            ]);
                        }
                    }
                });
            
            break;
        }

        $this->info(__('divine_log_cycle_finished'));
    }

    /**
     * Processes a single user. Changed to use localized logs.
     */
    private function processUserForActiveSection(User $user, int $sectionIndex, array $possibleHours, int $currentHour, int $currentMinute, Carbon $now): void
    {
        $sentLockKey = "divine_sent:{$user->id}:{$now->format('Y-m-d')}:sec_{$sectionIndex}";

        if (Cache::has($sentLockKey)) {
            return; 
        }

        $schedule = $this->getDivineSchedule($user->id, $sectionIndex, $possibleHours, $now);

        $isGoldenMoment = ($currentHour === $schedule['hour']) &&
                          ($currentMinute >= $schedule['minute'] && $currentMinute < ($schedule['minute'] + 10));

        if (!$isGoldenMoment) {
            return;
        }

        // --- TRIGGER ---
        $this->info(__('divine_log_golden_moment', ['id' => $user->id, 'section' => $sectionIndex]));
        
        $this->dispatchDivineMessage($user, $sectionIndex, $now, $sentLockKey);
    }

    /********
     * sry, updated version has coming Again... ;)
     * Updated to support DB-driven selection + Localization + Locking.
     * /
    private function dispatchDivineMessage(User $user, int $sectionIndex, Carbon $now, string $lockKey): void
    {
        // 1. KPI Calculation
        $goal = (int) $user->getTodaysGoal();
        $achieved = (int) $user->getTodaysAcheivedSales();
        $percentage = ($goal > 0) ? (($achieved / $goal) * 100) : 0;

        // 2. Select Payload from DB (The new logic)
        $messageData = $this->selectMessagePayloadFromDB($sectionIndex, $percentage);
        $messageContent = $messageData['content'];
        $messageId = $messageData['id'];

        // 3. Send via Krubot
        try {
            $bot = new Krubot(config('krubik.bot_token'));
            
            // Using localized header
            $finalText = __('divine_msg_header') . $messageContent;

            $bot->chat($user->rubika_user_id)
                ->message($finalText)
                ->send();
            
            // Lock ONLY after successful send
            Cache::put($lockKey, true, $now->endOfDay());

            $this->info(__('divine_log_dispatched', [
                'rubika_id' => $user->rubika_user_id,
                'section' => $sectionIndex,
                'kpi' => round($percentage, 1),
                'msg_id' => $messageId
            ]));

        } catch (\Throwable $e) {
            // If failed, we DO NOT lock, so it retries in the next slot (if applicable) or next run.
            throw $e; 
        }
    }

    /******
       sry, newer version added baby ;)
     * NEW METHOD: DB Selection with Map-Style Caching.
     * 
     * Strategy:
     * 1. Always Query IDs to allow new messages to appear instantly (Random Selection).
     * 2. Cache only the Content string by ID (Memory efficient).
     * 
     * @return array{id: int, content: string}
     * /
    private function selectMessagePayloadFromDB(int $sectionIndex, float $percentage): array
    {
        // A. Calculate Bucket
        $bucketIndex = min((int) floor($percentage / 20), 4);

        // B. Fetch Candidate IDs (Lightweight Query)
        // We fetch IDs every time to ensure randomness and freshness.
        $candidateIds = \App\Models\DivineMessage::where('section_index', $sectionIndex)
            ->where('bucket_index', $bucketIndex)
            ->pluck('id');

        // C. Fallback Logic (Safety Net)
        // If no message for this specific KPI, try bucket 0 of this section.
        if ($candidateIds->isEmpty()) {
            $candidateIds = \App\Models\DivineMessage::where('section_index', $sectionIndex)
                ->where('bucket_index', 0)
                ->pluck('id');
        }
        // If still empty, try Section 0, Bucket 0 (Absolute fallback)
        if ($candidateIds->isEmpty()) {
            $candidateIds = \App\Models\DivineMessage::where('section_index', 0)
                ->where('bucket_index', 0)
                ->pluck('id');
        }
        
        // If DB is completely empty, return a hardcoded emergency string (Cyber-Theology fail-safe)
        if ($candidateIds->isEmpty()) {
             return ['id' => 0, 'content' => "مسیر نور همیشه باز است. ادامه بده."];
        }

        // D. Select Random ID
        $selectedId = $candidateIds->random();

        // E. Map-Style Caching (Key: ID -> Value: Content)
        // We cache the content for 24 hours.
        $content = Cache::remember("divine_msg_content_{$selectedId}", now()->addDay(), function () use ($selectedId) {
            return \App\Models\DivineMessage::find($selectedId)->content;
        });

        return ['id' => $selectedId, 'content' => $content];
    } */


    private function dispatchDivineMessage(User $user, int $sectionIndex, Carbon $now, string $lockKey): void
    {
        // KPI calculation
        $goal = (int) $user->getTodaysGoal();
        $achieved = (int) $user->getTodaysAcheivedSales();
        $percentage = ($goal > 0) ? (($achieved / $goal) * 100) : 0.0;

        // Select payload from DB via model
        $messageData = $this->selectMessagePayloadFromDB($sectionIndex, $percentage);
        $messageContent = $messageData['content'] ?? 'مسیر نور همیشه باز است. ادامه بده.';
        $messageId = $messageData['id'] ?? 0;

        try {
            $bot = new Krubot(config('krubik.bot_token'));

            $finalText = __('divine_msg_header') . $messageContent;

            $bot->chat($user->rubika_user_id)
                ->message($finalText)
                ->send();

            // Lock only on successful send
            Cache::put($lockKey, true, $now->endOfDay());

            $this->info(__('divine_log_dispatched', [
                'rubika_id' => $user->rubika_user_id,
                'section' => $sectionIndex,
                'kpi' => round($percentage, 1),
                'msg_id' => $messageId
            ]));

        } catch (\Throwable $e) {
            // Do not lock; allow retrying in next eligible slot.
            Log::warning(__('divine_log_failed', [
                'id' => $user->id,
                'reason' => $e->getMessage()
            ]));
        }
    }

    /**
     * NEW COMPLETE METHOD: DB Selection using DivineMessage model (Map-style caching).
     *
     * Returns: ['id' => int, 'content' => string]
     */
    private function selectMessagePayloadFromDB(int $sectionIndex, float $percentage): array
    {
        // Calculate bucket index safely (0..4)
        $bucketIndex = min((int) floor($percentage / 20), 4);

        // Delegate heavy-lifting & fallback cascade to the model helper.
        $payload = DivineMessage::randomMessagePayload($sectionIndex, $bucketIndex);

        // Ensure we always return array with id & content.
        if (!isset($payload['id'], $payload['content'])) {
            return ['id' => 0, 'content' => 'مسیر نور همیشه باز است. ادامه بده.'];
        }

        return $payload;
    }

    /******* End New Version Funcs (Load Texts from DB instead of hard-coding them in php file) ******/

    /**
     * Gets or creates the random time slot for a user in a specific section for today.
     * The `Cache::remember` closure (the dice roll) is ONLY executed if the cache key doesn't exist.
     */
    private function getDivineSchedule(int $userId, int $sectionIndex, array $possibleHours, Carbon $now): array
    {
        $scheduleKey = "divine_schedule:{$userId}:{$now->format('Y-m-d')}:sec_{$sectionIndex}";

        return Cache::remember($scheduleKey, $now->endOfDay(), function () use ($possibleHours) {
            return [
                'hour'   => Arr::random($possibleHours),
                'minute' => Arr::random(self::ALLOWED_MINUTE_SLOTS),
            ];
        });
    }

    /**
     * Safely selects a random message from the 2D MESSAGE_BUCKETS matrix.
     * It includes fallbacks to prevent errors if the configuration is incomplete.
     */
    private function selectMessagePayload(int $sectionIndex, float $percentage): string
    {
        // 1. Calculate Bucket Index (0 to 4).
        // e.g., 78% -> floor(78/20) = 3. 110% -> min(floor(110/20), 4) = min(5, 4) = 4.
        $bucketIndex = min((int) floor($percentage / 20), 4);

        // 2. Safe Matrix Access.
        // Fallback to Section 0 if the requested section has no messages defined.
        $sectionMessages = self::MESSAGE_BUCKETS[$sectionIndex] ?? self::MESSAGE_BUCKETS[0];
        
        // Fallback to Bucket 0 of the selected section if the specific bucket is missing.
        $messages = $sectionMessages[$bucketIndex] ?? $sectionMessages[0];

        // 3. Randomly pick one of the available messages from the chosen bucket.
        return Arr::random($messages);
    }
}
