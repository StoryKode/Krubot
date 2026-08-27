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
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes; // <--- [NEW] Add this import

/**
 * KrubiK\DivineMessageSender\Models\DivineMessage
 *
 * Table: divine_messages
 *
 * Columns:
 *  - id (PK)
 *  - section_index (unsignedTinyInteger)  // vertical index (section)
 *  - bucket_index  (unsignedTinyInteger)  // horizontal index (performance bucket)
 *  - content (text)
 *  - created_at, updated_at
 *
 * Responsibilities:
 *  - Provide lightweight queries for candidate IDs (fresh each call).
 *  - Provide cached payload retrieval (Map-style: key=id -> value=content).
 *  - Invalidate cached content automatically on update/delete.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
class DivineMessage extends Model
{
    use SoftDeletes; // <--- [NEW] Enable Soft Deletes trait

    // Table name follows your "English-dev-style" request.
    protected $table = 'divine_messages';

    // Mass assignable fields (explicit).
    protected $fillable = [
        'section_index',
        'bucket_index',
        'content',
        'weight',    // <--- [NEW]
        'is_active', // <--- [NEW]
    ];

    // Casts for strictness.
    protected $casts = [
        'id' => 'integer',
        'section_index' => 'integer',
        'bucket_index' => 'integer',
        'content' => 'string',
        'weight' => 'integer',    // <--- [NEW]
        'is_active' => 'boolean', // <--- [NEW]
    ];

    // Cache TTL in seconds for message content cache (Map-style).
    private const CACHE_TTL_SECONDS = 86400; // 24 hours

    // Cache key prefix for message content.
    private const CACHE_KEY_PREFIX = 'divine_msg_content_';

    // Helpful constants for domains (optional but convenient).
    public const SECTION_MORNING = 0;
    public const SECTION_MIDDAY  = 1;
    public const SECTION_EVENING = 2;

    /**
     * Boot model events to invalidate content cache on change.
     *
     * @return void
     */
    protected static function booted(): void
    {
        // Invalidate cache when a message is updated.
        static::saved(function (DivineMessage $model): void {
            Cache::forget(self::cacheKeyFor($model->id));
        });

        // Invalidate cache when a message is deleted.
        static::deleted(function (DivineMessage $model): void {
            Cache::forget(self::cacheKeyFor($model->id));
        });
    }

    /**
     * Scope: filter by section index.
     *
     * @param Builder $q
     * @param int $section
     * @return Builder
     */
    public function scopeSection(Builder $q, int $section): Builder
    {
        return $q->where('section_index', $section);
    }

    /**
     * Scope: filter by bucket index.
     *
     * @param Builder $q
     * @param int $bucket
     * @return Builder
     */
    public function scopeBucket(Builder $q, int $bucket): Builder
    {
        return $q->where('bucket_index', $bucket);
    }

    /**
     * Return a Cache key for a given message id.
     *
     * @param int $id
     * @return string
     */
    public static function cacheKeyFor(int $id): string
    {
        return self::CACHE_KEY_PREFIX . (int) $id;
    }

    /**
     * Fetch content by id using Map-style cache.
     * If the id does not exist, returns null.
     *
     * @param int $id
     * @return string|null
     */
    public static function getContentCached(int $id): ?string
    {
        $key = self::cacheKeyFor($id);

        return Cache::remember($key, Carbon::now()->addSeconds(self::CACHE_TTL_SECONDS), function () use ($id) {
            $rec = self::find($id);
            return $rec ? $rec->content : null;
        });
    }

    /**
     * Get an array/collection of candidate IDs for given section & bucket.
     * This method purposely queries IDs fresh each call so new rows can appear immediately.
     *
     * @param int $section
     * @param int $bucket
     * @return \Illuminate\Support\Collection<int>
     */
    public static function candidateIdsFor(int $section, int $bucket)
    {
        return self::query()
            ->where('section_index', $section)
            ->where('bucket_index', $bucket)
            ->where('is_active', true) // <--- [NEW] Only pick active messages
            ->orderByDesc('weight') // // Optional: You can sort by weight here if needed, e.g., 
            ->pluck('id');
    }

    /**
     * Get a random candidate id for given section & bucket.
     * Returns null when no candidate exists.
     *
     * @param int $section
     * @param int $bucket
     * @return int|null
     */
    public static function randomIdFor(int $section, int $bucket): ?int
    {
        $ids = self::candidateIdsFor($section, $bucket);
        if ($ids->isEmpty()) {
            return null;
        }
        return (int) $ids->random();
    }

    /**
     * High-level helper: return associative array ['id' => int, 'content' => string]
     * Implements the fallback cascade:
     *  1) section & bucket
     *  2) section & bucket=0
     *  3) section=0 & bucket=0
     *  4) emergency fallback (id=0, content=hardcoded)
     *
     * Note: This method queries IDs fresh (candidateIdsFor) and uses cached content retrieval.
     *
     * @param int $section
     * @param int $bucket
     * @return array{id:int, content:string}
     */
    public static function randomMessagePayload(int $section, int $bucket): array
    {
        // 1) try exact section & bucket
        $selectedId = self::randomIdFor($section, $bucket);

        // 2) fallback: same section, bucket 0
        if ($selectedId === null) {
            $selectedId = self::randomIdFor($section, 0);
        }

        // 3) fallback: section 0, bucket 0
        if ($selectedId === null) {
            $selectedId = self::randomIdFor(0, 0);
        }

        // 4) final emergency fallback
        if ($selectedId === null) {
            return [
                'id' => 0,
                'content' => 'مسیر نور همیشه باز است. ادامه بده.' // short emergency Persian text
            ];
        }

        // Get cached content (Map-style)
        $content = self::getContentCached($selectedId);

        // If for some reason content is null (row deleted between selection and fetch), try again recursively but guard depth.
        if ($content === null) {
            // Remove the faulty id from DB candidate (best-effort) and try again without caching loop explosion.
            // We'll simply delete cache key (already null) and attempt to pick another id from the same bucket.
            Cache::forget(self::cacheKeyFor($selectedId));
            // Attempt one more time: get fresh ids and pick another.
            $ids = self::candidateIdsFor($section, $bucket)->reject(fn($x) => $x == $selectedId);
            if ($ids->isNotEmpty()) {
                $nextId = (int) $ids->random();
                $content = self::getContentCached($nextId);
                if ($content !== null) {
                    return ['id' => $nextId, 'content' => $content];
                }
            }
            // Fall back to the emergency string to avoid throwing.
            return [
                'id' => 0,
                'content' => 'مسیر نور همیشه باز است. ادامه بده.'
            ];
        }

        return [
            'id' => (int) $selectedId,
            'content' => $content,
        ];
    }
}
