<?php

/*
 * This file is part of pmg/queue-blackfire
 *
 * Copyright (c) PMG <https://www.pmg.com>
 *
 * For full copyright information see the LICENSE file distributed
 * with this source code.
 *
 * @license     http://opensource.org/licenses/Apache-2.0 Apache-2.0
 */

namespace PMG\Queue;

use PMG\Queue\Driver\MemoryDriver;
use PMG\Queue\Exception\SimpleMustStop;

class ProfilingConsumerTest extends \PHPUnit_Framework_TestCase
{
    private $driver, $handler, $consumer;

    public function testConsumerPassesProfilingFlagAsFalseWhenProfilingIsDisabled()
    {
        $msg = new SimpleMessage('test');
        $this->driver->enqueue('test', $msg);
        $this->handler->expects($this->once())
            ->method('handle')
            ->with($msg, [ProfilingConsumer::FLAG => false])
            ->willReturn(true);

        $this->consumer->disableProfiling();
        $result = $this->consumer->once('test');

        $this->assertTrue($result);
    }

    public function testConsumerPassesProfilingFlagAsTrueWhenProfilingIsEnabled()
    {
        $msg = new SimpleMessage('test');
        $this->driver->enqueue('test', $msg);
        $this->handler->expects($this->once())
            ->method('handle')
            ->with($msg, [ProfilingConsumer::FLAG => true])
            ->willReturn(true);

        $this->consumer->enableProfiling();
        $result = $this->consumer->once('test');

        $this->assertTrue($result);
    }

    /**
     * @requires extension pcntl
     * @requires extension posix
     */
    public function testRunningConsumerEnablesProfilingWhenSignalIsReceivedAndProfilingIsDisabled()
    {
        $msg = new SimpleMessage('test');
        $msg2 = new SimpleMessage('test2');
        $this->driver->enqueue('test', $msg);
        $this->driver->enqueue('test', $msg2);
        $this->handler->expects($this->at(0))
            ->method('handle')
            ->with($msg, [ProfilingConsumer::FLAG => false])
            ->willReturnCallback(function () {
                // send ourselves the toggle signal
                posix_kill(posix_getpid(), SIGUSR1);
                return true;
            });
        $this->handler->expects($this->at(1))
            ->method('handle')
            ->with($msg2, [ProfilingConsumer::FLAG => true])
            ->willThrowException(new SimpleMustStop());

        $this->consumer->disableProfiling();
        $exitCode = $this->consumer->run('test');

        $this->assertSame($exitCode, 0);
    }

    /**
     * @requires extension pcntl
     * @requires extension posix
     */
    public function testRunningConsumerDisablesProfilingWhenSignalIsReceivedAndProfilingIsEnabled()
    {
        $msg = new SimpleMessage('test');
        $msg2 = new SimpleMessage('test2');
        $this->driver->enqueue('test', $msg);
        $this->driver->enqueue('test', $msg2);
        $this->handler->expects($this->at(0))
            ->method('handle')
            ->with($msg, [ProfilingConsumer::FLAG => true])
            ->willReturnCallback(function () {
                // send ourselves the toggle signal
                posix_kill(posix_getpid(), SIGUSR1);
                return true;
            });
        $this->handler->expects($this->at(1))
            ->method('handle')
            ->with($msg2, [ProfilingConsumer::FLAG => false])
            ->willThrowException(new SimpleMustStop());

        $this->consumer->enableProfiling();
        $exitCode = $this->consumer->run('test');

        $this->assertSame($exitCode, 0);
    }

    protected function setUp()
    {
        $this->driver = new MemoryDriver();
        $this->handler = $this->createMock(MessageHandler::class);
        $this->consumer = new ProfilingConsumer($this->driver, $this->handler);
    }
}
