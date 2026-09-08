<?php

declare(strict_types=1);

namespace KrubiK\Render\Kernel;

use KrubiK\Render\RichElements\RichEntity;
use KrubiK\Render\RichMan;
use LogicException;
use Throwable;

use function KrubiK\Render\Helpers\plain;

/**
 * ReactorFlux — یک Session کاملاً مستقل و خودکفا برای ضبط داستان Rich.
 *
 * این کلاس تمام state مربوط به یک عملیات capture را داخل خودش نگه می‌دارد.
 * هیچ وابستگی به static $active یا singleton BladeCipher ندارد.
 *
 * استفاده:
 *   $flux = ReactorFlux::charge();
 *   // ... directives / eval ...
 *   $harvestor = $flux->shutdown();
*/
final class ReactorFlux
{
    /** @var RichMan[] */
    private array $composerStack = [];

    /** @var array<int, array{name: string, args: array}> */
    private array $argumentStack = [];

    /** @var array<string, RichMan> */
    private array $harvested = [];

    private bool $isCapturing = false;

    /** سطح بافر خروجی در لحظه‌ی شروع Session */
    private int $startedObLevel = 0;

    public static function spawn(): static
    {
        return new static();
    }

    public static function charge(): static
    {
        $flux = static::spawn();
        $flux->boot();
        return $flux;
    }

    // ────────────────────────────────────────────────
    // Lifecycle
    // ────────────────────────────────────────────────

    /**
     * شروع ضبط. یک composer ریشه می‌سازد و بافر خروجی را باز می‌کند.
    */
    public function boot(): void
    {
        if ($this->isCapturing) {
            throw new LogicException(
                'Cannot start a new capture while another is already in progress inside this session.'
            );
        }

        $this->startedObLevel = ob_get_level();
        ob_start();

        $this->composerStack = [RichMan::summon()];
        $this->argumentStack = [];
        $this->harvested = [];
        $this->isCapturing = true;

        BladeCipher::activate(true);
        BladeCipher::setActiveReactor($this);
    }

    /**
     * پایان ضبط و بازگرداندن نتیجه‌ی نهایی.
    */
    public function shutdown(): ?SoulHarvestor
    {
        if (!$this->isCapturing) {
            return null;
        }

        try {
            $this->captureBufferedContent();
            $this->flushNestedComponents();

            if (count($this->composerStack) !== 1) {
                throw new LogicException(
                    'Mismatched component directives. A closing tag is likely missing.'
                );
            }

            $finalComposer = array_pop($this->composerStack);

            return SoulHarvestor::feed($finalComposer, $this->harvested);
        } finally {
            $this->cleanup();
        }
    }

    /**
     * پایان + رندر مستقیم به رشته.
    */
    public function finalize(): string
    {
        $masterpiece = $this->shutdown();
        return $masterpiece?->render() ?? '';
    }

    // ────────────────────────────────────────────────
    // Component Management
    // ────────────────────────────────────────────────

    /**
     * شروع یک کامپوننت ساختاری (مثل @Details, @Bold block و ...)
    */
    public function startComponent(string $helperFunctionName, array $arguments = []): void
    {
        $this->ensureCapturing();
        $this->captureBufferedContent();

        $this->argumentStack[] = [
            'name' => $helperFunctionName,
            'args' => $arguments,
        ];

        $this->composerStack[] = RichMan::summon();
    }

    /**
     * پایان یک کامپوننت ساختاری.
    */
    public function endComponent(): void
    {
        $this->ensureCapturing();
        $this->captureBufferedContent();

        if (count($this->composerStack) < 2) {
            throw new LogicException(
                'Mismatched component directives. Found a closing tag with no matching opening tag.'
            );
        }

        $childComposer = array_pop($this->composerStack);
        $children = $childComposer->getElements();

        $parentData = array_pop($this->argumentStack);
        $helperName = $parentData['name'];
        $arguments  = $parentData['args'];
        $arguments[] = $children;

        /** @var RichEntity $entity */
        $entity = $helperName(...$arguments);

        $this->addComponent($entity);
    }

    /**
     * افزودن یک Entity ساده (void یا wrapper این‌لاین) به composer فعلی.
    */
    public function addComponent(RichEntity $element): void
    {
        $this->ensureCapturing();
        $this->captureBufferedContent();

        if (!empty($this->composerStack)) {
            end($this->composerStack)->add($element);
        }
    }

    /**
     * برداشت داستان فعلی به یک کانال نام‌گذاری‌شده و شروع یک داستان جدید.
    */
    public function harvest(string $name): void
    {
        $this->ensureCapturing();
        $this->flushNestedComponents();

        // composer ریشه (ایندکس ۰) را برداشت می‌کنیم
        $this->harvested[$name] = $this->composerStack[0];

        // یک composer تازه جایگزین می‌کنیم تا ادامه داستان تمیز باشد
        $this->composerStack[0] = RichMan::summon();
    }

    // ────────────────────────────────────────────────
    // Internal Helpers
    // ────────────────────────────────────────────────

    private function captureBufferedContent(): void
    {
        if (!$this->isCapturing) {
            return;
        }

        if (ob_get_level() > $this->startedObLevel) {
            $buffered = ob_get_clean();
            if ($buffered !== false && $buffered !== '') {
                $this->addComponent(plain($buffered));
            }
            // دوباره بافر را باز می‌کنیم تا ادامه‌ی خروجی‌ها را بگیریم
            ob_start();
        }
    }

    /**
     * تمام کامپوننت‌های باز مانده را به صورت ایمن می‌بندد
     * (برای حالتی که کاربر @End... را فراموش کرده باشد).
    */
    private function flushNestedComponents(): void
    {
        while (count($this->composerStack) > 1) {
            $childComposer = array_pop($this->composerStack);
            $children = $childComposer->getElements();

            if (!empty($this->argumentStack)) {
                $parentData = array_pop($this->argumentStack);
                $helperName = $parentData['name'];
                $arguments  = $parentData['args'];
                $arguments[] = $children;

                try {
                    $entity = $helperName(...$arguments);
                    $this->addComponent($entity);
                } catch (Throwable) {
                    // Fail-safe: children را مستقیماً به parent فعلی اضافه کن
                    $target = end($this->composerStack);
                    foreach ($children as $child) {
                        $target->add($child);
                    }
                }
            } else {
                $target = end($this->composerStack);
                foreach ($children as $child) {
                    $target->add($child);
                }
            }
        }
    }

    private function cleanup(): void
    {
        $this->isCapturing = false;
        $this->composerStack = [];
        $this->argumentStack = [];
        $this->harvested = [];

        // فقط بافرهایی که خود این Session باز کرده را می‌بندیم
        while (ob_get_level() > $this->startedObLevel) {
            ob_end_clean();
        }

        BladeCipher::setActiveReactor(null);
    }

    private function ensureCapturing(): void
    {
        if (!$this->isCapturing) {
            throw new LogicException(
                'This ReactorFlux is not currently capturing. Call boot() first.'
            );
        }
    }

    // ────────────────────────────────────────────────
    // Debug / Introspection
    // ────────────────────────────────────────────────

    public function isCapturing(): bool
    {
        return $this->isCapturing;
    }

    public function getStackDepth(): int
    {
        return count($this->composerStack);
    }

    public function getHarvestedNames(): array
    {
        return array_keys($this->harvested);
    }
}
