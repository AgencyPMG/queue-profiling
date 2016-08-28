# Queue Profiling

Provides a `ProfilingConsumer` and set of handler decorators that can profile
messages once or while the queue is running via `SIGUSR1` (or other) posix
signals.

## Usage

### Pick an appropriate handler for your setup.

- `PMG\Queue\Handler\BlackfireProfilingHandler` - Profile with [blackfire.io](https://blackfire.io/)
- `PMG\Queue\Handler\TidewaysProfilingHandler` - Profile with the
  [tideways.io PHP extension](https://github.com/tideways/php-profiler-extension)

### Instantiate a Consumer Like Normal

```php

use PMG\Queue\ProfilingConsumer

$consumer = new ProfilingConsumer($driver, $handler);
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
