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

namespace PMG\Queue;

use Psr\Log\LoggerInterface;

/**
 * A consumer that enables or disable profiling based on signals sent to a running
 * process or methods called on the instance.
 *
 * @since 1.0
 */
final class ProfilingConsumer extends DefaultConsumer
{
    const FLAG = 'profile';

    /**
     * Whether or not to pass the profiling option to the message handler
     *
     * @var boolean
     */
    private $profilingEnabled = false;

    /**
     * The unix signal to use to toggle profiling.
     *
     * @var int
     */
    private $toggleSignal;

    public function __construct(
        Driver $driver,
        MessageHandler $handler,
        RetrySpec $retries=null,
        LoggerInterface $logger=null,
        $toggleSignal=null
    ) {
        parent::__construct($driver, $handler, $retries, $logger);
        $this->toggleSignal = $toggleSignal;
    }

    public function enableProfiling()
    {
        $this->profilingEnabled = true;
    }

    public function disableProfiling()
    {
        $this->profilingEnabled = false;
    }

    public function isProfilingEnabled()
    {
        return $this->profilingEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function run($queueName)
    {
        $this->handleSignals();

        return parent::run($queueName);
    }

    /**
     * {@inheritdoc}
     */
    public function once($queueName)
    {
        $this->setHandlerOptions([
            self::FLAG => $this->isProfilingEnabled(),
        ]);

        return parent::once($queueName);
    }

    private function handleSignals()
    {
        if (!function_exists('pcntl_signal')) {
            $this->getLogger()->warning('The pcntl extension is not loaded, {cls} cannot enable/disable profiling while running', [
                'cls' => __CLASS__,
            ]);
            return;
        }

        pcntl_signal(
            null === $this->toggleSignal ? SIGUSR1 : $this->toggleSignal,
            function () {
                $this->profilingEnabled = !$this->profilingEnabled;
            }
        );
    }
}
