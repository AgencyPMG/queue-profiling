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

use Blackfire\Client as BfClient;
use Blackfire\Probe as BfProbe;
use Blackfire\Profile\Configuration as BfConfig;
use PMG\Queue\MessageHandler;
use PMG\Queue\ProfilingConsumer;
use PMG\Queue\SimpleMessage;
use PMG\Queue\Handler\Blackfire\ConfigurationFactory;

/**
 * Not an idea test, since it mocks third party code, but as with all third party
 * libraries that make requests there's not much choice.
 */
class BlackfireProfilingHandlerTest extends \PHPUnit_Framework_TestCase
{
    private $message, $config, $probe, $client, $configs, $wrapped, $handler;

    public function testHandlerStartsABlackfireProbeAndEndsIt()
    {
        $this->client->expects($this->once())
            ->method('createProbe')
            ->with($this->config)
            ->willReturn($this->probe);
        $this->client->expects($this->once())
            ->method('endProbe')
            ->with($this->identicalTo($this->probe));

        $result = $this->handler->handle($this->message, [
            ProfilingConsumer::FLAG => true,
        ]);

        $this->assertTrue($result);
    }

    protected function setUp()
    {
        $this->message = new SimpleMessage('test');
        $this->config = new BfConfig();
        $this->probe = $this->mockWithoutConstructor(BfProbe::class);
        $this->client = $this->mockWithoutConstructor(BfClient::class);
        $this->configs = $this->createMock(ConfigurationFactory::class);
        $this->wrapped = $this->createMock(MessageHandler::class);
        $this->handler = new BlackfireProfilingHandler(
            $this->wrapped,
            $this->client,
            $this->configs
        );

        $this->configs->expects($this->once())
            ->method('configurationFor')
            ->with($this->message)
            ->willReturn($this->config);
        $this->wrapped->expects($this->once())
            ->method('handle')
            ->with($this->message)
            ->willReturn(true);
    }

    private function mockWithoutConstructor($cls)
    {
        return $this->getMockBuilder($cls)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
