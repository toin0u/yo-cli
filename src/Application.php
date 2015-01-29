<?php

/*
 * This file is part of the Yo CLI package.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yo;

use Symfony\Component\Console\Input\InputOption;
use Yo\Command\User;
use Yo\Helper\Config;
use Yo\Helper\Yo as YoHelper;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Application extends \Symfony\Component\Console\Application
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        $commands[] = new User;

        return $commands;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();

        $definition->addOption(new InputOption(
            'config',
            'c',
            InputOption::VALUE_REQUIRED,
            'Specify a custom location for the configuration file'
        ));

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultHelperSet()
    {
        $helperSet = parent::getDefaultHelperSet();

        $helperSet->set(new Config);
        $helperSet->set(new YoHelper);

        return $helperSet;
    }
}
