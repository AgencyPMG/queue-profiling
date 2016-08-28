# Queue Profiling

Provides a `ProfilingConsumer` and set of handler decorators that can profile
messages once or while the queue is running via `SIGUSR1` (or other) posix
signals.

## Usage

### Pick an appropriate handler for your setup.

- `PMG\Queue\Handler\BlackfireProfilingHandler` - Profile with [blackfire.io](https://blackfire.io/)

All profiling handlers are [decorators](https://en.wikipedia.org/wiki/Decorator_pattern),
so you'll create a real message handler and decorate it with the profiling
handler.

```php
use PMG\Queue\Message;
use PMG\Queue\ProfilingConsumer;
use PMG\Queue\Handler\CallableHandler;
use PMG\Queue\Handler\BlackfireProfilingHandler;

$realHandler = new CallableHandler(function (Message $message) {
    // do stuff
});

$profilingHandler = BlackfireProfilingHandler::createDefault($realHandler);
```

If you plan on forking new processes to handle messages with
`PcntlForkingHandler`, decorate the profiling handler with it.

```php
use PMG\Queue\Handler\PcntlForkingHandler;

$forkingHandler = new PcntlForkingHandler($profilingHandler);
```

### Instantiate a Consumer Like Normal

```php

use PMG\Queue\ProfilingConsumer

$consumer = new ProfilingConsumer($driver, $profilingHandler);
```

### Enable or Disable Profiling or Let the Signal Handlers Take Care of It


```php
// let the signal handler do their thing(s)
$consumer->run('SomeQueue');

// or enable/disable profiling manually
$consumer->enableProfiling();
$consumer->once('SomeQueue');

$consumer->disableProfiling();
$consumer->once('SomeQueue');
```
