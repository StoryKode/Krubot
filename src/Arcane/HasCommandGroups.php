<?php

namespace KrubiK\Arcane;
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

use KrubiK\Router\RouteGroup;

trait HasCommandGroups
{
    /**
     * Stack to hold attributes of nested groups.
    */
    protected array $groupAttributesStack = [];
    
    /**
     * Temporary storage to track routes added within a group closure.
     * Used to return a RouteGroup object.
    */
    protected ?array $currentGroupRoutes = null;

    /**
     * Define a command group.
     * Returns a RouteGroup object to allow chaining ->middleware() on the whole group.
    */
    public function group(array|callable $attributesOrCallback, ?callable $callback = null): RouteGroup
    {
        // Normalize arguments: allow group(fn) or group([], fn)
        if (is_callable($attributesOrCallback) && is_null($callback)) {
            $callback = $attributesOrCallback;
            $attributes = [];
        } else {
            $attributes = $attributesOrCallback;
        }

        // 1. Push attributes to stack
        $this->groupAttributesStack[] = $attributes;
        
        // 2. Capture routes added in this scope
        $previousGroupRoutes = $this->currentGroupRoutes; // Handle nesting
        $this->currentGroupRoutes = [];

        // 3. Execute Closure
        $callback($this);

        // 4. Create Group Object with captured routes
        $group = new RouteGroup($this->currentGroupRoutes);

        // 5. Restore Previous State (Nesting support)
        // If we are inside another group, add these routes to the parent too
        if ($previousGroupRoutes !== null) {
            $previousGroupRoutes = array_merge($previousGroupRoutes, $this->currentGroupRoutes);
            $this->currentGroupRoutes = $previousGroupRoutes;
        } else {
            $this->currentGroupRoutes = null;
        }

        // 6. Pop attributes
        array_pop($this->groupAttributesStack);

        return $group;
    }

    /**
     * Get merged attributes.
    */
    protected function getGroupAttributes(): array
    {
        $final = [];
        foreach ($this->groupAttributesStack as $group) {
            // Merge logic (Middleware array merge, Prefix concat, etc.)
             foreach ($group as $key => $value) {
                if ($key === 'middleware') {
                    $current = $final[$key] ?? [];
                    $value = is_array($value) ? $value : [$value];
                    $final[$key] = array_merge($current, $value);
                } elseif ($key === 'prefix') {
                    $final[$key] = isset($final[$key]) ? trim($final[$key] . '/' . trim($value, '/'), '/') : trim($value, '/');
                } else {
                    $final[$key] = $value;
                }
            }
        }
        return $final;
    }
    
    /**
     * Internal: Called by onCommand/onText to register the route object to the current group tracker.
    */
    protected function registerRouteToGroup($routeObject): void
    {
        if ($this->currentGroupRoutes !== null) {
            $this->currentGroupRoutes[] = $routeObject;
        }
    }
}
