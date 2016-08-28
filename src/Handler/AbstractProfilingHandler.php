<?php

/*
 * This file is part of pmg/queue-profiling
 *
 * Copyright (c) PMG <https://www.pmg.com>
 *
 * For full copyright information see the LICENSE file distributed
 * with this source code.
 *
 * @license     http://opensource.org/licenses/Apache-2.0 Apache-2.0
 */

namespace PMG\Queue\Handler;

use PMG\Queue\Message;
use PMG\Queue\MessageHandler;
use PMG\Queue\ProfilingConsumer;

/**
 * Base class for profiling handlers.
 *
 * @since 0.1
 */
abstract class AbstractProfilingHandler implements MessageHandler
{
    /**
     * The wrapped handler instance.
     *
     * @var MessageHandler
     */
    private $wrapped;

    public function __construct(MessageHandler $wrapped)
    {
        $this->wrapped = $wrapped;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Message $message, array $options=[])
    {
        $isProfiling = $this->profilingEnabled($options);

        if ($isProfiling) {
            // most libraries call it a probe, stick with that.
            $probe = $this->startProfiling($message, $options);
        }

        try {
            return $this->wrapped->handle($message, $options);
        } finally {
            if ($isProfiling) {
                $this->stopProfiling($probe, $message, $options);
            }
        }
    }

    /**
     * See whether the profiling flag has been set in the configuration.
     *
     * @param $options The handler options passed to `handle`.
     * @return bool
     */
    protected function profilingEnabled(array $config)
    {
        return !empty($config[ProfilingConsumer::FLAG]);
    }

    /**
     * Begin the profiling for the given message & configuration.
     *
     * @param $message The message currently being handled
     * @param $options The handler options passed to `handle`.
     * @return mixed A "probe" for the configuration. Something that will be
     *         passed to `stopProfiling`.
     */
    abstract protected function startProfiling(Message $message, array $options);

    /**
     * Called when profiling is enabled for the message to stop the profile
     *
     * @param mixed $probe Whatever was returned from `startProfiling`
     * @param $message The message being handled
     * @param $options The handler options passed to `handle`.
     * @return void
     */
    abstract protected function stopProfiling($probe, Message $message, array $options);
}
