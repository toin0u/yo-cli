<?php

/*
 * This file is part of the Yo CLI package.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yo\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Yo\Bag;
use Yo\Bag\Link;
use Yo\Bag\Location;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class User extends \Symfony\Component\Console\Command\Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('user')
            ->setDescription('Yo an user with a link, a location or nothing')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'The user to yo'
            )
            ->addArgument(
                'parameters',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'The URL or the location (longitude latitude) to yo'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $yo         = $this->getHelper('yo')->getYo();
        $username   = $input->getArgument('username');
        $parameters = $input->getArgument('parameters');
        $bag        = null;

        if ($parameters && filter_var($parameters[0], FILTER_VALIDATE_URL)) {
            $bag = new Link($parameters[0]);
        } elseif (2 === count($parameters)) {
            $bag = new Location($parameters[0], $parameters[1]);
        }

        $output->write(sprintf('Yo <comment>`%s`</comment>', strtoupper($username)));

        if ($bag instanceof Bag) {
            $output->write(sprintf(' ~ <info>`%s`</info>', $bag->getValue()));
        }

        try {
            $yo->user($username, $bag);
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }
}
