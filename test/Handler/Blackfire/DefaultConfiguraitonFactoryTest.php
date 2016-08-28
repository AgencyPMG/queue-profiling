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

namespace PMG\Queue\Handler\Blackfire;

use Blackfire\Profile\Configuration;
use PMG\Queue\SimpleMessage;

class DefaultConfiguraitonFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactoryReturnsConfigurationWithProfileTitleSetToMessageNameAtHostName()
    {
        $factory = new DefaultConfigurationFactory();

        $config = $factory->configurationFor(new SimpleMessage('TestFactory'), []);

        $this->assertInstanceOf(Configuration::class, $config);
        $this->assertEquals(sprintf('TestFactory@%s', (string) gethostname()), $config->getTitle());
    }
}
