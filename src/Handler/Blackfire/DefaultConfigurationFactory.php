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

namespace PMG\Queue\Handler\Blackfire;

use Blackfire\Profile\Configuration;
use PMG\Queue\Message;

/**
 * The default blackfire configuration factory. It uses a prototype configuration
 * object and sets the profile name to "{messageName}@{hostName}".
 *
 * @since 0.1
 */
final class DefaultConfigurationFactory implements ConfigurationFactory
{
    private $prototype;

    public function __construct(Configuration $prototype=null)
    {
        $this->prototype = $prototype ?: new Configuration();
    }

    /**
     * {@inheritdoc}
     */
    public function configurationFor(Message $message, array $options)
    {
        $config = clone $this->prototype;

        $config->setTitle(sprintf('%s@%s', $message->getName(), (string) gethostname()));

        return $config;
    }
}
