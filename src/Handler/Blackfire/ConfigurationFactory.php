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
 * Creates Blackfire probe configuration objects for the given message and set
 * of options.
 *
 * @since 0.1
 */
interface ConfigurationFactory
{
    /**
     * Create a new blackfire probe configuration for the given message an options
     *
     * @param $message The message to profile
     * @param $options The handler options passed to MessageHandler::handle
     * @return Configuration
     */
    public function configurationFor(Message $message, array $options);
}
