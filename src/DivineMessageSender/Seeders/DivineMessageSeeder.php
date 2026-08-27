<?php

namespace KrubiK\DivineMessageSender\Seeders;
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

use App\Models\DivineMessage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

// Install with run:
// php artisan db:seed --class=DivineMessageSeeder

// Test with ['script']
// use App\Models\DivineMessage; dd(DivineMessage::where('is_active', true)->get()->take(5)->toArray());

class DivineMessageSeeder extends Seeder
{
    /**
     * 2D Matrix for Messages provided by DoKtor K.
     */
    private const MESSAGE_BUCKETS = [
        // ==========================================
        // SECTION 0: MORNING (The Awakening)
        // ==========================================
        0 => [
            0 => [ // 0-19% (Crisis/Hope)
                "☀️ بسم‌الله. هنوز صفحه امروز سفیده. یه جوری بنویسش که شب حال کنی.",
                "قهرمان! عدد صفر یعنی بی‌نهایت پتانسیل. استارت بزن، خدا برکت میده.",
                "اول صبحی فقط یه قدم کوچیک بردار. همون یه قدم، معجزه می‌کنه.",
            ],
            1 => [ // 20-39% (Warm Up)
                "عالی شروع کردی! موتورت گرم شده، حالا دنده رو عوض کن.",
                "سحرخیز بودی، کامروا هم باش. سرعتت خوبه، ولی مقصد دورتره.",
                "باریکلا! یه تکون دیگه بدی به نصف هدف رسیدی قبل از ناهار.",
            ],
            2 => [ // 40-59% (Halfway)
                "چه کردی اول صبحی؟! نصف راه رو رفتی. دمت گرم.",
                "سرعتت عالیه. امروز روز توئه، شک نکن.",
                "ماشالله به این اراده. یه نفس عمیق بکش و ادامه بده.",
            ],
            3 => [ // 60-79% (Pushing)
                "طوفانی شروع کردی! امروز رکورد می‌زنی، من مطمئنم.",
                "بوی موفقیت میاد. همین فرمون رو بگیر، عصر پادشاهی.",
                "چیزی نمونده تا تیکِ سبزِ تارگت. بجنگ پهلوون.",
            ],
            4 => [ // 80-100%+ (Victory/Gratitude)
                "بابا تو دیگه کی هستی؟! تارگت رو اول صبح لوله کردی!",
                "شکر نعمت فراموش نشه. الان وقتشه دست یکی دیگه رو هم بگیری.",
                "امروز رو تاریخ‌ساز کردی. حالا با خیال راحت کیفیت رو ببر بالا.",
            ],
        ],

        // ==========================================
        // SECTION 1: MIDDAY (The Persistence)
        // ==========================================
        1 => [
            0 => [
                "ظهر شده و هنوز خبری نیست؟ عیبی نداره. نیمه دوم بازی مهم‌تره.",
                "ناامیدی کار شیطانه. یه وضو، یه چای، یه 'یا علی'. بلند شو.",
                "هنوز وقت هست. بازار تازه داره گرم می‌شه. تو هم گرم شو.",
            ],
            1 => [
                "خوبه، ولی راضی نباش. پتانسیل تو خیلی بیشتر از این حرفاست.",
                "خستگی ممنوع! الان وقت شل کردن نیست، وقت فشار آوردنه.",
                "یه تکون به خودت بدی، آمار از این رو به اون رو میشه.",
            ],
            2 => [
                "نصف روز، نصف هدف. تعادلت خوبه ولی ما دنبال قهرمانی هستیم.",
                "وسط راهی. نه برگرد، نه وایسا. فقط گاز بده.",
                "خدا قوت! یه همت دیگه کنی، از سرازیری رد میشی.",
            ],
            3 => [
                "آفرین! بوی کباب موفقیت داره میاد. یکم دیگه باد بزن.",
                "اعدادت دارن می‌خندن. کم نیار که آخراشه.",
                "عالی پیش رفتی. نذار خستگی ظهر، سرعتت رو بگیره.",
            ],
            4 => [
                "ناهار رو با طعم پیروزی بخور! دمت گرم واقعا.",
                "امروز رو ترکوندی. بقیه روز رو ریلکس کن یا رکورد بزن.",
                "خدا برکت داده به وقتت. شکر یادت نره.",
            ],
        ],

        // ==========================================
        // SECTION 2: EVENING (The Accounting)
        // ==========================================
        2 => [
            0 => [
                "روز تموم شده؟ نه تا وقتی که تو نخوای. معجزه دقیقه ۹۰ رو دیدی؟",
                "شاید امروز روزت نبوده، ولی شبِت می‌تونه باشه. یه حرکت بزن.",
                "فدا سرت. ولی قبل خواب یه دونه فروش بزن که وجدانت آروم بخوابه.",
            ],
            1 => [
                "هنوز چراغ‌ها روشنه. یه تلاش آخر می‌تونه آبروداری کنه.",
                "کم نیار. قهرمان‌ها توی خستگی‌های آخر وقت ساخته میشن.",
                "یه یا علی دیگه بگو. خدا بیدارترینه.",
            ],
            2 => [
                "بد نبود، ولی عالی هم نبود. فردا رو باید بهتر بسازی.",
                "خدا قوت. پرونده امروز رو با یه حس خوب ببند.",
                "تلاشت مقدس بود. نتیجه دست خداست. برو استراحت کن.",
            ],
            3 => [
                "حیفه! چیزی نمونده تا صد. یه تلفن دیگه، یه پیگیری دیگه...",
                "خستگی در میره، ولی افتخار میمونه. تمومش کن کارو.",
                "عالی بودی. یه فشار کوچیک دیگه، فقط برای دل خودت.",
            ],
            4 => [
                "شب بخیر قهرمان. سرت رو بالا بگیر و راحت بخواب.",
                "گل کاشتی! رزق حلالت گوارای وجود.",
                "امشب ستاره‌ها به تلاشت چشمک می‌زنن. دمت گرم.",
            ],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $totalInserted = 0;

        foreach (self::MESSAGE_BUCKETS as $sectionIndex => $buckets) {
            foreach ($buckets as $bucketIndex => $messages) {
                foreach ($messages as $content) {
                    // Calculate "Magical Weight" dynamically
                    $weight = $this->calculateManaWeight($content, $bucketIndex);

                    DivineMessage::create([
                        'section_index' => $sectionIndex,
                        'bucket_index'  => $bucketIndex,
                        'content'       => $content,
                        'weight'        => $weight,
                        'is_active'     => true,
                    ]);

                    $totalInserted++;
                }
            }
        }

        $this->command->info("✨ DivineMatrix Loaded: {$totalInserted} messages injected with magical weights.");
    }

    /**
     * Calculates the 'weight' of a message based on its spiritual and emotional content.
     * 
     * Algorithm Logic:
     * 1. Base Weight: 10 (Standard)
     * 2. Spiritual Keywords (God, Start, Gratitude) => +15 (Priority)
     * 3. Power Keywords (Hero, Champion, Will) => +10
     * 4. Action Keywords (Start, Go, Push) => +5
     * 5. Short & Punchy (Length < 60 chars) => +5 (Readability Bonus)
     * 6. Crisis/Hope Buckets (0) get a slight boost to ensure empathy visibility.
     *
     * @param string $content
     * @param int $bucketIndex
     * @return int
     */
    private function calculateManaWeight(string $content, int $bucketIndex): int
    {
        $weight = 10; // Base weight

        // 1. Spiritual & Deep Impact (Highest Priority)
        if (Str::contains($content, ['خدا', 'بسم‌الله', 'یا علی', 'شکر', 'برکت', 'مقدس', 'رزق'])) {
            $weight += 15;
        }

        // 2. Ego & Identity (High Priority)
        if (Str::contains($content, ['قهرمان', 'پهلوان', 'تاریخ‌ساز', 'پادشاهی', 'معجزه'])) {
            $weight += 10;
        }

        // 3. Encouragement & Intimacy (Medium Priority)
        if (Str::contains($content, ['دمت گرم', 'بابا تو دیگه', 'فدا سرت', 'ماشالله', 'باریکلا'])) {
            $weight += 8;
        }

        // 4. Action Triggers
        if (Str::contains($content, ['استارت', 'گاز بده', 'حرکت', 'بجنگ'])) {
            $weight += 5;
        }

        // 5. Length Bonus: People read short messages more often.
        if (mb_strlen($content) < 60) {
            $weight += 5;
        }

        // 6. Contextual Boost for Crisis (Bucket 0 needs high variety/rotation)
        if ($bucketIndex === 0) {
            $weight += 3;
        }

        // Cap weight at 255 (tinyInteger unsigned max)
        return min($weight, 255);
    }
}
