# with-global-state
withGlobalState is a simple utility that will wrap any function in order to make its global state requirements clear.

## motivation
Working on an application recently I found that large chunks of it shared global state. I wanted to focus my efforts untangling modules that shared state but didn't want to go all big bang on it and run down the rabbit hole. Thus this little function was born. I used it to get a grip on each modules global state while I refactored it out without interfering with the other modules.

## example
```php
function greetMe (String $moniker): String
{
    return "Hello {$moniker} my name is {$GLOBALS['name']}";
}

greetMe('stranger'); // ??

$linusGreetMe = withGlobalState('greetMe', ['name' => 'Linus']);
$linusGreetMe('stranger'); // "Hello stranger my name is Linus"
```
The above is a contrived example but it does demonstrate the functions purpose. We can see a poorly implemented function that depends on global state instantly become more readable, manageable and testable.

Essentially we can write tests against this legacy code. Once we have a handle on it, we can refactor it down to something more meanful and maintainable without treading on the toes of other modules.
