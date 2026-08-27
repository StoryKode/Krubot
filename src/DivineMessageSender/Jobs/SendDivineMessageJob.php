<?php

namespace KrubiK\DivineMessageSender\Jobs;
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

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use KrubiK\Helpers\AmethystMatrix as Log;
use App\Models\User;
use KrubiK\Krubot;
use KrubiK\DivineMessageSender\Models\DivineMessage;

class SendDivineMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The user to receive the message.
     */
    protected User $user;

    /**
     * The time section index (0, 1, 2).
     */
    protected int $sectionIndex;

    /**
     * The lock key to ensure idempotency.
    */
    protected string $lockKey;

    /**
     * The precise timestamp when the lock should release (Start of Next Section).
    */
    protected \DateTimeInterface $lockExpiration;

    /**
     * Create a new job instance.
     *
     * @param Krubot $bot
     * @param User $user
     * @param int $sectionIndex
     * @param string $lockKey کلیدی که پس از ارسال موفق در کش ست می‌شود
     * @param \DateTimeInterface $lockExpiration
     */
    public function __construct(User $user, int $sectionIndex, string $lockKey, \DateTimeInterface $lockExpiration)
    {
        $this->user = $user;
        $this->sectionIndex = $sectionIndex;
        $this->lockKey = $lockKey;
        $this->lockExpiration = $lockExpiration;
    }

    /**
     * Execute the job.
     *
     * @param Krubot $bot
     * 
     * لاراول به قدری هوشمند است که وقتی یک `Job` را از صف پردازش می‌کند، خودش وابستگی‌های متد `handle` را نیز **تزریق** می‌کند. این زیباترین و تمیزترین راه حل است.
     * 
     */
    public function handle(Krubot $bot): void
    {
        // 00. Global Kill-Switch Check .00
        if (!config('krubot.divine_sender.enabled', true)) {
             // اگر کل سیستم خاموش شده، جاب را بیخیال شو
             return; 
        }

        // Double-Check Locking (Concurrency Safety Check)
        // ممکن است بین زمان دیسپچ و اجرا، جاب دیگری اجرا شده باشد.
        // اگر به هر دلیلی قبلاً ارسال شده، بیخیال شو

        // Idempotency Check
        // If lock exists, so it means that this message was already sent (or handled by another worker)
        if (Cache::has($this->lockKey)) {
            return; 
        }

        /*$now = now();
        $sentLockKey = "divine_sent:{$this->user->id}:{$now->format('Y-m-d')}:sec_{$this->sectionIndex}";

        // قفل نهایی: اگر به هر دلیلی این جاب دوبار در صف قرار گرفت، اینجا متوقف شود.
        if (Cache::has($sentLockKey)) {
            Log::warning("Divine message for user {$this->user->id} in section {$this->sectionIndex} was already sent. Job skipped.");
            return;
        }*/

        // [NEW] 1.5. Config Validity Check (Safety Net)
        // اطمینان حاصل می‌کنیم سکشنی که قبلا برنامه‌ریزی شده، هنوز در کانفیگ وجود دارد
        $allowedSections = config('krubot.divine_sender.allowed_hours_sections', []);
        if (!isset($allowedSections[$this->sectionIndex])) {
            Log::warning("DivineJob Skipped: Section {$this->sectionIndex} no longer exists in config for User {$this->user->id}.");
            return;
        }

        try {
            // KPI Logic (Logic moved from Dispatcher to Job)
            // 1. KPI Calculation (Heavy Logic)

            // Just-In-Time KPI Calculation
            // We calculate KPI *now* (at delivery time), not at planning time.
            $goal = (int) $this->user->getTodaysGoal();
            $achieved = (int) $this->user->getTodaysAcheivedSales();
            $percentage = ($goal > 0) ? (($achieved / $goal) * 100) : 0.0;
            $bucketIndex = min((int) floor($percentage / 20), 4);

            // 2. Select Payload from DB
            // Fetch Payload
            // از متد استاتیک مدل یا لاجیک خودتان استفاده کنید
            $payload = DivineMessage::randomMessagePayload($this->sectionIndex, $bucketIndex);
            $messageContent = $payload['content'] ?? __('divine_msg_default');

            // 3. Send via Krubot
            // ارسال پیام با استفاده از Krubot::for()
            // Using 'for()' to target specific user
            $bot->for($this->user->rubika_user_id)
                ->message(__('divine_msg_header') . $messageContent)
                ->send();

            // 5. Apply Smart Lock
            // Lock allows us to skip redundant jobs and expires exactly when next section starts.
            Cache::put($this->lockKey, true, $this->lockExpiration);

            // 4. قفل کردن این سکشن برای امروز (مهمترین بخش)
            // این قفل به "برنامه‌ریز" می‌گوید که دیگر برای این کاربر در این سکشن برنامه‌ریزی نکند.
            //-// Cache::put($sentLockKey, true, $now->endOfDay()); //-//

            // 4. Final Lock (Success)
            // قفل تا آخر شب باقی می‌ماند
            //-// Cache::put($this->lockKey, true, now()->endOfDay());

            // Optional: Log success
            // Log::info("Divine Msg sent to {$this->user->id} | Sec: {$this->sectionIndex}");
            // Log::info("✅ Divine message successfully sent to user {$this->user->id}. [Sec: {$this->sectionIndex}]");

        } catch (\Throwable $e) {
            Log::error("Failed to send divine message via job for user {$this->user->id}. Reason: {$e->getMessage()}");
            // جاب به صورت خودکار برای تلاش مجدد به صف برمی‌گردد.
            $this->release(60); // 60 ثانیه دیگر دوباره تلاش کن

            // Log::error("DivineJob Failed for User {$this->user->id} | " . $e->getMessage());
            // نکته: ما اینجا جاب را fail نمی‌کنیم تا دوباره تلاش کند (مگر اینکه بخواهید retry داشته باشد)
            // اگر فیل شود، قفل ست نمی‌شود و در سیکل ۱۰ دقیقه‌ای بعدی دوباره کاندید می‌شود.

            // جاب را فیل نمی‌کنیم تا در صف نماند (Retry با استراتژی شما همخوانی ندارد چون زمان‌دار است)
        }
    }
}
