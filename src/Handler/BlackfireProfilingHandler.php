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

use Blackfire\Client as BlackfireClient;
use PMG\Queue\Message;
use PMG\Queue\MessageHandler;
use PMG\Queue\Handler\Blackfire\ConfigurationFactory;
use PMG\Queue\Handler\Blackfire\DefaultConfigurationFactory;

/**
 * Profiles via http://blackfire.io/
 *
 * @since 0.1
 */
final class BlackfireProfilingHandler extends AbstractProfilingHandler
{
    /**
     * @var BlackfireClient
     */
    private $client;

    /**
     * @var ConfigurationFactory
     */
    private $configs;

    public function __construct(MessageHandler $wrapped, BlackfireClient $client, ConfigurationFactory $configs)
    {
        parent::__construct($wrapped);
        $this->client = $client;
        $this->configs = $configs;
    }

    public static function createDefault(MessageHandler $wrapped)
    {
        return new self($wrapped, new BlackfireClient(), new DefaultConfigurationFactory());
    }

    /**
     * {@inheritdoc}
     */
    protected function startProfiling(Message $message, array $options)
    {
        $config = $this->configs->configurationFor($message, $options);

        return $this->client->createProbe($config);
    }

    /**
     * {@inheritdoc}
     */
    public function stopProfiling($probe, Message $message, array $options)
    {
        $this->client->endProbe($probe);
    }
}
