<?php

namespace KrubiK\Enums;
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
 * مدیریت انواع دکمه‌ها با قدرت PHP 8.1+ Enums
 * حذف کامل رشته‌های جادویی و جلوگیری از خطاهای تایپی
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
enum ButtonType: string
{
    // انواع استاندارد
    case Text = 'Text';
    case Link = 'Link';
    case Simple = 'Simple'; // نگاشت به مقدار استاندارد متد قدیمی
    
    // انواع انتخابی و ورودی
    case Selection = 'Selection';
    case NumberPicker = 'NumberPicker';
    case StringPicker = 'StringPicker';
    case Calendar = 'Calendar';
    case LocationPicker = 'Location'; // نام‌گذاری صریح‌تر
    case TextBox = 'TextBox';

    // انواع مدیا و فایل
    case Payment = 'Payment';
    case CameraImage = 'CameraImage';
    case CameraVideo = 'CameraVideo';
    case GalleryImage = 'GalleryImage';
    case GalleryVideo = 'GalleryVideo';
    case File = 'File';
    case Audio = 'Audio';
    case RecordAudio = 'RecordAudio';
    
    // انواع شخصی و تعاملی
    case MyPhoneNumber = 'MyPhoneNumber';
    case MyLocation = 'MyLocation';
    case ActivityPhoneNumber = 'ActivityPhoneNumber';
    case AsMLocation = 'AsMLocation';
    case Barcode = 'Barcode';

    /**
     * آیا این دکمه نیاز به دیتای اضافی (Payload) دارد؟
     * (جهت ولیدیشن هوشمند در آینده)
     */
    public function requiresPayload(): bool
    {
        return match($this) {
            self::Selection, self::NumberPicker, self::StringPicker, 
            self::Calendar, self::LocationPicker, self::TextBox => true,
            default => false,
        };
    }
}
