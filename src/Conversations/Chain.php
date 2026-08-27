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

use Illuminate\Support\Collection;
use KrubiK\Krubot;
use KrubiK\Keyboard\Keyboard;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate_Database_Eloquent_Collection;

/**
 * Chain Class: The UI State Machine for Complex Menus.
 * 
 * This class extends Conversation to provide a robust framework for building
 * interactive, stateful, and navigable inline keyboard menus. It introduces
 * concepts like breadcrumb navigation, built-in pagination, and a centralized
 * state management system to dramatically improve Developer Experience (DX).
 * 
 * Base class for action-driven, chainable conversations (Menus, Wizards, etc.).
 * Its primary purpose is to set a more aggressive, button-friendly default
 * for automatically answering callback queries.
 *
 * @property-read Collection $state The persistent state container for the chain instance.
 * 
 * @author DoKtor K.
 * @link https://StoryKo.de/Krubot Official website of engine.
 * @version Krubot: ×RC.8×
 * @license MIT
*/
abstract class Chain extends Conversation
{
    /**
     * The heart of the Chain's state.
     * This collection holds all transient data, such as the current page,
     * applied filters, and the navigation breadcrumb stack.
     * It's automatically serialized and cached with the conversation.
     *
     * @var Collection
     */
    protected Collection $state;

    /**
     * Default behavior for Chains is to provide instant feedback on button clicks.
     * Set to `false` within a specific method for long-running operations.
     *
     * Override the default behavior to automatically answer callbacks.
     * This improves UX in button-heavy interfaces by immediately
     * removing the loading indicator from the clicked button.
     *
     * You can still set this to `false` in a concrete child class
     * for specific actions that require a longer processing time.
     *
     * @var bool|null
     */
    protected ?bool $autoAnswerCallback = true;
    
    /**
     * The main entry point for the developer's logic.
     * This method is called by `start()` after the initial state is set up.
     *
     * @param Krubot $bot
     * @return void
     */
    abstract public function main(Krubot $bot);

    // --- Constructor & Core ---

    /**
     * Initializes the Chain, ensuring the state container is ready.
     * It also passes any constructor arguments up to the parent Conversation.
     */
    public function __construct(...$args)
    {
        parent::__construct(...$args);
        $this->state = new Collection();
    }

    /**
     * The official starting point of the Chain.
     * It initializes the breadcrumb and makes the first jump to the `main` method.
     * This ensures a clean and predictable starting state.
     */
    public function start(Krubot $bot): void
    {
        // Initialize the breadcrumb with the main entry point.
        $this->state->put('breadcrumb', []);
        $this->jump('main');
    }

    // --- Navigation Engine (Breadcrumbs & Jumps) ---

    /**
     * Jumps to a new state (method) within the Chain and pushes it onto the breadcrumb stack.
     * This is the primary method for navigating forward in menus.
     *
     * @param string $method The name of the method to execute.
     * @param mixed ...$args Arguments to pass to the target method.
     */
    public function jump(string $method, ...$args): void
    {
        // Prevent infinite loops by not adding the same consecutive state twice.
        $lastCrumb = collect($this->state->get('breadcrumb'))->last();
        if (!$lastCrumb || $lastCrumb['method'] !== $method) {
            $this->state->push('breadcrumb', ['method' => $method, 'args' => $args]);
        }
        
        // Execute the target method.
        call_user_func_array([$this, $method], $args);
    }
    
    /**
     * Navigates back to the previous state in the breadcrumb stack.
     * It automatically pops the current state and executes the one before it.
     * If there's no previous state, it gracefully jumps home.
     */
    public function back(): void
    {
        $breadcrumb = $this->state->get('breadcrumb', []);
        
        // Pop the current state.
        array_pop($breadcrumb);

        // Get the previous state.
        $previous = array_pop($breadcrumb);
        $this->state->put('breadcrumb', $breadcrumb);

        if ($previous) {
            $this->jump($previous['method'], ...$previous['args']);
        } else {
            // No history left, go to the main screen.
            $this->home();
        }
    }

    /**
     * Jumps to the main entry point of the Chain, effectively resetting the navigation history.
     */
    public function home(): void
    {
        $this->start($this->bot);
    }

    /**
     * Checks if navigation back is possible.
     */
    public function canGoBack(): bool
    {
        return $this->state->has('breadcrumb') && count($this->state->get('breadcrumb')) > 1;
    }
    
    // --- Pagination Engine ---

    /**
     * Renders a paginated list of items as keyboard buttons.
     *
     * @param LengthAwarePaginator|array $items The data source.
     * @param \Closure $buttonFactory A closure that receives a single item and must return a Button instance.
     * @param int $columns Number of columns for the item buttons.
     * @return Keyboard
     */
    protected function paginate($items, \Closure $buttonFactory, int $columns = 1): Keyboard
    {
        $page = $this->state->get('pagination.page', 1);

        if (is_array($items)) {
            $items = new LengthAwarePaginator($items, count($items), 10, $page);
        }
        
        $keyboard = Keyboard::make();
        $itemButtons = [];
        
        foreach ($items->items() as $item) {
            $itemButtons[] = $buttonFactory($item);
        }
        
        // Add item buttons with specified column layout.
        if (!empty($itemButtons)) {
            $keyboard->buttons($itemButtons)->chunk($columns);
        }
        
        // Add pagination controls.
        $keyboard->row(function() use ($items) {
            $row = [];
            if ($items->previousPageUrl()) {
                $row[] = Button::make("⬅️ قبلی")->action('renderPage', ['page' => $items->currentPage() - 1]);
            }
            if ($items->hasMorePages()) {
                $row[] = Button::make("بعدی ➡️")->action('renderPage', ['page' => $items->currentPage() + 1]);
            }
            return $row;
        });

        return $keyboard;
    }

    /**
     * The internal action handler for pagination button clicks.
     * It updates the page state and re-renders the current main view.
     */
    public function renderPage(int $page): void
    {
        $this->state->put('pagination.page', $page);
        
        // Pop the "renderPage" action from breadcrumb and re-run the previous method.
        $breadcrumb = $this->state->get('breadcrumb');
        array_pop($breadcrumb); // Remove renderPage call
        $lastView = end($breadcrumb); // Get the method that created the pagination

        $this->state->put('breadcrumb', $breadcrumb);
        $this->jump($lastView['method'], ...$lastView['args']);
    }

    // --- Keyboard DX Helpers ---

    /**
     * Adds a standardized "Back" button to the keyboard if navigation is possible.
     */
    public function addBackButton(Keyboard $keyboard): static
    {
        if ($this->canGoBack()) {
            $keyboard->addRowButton('⬅️ بازگشت', fn($btn) => $btn->action('back'));
        }
        return $this;
    }
    
    /**
     * Adds a standardized "Home" button to the keyboard.
     */
    public function addHomeButton(Keyboard $keyboard): static
    {
        $keyboard->addRowButton('🏠 منوی اصلی', fn($btn) => $btn->action('home'));
        return $this;
    }
}
