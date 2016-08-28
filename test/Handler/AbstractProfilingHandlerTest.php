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

namespace PMG\Queue\Handler;

use PMG\Queue\MessageHandler;
use PMG\Queue\ProfilingConsumer;
use PMG\Queue\SimpleMessage;

class AbstractProfilingHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $wrapped, $handler;

    public function testHandlingWithoutProfilingEnabledProxiesToWrappedHandler()
    {
        $this->handler->expects($this->never())
            ->method('startProfiling');
        $this->handler->expects($this->never())
            ->method('stopProfiling');

        $result = $this->handler->handle($this->message, []);

        $this->assertTrue($result);
    }

    public function testHandlingWithProfilingEnabledStartsAndStopsProfiling()
    {
        $options = [ProfilingConsumer::FLAG => true];
        $probe = new \stdClass;
        $this->handler->expects($this->once())
            ->method('startProfiling')
            ->with($this->message, $options)
            ->willReturn($probe);
        $this->handler->expects($this->once())
            ->method('stopProfiling')
            ->with($this->identicalTo($probe), $this->message, $options);

        $result = $this->handler->handle($this->message, $options);

        $this->assertTrue($result);
    }

    protected function setUp()
    {
        $this->message = new SimpleMessage('test');
        $this->wrapped = $this->createMock(MessageHandler::class);
        $this->handler = $this->getMockForAbstractClass(AbstractProfilingHandler::class, [
            $this->wrapped,
        ]);

        $this->wrapped->expects($this->once())
            ->method('handle')
            ->with($this->message)
            ->willReturn(true);
    }
}
