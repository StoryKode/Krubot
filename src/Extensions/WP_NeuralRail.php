<?php

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

declare(strict_types=1);

/*
| WP_NeuralRail — HyperDX Hook Highway // Architect-Grade Bridge
|--------------------------------------------------------------------------
| A global procedural facade that jacks WordPress-style actions/filters straight into JackPoint.
| Every add_action/do_action and add_filter/apply_filters becomes a clean, low-friction signal lane.
| No magic smoke: just a sharp, deterministic relay from your code to the CoreSynapse.
| Plug in. Fire hooks. Bend flow. The rail is live.
*/

// GLOBAL NAMESPACE ENFORCEMENT 🌍
// All functions in this block are explicitly registered in the global namespace to mimic WordPress Procedural API.
namespace {

    // `use`s must be inside namespace block to grant access from here.
    use KrubiK\Helpers\JackPoint as JP;

    /**
     * =====================================================================
     * 🧬 JACKPOINT PROCEDURAL API (WORDPRESS-STYLE)
     * =====================================================================
     * This file provides global helper functions that mimic the WordPress
     * Plugin API (Hooks, Actions, and Filters), routing directly to the
     * JackPoint class (and therefore into the CoreSynapse).
     * 
     * @package KrubiK\Extensions\CoreSynapse
     * @subpackage WP_Plugin_API
     * =====================================================================
    */

    // =====================================================================
    // 🎬 ACTION API
    // =====================================================================

    if ( ! function_exists( 'add_action' ) ) {
        /**
         * Hooks a function on to a specific action.
         *
         * Actions are the hooks that the core launches at specific points
         * during execution, or when specific events occur.
         *
         * @since 1.0.0
         *
         * @param string          $event    The name of the action to which the $listener is hooked.
         * @param callable        $listener The callback to be run when the action is called.
         * @param int             $priority Optional. Used to specify the order in which the functions
         *                                  associated with a particular action are executed. 
         *                                  Default JP::PRIORITY_NORMAL.
         * @param string|int|null $id       Optional. Specific ID for the listener.
         * @return string|int The registered listener ID.
        */
        function add_action( string $event, callable $listener, int $priority = JP::PRIORITY_NORMAL, string|int|null $id = null ): string|int {
            return JP::addAction( $event, $listener, $priority, $id );
        }
    }

    if ( ! function_exists( 'add_action_before' ) ) {
        /**
         * Hooks a function on to a specific action, executing before the normal priority band.
         *
         * @since 1.0.0
         *
         * @param string          $event    The name of the action.
         * @param callable        $listener The callback to be run.
         * @param string|int|null $id       Optional. Specific ID for the listener.
         * @return string|int The registered listener ID.
        */
        function add_action_before( string $event, callable $listener, string|int|null $id = null ): string|int {
            return JP::addActionBefore( $event, $listener, $id );
        }
    }

    if ( ! function_exists( 'add_action_after' ) ) {
        /**
         * Hooks a function on to a specific action, executing after the normal priority band.
         *
         * @since 1.0.0
         *
         * @param string          $event    The name of the action.
         * @param callable        $listener The callback to be run.
         * @param string|int|null $id       Optional. Specific ID for the listener.
         * @return string|int The registered listener ID.
        */
        function add_action_after( string $event, callable $listener, string|int|null $id = null ): string|int {
            return JP::addActionAfter( $event, $listener, $id );
        }
    }

    if ( ! function_exists( 'do_action' ) ) {
        /**
         * Calls the callback functions that have been added to an action hook.
         *
         * This function invokes all functions attached to action hook `$event`.
         * It is possible to create new action hooks by simply calling this function,
         * specifying the name of the new hook using the `$event` parameter.
         *
         * @since 1.0.0
         *
         * @param string $event   The name of the action to be executed.
         * @param mixed  ...$payload Optional. Additional arguments which are passed on to the
         *                           functions hooked to the action.
        */
        function do_action( string $event, mixed ...$payload ): void {
            JP::doAction( $event, ...$payload );
        }
    }

    if ( ! function_exists( 'remove_action' ) ) {
        /**
         * Removes a function from a specified action hook.
         *
         * This function removes a function attached to a specified action hook.
         *
         * @since 1.0.0
         *
         * @param string              $event        The action hook to which the function to be removed is hooked.
         * @param string|int|callable $idOrCallable The ID or exact callable to be removed.
         * @param int                 $priority     Optional. Signature compatibility. Default JP::PRIORITY_NORMAL.
        */
        function remove_action( string $event, string|int|callable $idOrCallable, int $priority = JP::PRIORITY_NORMAL ): void {
            JP::removeAction( $event, $idOrCallable, $priority );
        }
    }

    if ( ! function_exists( 'has_action' ) ) {
        /**
         * Checks if any action has been registered for a hook.
         *
         * @since 1.0.0
         *
         * @param string                    $event        The name of the action hook.
         * @param string|int|callable|false $idOrCallable Optional. The callback or ID to check for. Default false.
         * @return bool True if the action has been registered, false otherwise.
        */
        function has_action( string $event, string|int|callable|false $idOrCallable = false ): bool {
            return JP::hasAction( $event, $idOrCallable );
        }
    }

    if ( ! function_exists( 'remove_all_actions' ) ) {
        /**
         * Removes all of the callback functions from an action hook.
         *
         * @since 1.0.0
         *
         * @param string|null $event Optional. The action to remove callbacks from. If null, removes all.
        */
        function remove_all_actions( ?string $event = null ): void {
            JP::removeAllActions( $event );
        }
    }

    // =====================================================================
    // 🎬 EXTENDED ACTION API (JackPoint Specifics)
    // =====================================================================

    if ( ! function_exists( 'once_action' ) ) {
        /**
         * Hooks a function to a specific action, but ensures it only runs once.
         *
         * @since 1.0.0
         *
         * @param string          $event    The name of the action.
         * @param callable        $listener The callback to be run.
         * @param int             $priority Optional. Execution priority. Default JP::PRIORITY_NORMAL.
         * @param string|int|null $id       Optional. Specific ID for the listener.
         * @return string|int The registered listener ID.
        */
        function once_action( string $event, callable $listener, int $priority = JP::PRIORITY_NORMAL, string|int|null $id = null ): string|int {
            return JP::onceAction( $event, $listener, $priority, $id );
        }
    }

    if ( ! function_exists( 'once_action_before' ) ) {
        /** Hooks a run-once function before normal band. */
        function once_action_before( string $event, callable $listener, string|int|null $id = null ): string|int {
            return JP::onceActionBefore( $event, $listener, $id );
        }
    }

    if ( ! function_exists( 'once_action_after' ) ) {
        /** Hooks a run-once function after normal band. */
        function once_action_after( string $event, callable $listener, string|int|null $id = null ): string|int {
            return JP::onceActionAfter( $event, $listener, $id );
        }
    }

    if ( ! function_exists( 'remove_action_by_id' ) ) {
        /** Removes a listener by its explicit ID. */
        function remove_action_by_id( string $event, string|int $id ): void {
            JP::removeActionById( $event, $id );
        }
    }

    // =====================================================================
    // 🧬 FILTERS API
    // =====================================================================

    if ( ! function_exists( 'add_filter' ) ) {
        /**
         * Hooks a function or method to a specific filter action.
         *
         * Filters are the hooks that the core launches to modify text or data
         * before it is saved or rendered on the screen.
         *
         * @since 1.0.0
         *
         * @param string          $event       The name of the filter to hook the $transformer to.
         * @param callable        $transformer The callback to be run when the filter is applied.
         * @param int             $priority    Optional. Used to specify the order in which the functions
         *                                     associated with a particular filter are executed. 
         *                                     Default JP::PRIORITY_NORMAL.
         * @param string|int|null $id          Optional. Specific ID for the transformer.
         * @return string|int The registered transformer ID.
        */
        function add_filter( string $event, callable $transformer, int $priority = JP::PRIORITY_NORMAL, string|int|null $id = null ): string|int {
            return JP::addFilter( $event, $transformer, $priority, $id );
        }
    }

    if ( ! function_exists( 'apply_filters' ) ) {
        /**
         * Calls the callback functions that have been added to a filter hook.
         *
         * This function runs `$value` through the full pipeline for `$event`.
         * Each transformer receives the running value and MUST return the next one.
         *
         * @since 1.0.0
         *
         * @param string $event   The name of the filter hook.
         * @param mixed  $value   The value to filter.
         * @param mixed  ...$context Optional. Additional context variables passed to the functions hooked.
         * @return mixed The filtered value after all hooked functions are applied to it.
        */
        function apply_filters( string $event, mixed $value, mixed ...$context ): mixed {
            return JP::applyFilters( $event, $value, ...$context );
        }
    }

    if ( ! function_exists( 'remove_filter' ) ) {
        /**
         * Removes a function from a specified filter hook.
         *
         * @since 1.0.0
         *
         * @param string              $event        The filter hook to which the function to be removed is hooked.
         * @param string|int|callable $idOrCallable The ID or exact callable to be removed.
        */
        function remove_filter( string $event, string|int|callable $idOrCallable ): void {
            JP::removeFilter( $event, $idOrCallable );
        }
    }

    if ( ! function_exists( 'remove_all_filters' ) ) {
        /**
         * Removes all of the callback functions from a filter hook.
         *
         * @since 1.0.0
         *
         * @param string|null $event Optional. The filter to remove callbacks from. If null, removes all.
        */
        function remove_all_filters( ?string $event = null ): void {
            JP::clearFilters( $event );
        }
    }
}