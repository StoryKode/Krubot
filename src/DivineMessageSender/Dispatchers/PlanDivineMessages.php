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
use App\Models\User;
use App\Models\DivineDispatchQueue;
use Illuminate\Support\Carbon;

class PlanDivineMessages extends Command
{
    protected $signature = 'krubik:plan-divine-messages';
    protected $description = 'Plans the divine messages for active users and populates the Redis dispatch queue.';

    /**
     * نام کلید Sorted Set در Redis
     */
    private const DISPATCH_QUEUE_KEY = 'krubik:divine_dispatch_queue';

    public function handle()
    {
        // 0. Config & Kill Switch Check
        // اگر سیستم از کانفیگ خاموش باشد، هیچ برنامه‌ریزی انجام نشود.
        if (!config('krubot.divine_sender.enabled', true)) {
            $this->warn('⛔ Divine Sender is disabled in config. Planning skipped.');
            return;
        }

        $this->info('🗓️ Divine Message Planner started...');
        $now = now();
        $config = config('krubot.divine_sender');
        $allowedSections = $config['allowed_hours_sections'];

        $users = User::where('rubika_state', '!=', 0)
                     ->select(['id', 'rubika_user_id'])
                     ->get();
        // ->chunkById(200, function ($users) use ($allowedSections, $now, &$totalPlanned) {}); // Memory Optimization: Chunking

        $plannedCount = 0;

        foreach ($users as $user) {
            foreach ($allowedSections as $sectionIndex => $possibleHours) {
                try {
                    /**
                    // * Logic extracted for cleaner Chunk / Loop * //
                    /*
                    // 1. چک می‌کنیم آیا امروز در این سکشن قبلا پیام "ارسال شده" یا نه. اگر بله، رد می‌شویم.
                    $sentLockKey = "divine_sent:{$user->id}:{$now->format('Y-m-d')}:sec_{$sectionIndex}";
                    if (Cache::has($sentLockKey)) {
                        continue;
                    }

                    // 2. برنامه زمانی (قرعه‌کشی) را دریافت یا ایجاد می‌کنیم.
                    // این همان منطق قبلی است که تضمین می‌کند قرعه‌کشی فقط یکبار انجام شود.
                    $schedule = $this->getOrSetDivineSchedule($user->id, $sectionIndex, $possibleHours, $now);
                    $targetTimestamp = $schedule['timestamp'];
                    $targetDateTime = Carbon::createFromTimestamp($targetTimestamp);

                    // 3. چک می‌کنیم که آیا این برنامه زمانی از قبل در صف Redis ما وجود دارد یا نه.
                    // این کار از ثبت رکوردهای تکراری جلوگیری می‌کند.
                    $isAlreadyQueued = DivineDispatchQueue::existsFor($user->id, $sectionIndex, $now);
                    
                    if ($isAlreadyQueued) {
                        continue;
                    }

                    // 4. این برنامه زمانی را به صف انتظار Redis اضافه می‌کنیم.
                    // Score = زمان ارسال (timestamp)
                    // Value = اطلاعات لازم برای ارسال (JSON)
                    DivineDispatchQueue::enqueue(
                        $user->id,
                        $sectionIndex,
                        $targetDateTime,
                        json_decode($this->getQueueMemberValue($user, $sectionIndex), true)
                    );

                    $plannedCount++;
                    */
                    $this->planForUserInSection($user, $sectionIndex, $possibleHours, $now, $plannedCount);
                    
                } catch (\Throwable $e) {
                    Log::error("Divine Planner failed for UserID {$user->id} in Section {$sectionIndex}. Reason: {$e->getMessage()}");
                }
            }
        }
        // آزادسازی مموری در هر چانک (اختیاری اما مفید برای اسکریپت‌های طولانی)
        // gc_collect_cycles(); 

        $this->info("✅ Divine Planner finished. {$plannedCount} new messages scheduled in DB Queue.");
    }

    /**
     * Logic extracted for cleaner Chunk Loop
     */
    private function planForUserInSection(User $user, int $sectionIndex, array $possibleHours, Carbon $now, int &$counter): void
    {
        // A. Check if already sent today (Cache Lock)
        // این قفل زمانی ست می‌شود که پیام "واقعا" ارسال شده باشد.
        $sentLockKey = "divine_sent:{$user->id}:{$now->format('Y-m-d')}:sec_{$sectionIndex}";
        if (Cache::has($sentLockKey)) {
            return;
        }

        // B. Get or Create Schedule (Lottery)
        // قرعه‌کشی زمان ارسال
        $schedule = $this->getOrSetDivineSchedule($user->id, $sectionIndex, $possibleHours, $now);
        $targetTimestamp = $schedule['timestamp'];
        $targetDateTime = Carbon::createFromTimestamp($targetTimestamp);

        // C. Check if already Queued in DB (Prevent Duplicates)
        // جلوگیری از اینسرت تکراری در صف دیتابیس
        $isAlreadyQueued = DivineDispatchQueue::existsFor($user->id, $sectionIndex, $now);

        if ($isAlreadyQueued) {
            return;
        }

        // D. Enqueue to DB
        DivineDispatchQueue::enqueue(
            $user->id,
            $sectionIndex,
            $targetDateTime,
            json_decode($this->getQueueMemberValue($user, $sectionIndex), true)
        );

        $counter++;
    }
    
    /**
     * یک مقدار منحصر به فرد برای هر کاربر/سکشن جهت ذخیره در صف انتظار تولید می‌کند.
     */
    private function getQueueMemberValue(User $user, int $sectionIndex): string
    {
        return json_encode(['user_id' => $user->id, 'section_index' => $sectionIndex]);
    }

    /**
     * قرعه‌کشی زمان ارسال را انجام داده و نتیجه را در کش ذخیره و بازخوانی می‌کند.
     */
    private function getOrSetDivineSchedule(int $userId, int $sectionIndex, array $possibleHours, \Carbon\Carbon $now): array
    {
        $scheduleKey = "divine_schedule:{$userId}:{$now->format('Y-m-d')}:sec_{$sectionIndex}";

        return Cache::remember($scheduleKey, $now->endOfDay(), function () use ($possibleHours, $now) {
            $randomHour = Arr::random($possibleHours);
            $randomMinute = random_int(0, 59); // دقیقه کاملا رندوم بین ۰ تا ۵۹
            
            // ایجاد یک آبجکت تاریخ و زمان دقیق برای ارسال
            $targetDateTime = $now->copy()->hour($randomHour)->minute($randomMinute)->second(0);


            // نکته: اگر زمان تولید شده مربوط به گذشته باشد (مثلا الان ساعت ۱۰ است و قرعه ساعت ۹ درآمده)،
            // سیستم در فاز Pulse آن را به عنوان "معوقه" شناسایی کرده و بلافاصله ارسال می‌کند.
            // این رفتار مطلوب است (Catch-up mechanism).


            return [
                'hour'      => $randomHour,
                'minute'    => $randomMinute,
                'timestamp' => $targetDateTime->getTimestamp()
            ];
        });
    }
}
