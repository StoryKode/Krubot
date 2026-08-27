<?php

namespace KrubiK\Conversations;
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

use Stringable;
use KrubiK\Keyboard\Keyboard;
use KrubiK\Keyboard\PowerButton;

class Question implements Stringable
{
    protected string $text;
    protected array $buttons = [];

    /**
     * Factory method برای ساخت سریع
     */
    public static function create(string $text): self
    {
        $instance = new self();
        $instance->text = $text;
        return $instance;
    }

    /**
     * افزودن یک دکمه به سوال
     */
    public function addButton(PowerButton $button): self
    {
        $this->buttons[] = $button;
        return $this;
    }

    /**
     * افزودن آرایه‌ای از دکمه‌ها
    */
    public function addButtons(array $buttons): self
    {
        foreach ($buttons as $button) {
            if ($button instanceof PowerButton) {
                $this->addButton($button);
            }
        }
        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    /**
     * تبدیل شیء به رشته (برای استفاده در متن پیام)
    */
    public function __toString(): string
    {
        return $this->text;
    }
    
    /**
     * تبدیل نهایی به آبجکت قدرتمند Keyboard در KrubiK.
     * این متد دکمه‌های جمع‌آوری شده را به انجین کیبورد می‌دهد
     * و دستور chunk(2) را برای چیدمان دو‌تایی صادر می‌کند.
    */
    public function toKeyboard(): Keyboard
    {

        return Keyboard::make()->buttons($this->buttons)->chunk(2);

        /* // 1. ایجاد نمونه جدید از کیبورد
        $keyboard = new Keyboard();

        // 2. تزریق تمام دکمه‌های ذخیره شده به "pending buttons" کیبورد
        // نکته: متد buttons() در کلاس Keyboard شما باید آرایه دکمه‌ها را بپذیرد
        $keyboard->buttons($this->buttons);

        // 3. اعمال منطق Chunk 2 (دو ستونه)
        // این متد در کلاس Keyboard شما در لحظه toArray()، دکمه‌ها را تقسیم می‌کند
        $keyboard->chunk(2);

        return $keyboard; */
    }

    /***
     * تبدیل به فرمت کیبورد Krubot
     * (این متد پل ارتباطی بین UniChatKit-Style و Krubot است)
    * /
    public function toKeyboard(): array
    {
        $rows = [];
        // یک منطق ساده: هر دو دکمه در یک ردیف
        $chunks = array_chunk($this->buttons, 2);
        
        foreach ($chunks as $chunk) {
            $row = [];
            /** @var PowerButton $btn * /
            foreach ($chunk as $btn) {
                $row[] = $btn->toArray();
            }
            $rows[] = $row;
        }
        return $rows;
    } * /

    /*****
     * تبدیل دکمه‌های ذخیره شده به آبجکت استاندارد و بومی KrubiK Keyboard
     * با رعایت منطق "دو دکمه در هر سطر".
     * /
    public function toKeyboard(): Keyboard
    {
        // 1. ایجاد نمونه جدید از کیبورد بومی
        $keyboard = Keyboard::make();

        // 2. منطق اختصاصی: تقسیم دکمه‌ها به دسته‌های 2 تایی
        $chunks = array_chunk($this->buttons, 2);

        // 3. ساخت ردیف‌ها در آبجکت کیبورد
        foreach ($chunks as $chunk) {
            $keyboard->row(function($rowBuilder) use ($chunk) {
                foreach ($chunk as $btn) {
                    // فرض بر این است که $btn متد toArray دارد یا آرایه است
                    $data = is_object($btn) && method_exists($btn, 'toArray') 
                            ? $btn->toArray() 
                            : (array)$btn;

                    $text = $data['text'] ?? 'Button';
                    
                    // تشخیص لینک یا دکمه ساده
                    if (isset($data['url'])) {
                         $rowBuilder->link($text, $data['url']);
                    } else {
                        // تلاش برای یافتن ولیو
                        $value = $data['value'] ?? $data['name'] ?? $data['callback_data'] ?? null;
                        $rowBuilder->add($text, $value);
                    }
                }
            });
        }

        return $keyboard;
    } */
}
