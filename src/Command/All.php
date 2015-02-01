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
use Yo\Bag\Link;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class All extends \Symfony\Component\Console\Command\Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('all')
            ->setDescription('Yo your subscribers with or without a link')
            ->addArgument(
                'url',
                InputArgument::OPTIONAL,
                'The link to yo'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $yo  = $this->getHelper('yo')->getYo();
        $url = $input->getArgument('url');
        $bag = null;

        $output->write('Yo <comment>`ALL`</comment>');

        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $bag = new Link($url);
            $output->write(sprintf(' ~ <info>`%s`</info>', $bag->getValue()));
        }

        try {
            $yo->all($bag);
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }
}
