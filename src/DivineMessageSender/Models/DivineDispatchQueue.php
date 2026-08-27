<?php

namespace KrubiK\DivineMessageSender\Models;
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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DivineDispatchQueue extends Model
{
    protected $table = 'divine_dispatch_queue';

    protected $fillable = [
        'user_id',
        'section_index',
        'scheduled_for_date',
        'scheduled_at',
        'payload',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'payload'      => 'array',
    ];

    public static function existsFor(int $userId, int $sectionIndex, Carbon $date): bool
    {
        return static::query()
            ->where('user_id', $userId)
            ->where('section_index', $sectionIndex)
            ->whereDate('scheduled_for_date', $date->toDateString())
            ->exists();
    }

    public static function enqueue(int $userId, int $sectionIndex, Carbon $scheduledAt, array $payload): void
    {
        static::query()->updateOrCreate(
            [
                'user_id'            => $userId,
                'section_index'      => $sectionIndex,
                'scheduled_for_date' => $scheduledAt->toDateString(),
            ],
            [
                'scheduled_at' => $scheduledAt,
                'payload'      => $payload,
            ]
        );
    }
}
