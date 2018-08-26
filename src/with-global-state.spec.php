<?php

namespace LinusCash\Functions;

use RuntimeException;

define('MESSAGE_KEY', 'message_key');
define('TEST_MESSAGE', 'test message');

describe('with-global-state', function () {
    describe('setting the global state for a function', function () {
        it('correctly resets the global state after invocation', function () {
            $GLOBALS[MESSAGE_KEY] = TEST_MESSAGE;

            withGlobalState(
                function () {
                    $GLOBALS[MESSAGE_KEY] = 'a sample change';
                },
                []
            )();

            expect($GLOBALS[MESSAGE_KEY])->toBe(TEST_MESSAGE);
        });

        it('works for the empty state', function () {
            withGlobalState(
                function () {
                    expect($GLOBALS)->toBe([]);
                },
                []
            )();
        });

        it('works for a non-empty state', function () {
            withGlobalState(
                function () {
                    expect($GLOBALS)->toBe([MESSAGE_KEY => TEST_MESSAGE]);
                },
                [MESSAGE_KEY => TEST_MESSAGE]
            )();
        });
    });

    describe('using the returned function', function () {
        it('returns a wrapped function that has a predefined state', function () {
            $fn = function ($key1, $key2) {
                return (($GLOBALS['key1'] === $key1) && ($GLOBALS['key2'] === $key2));
            };

            $wrappedFn = withGlobalState($fn, ['key1' => 'super', 'key2' => 'secret']);

            expect($wrappedFn('super', 'secret'))->toBe(true);
            expect($wrappedFn('brute', 'force'))->toBe(false);
        });

        it('does not swallow exceptions', function () {
            $fn = function () {
                throw new RuntimeException('oops');
            };

            expect(withGlobalState($fn, []))->toThrow(new RuntimeException('oops'));
        });

        it('resets the global state correctly upon exception', function () {
            $GLOBALS = [MESSAGE_KEY => TEST_MESSAGE];

            $fn = function () {
                $GLOBALS[MESSAGE_KEY] = 'changed';
                throw new RuntimeException('oops');
            };

            expect(withGlobalState($fn, []))->toThrow(new RuntimeException('oops'));
            expect($GLOBALS)->toBe([MESSAGE_KEY => TEST_MESSAGE]);
        });
    });
});
