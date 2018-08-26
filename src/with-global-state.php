<?php

namespace LinusCash\Functions;

/**
 * provides a means of setting the global state that a function will have access to upon invocation
 *
 * @param Callable $fn - function for which to set the global state
 * @param Array $state - global state for the function
 *
 * @return Callable wrapped function that when invoked will have a global state of that provided by $state
 */
function withGlobalState(Callable $fn, Array $state): Callable {
    return function (...$args) use ($fn, $state) {
        $original = $GLOBALS;
        $GLOBALS = $state;

        try {
            return $fn(...$args);
        } finally {
            $GLOBALS = $original;
        }
    };
}
