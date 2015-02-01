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
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Check extends \Symfony\Component\Console\Command\Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('check')
            ->setDescription('Check if the username exists or not')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'The username to check'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $yo       = $this->getHelper('yo')->getYo();
        $username = $input->getArgument('username');

        try {
            $exists = $yo->exists($username);
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }

        $output->write(sprintf('<comment>`%s`</comment>', strtoupper($username)));
        $output->write(sprintf(' %s.', $exists ? 'exists' : 'does not exist'));
    }
}
