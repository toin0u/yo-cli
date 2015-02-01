<?php

namespace Yo\Tests;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Tester\CommandTester;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $yoHelper;

    protected function setUp()
    {
        $this->yoHelper = $this
            ->getMockBuilder('Yo\Helper\Yo')
            ->setMethods(['getYo'])
            ->getMock()
        ;

        parent::setUp();
    }

    /**
     * @param  Command $command
     * @return Command
     */
    protected function createCommand(Command $command)
    {
        $application = new Application;
        $application->add($command);

        return $application->get($command->getName());
    }

    /**
     * @param  array $methodNames
     * @return Yo
     */
    protected function getYo(array $methodNames)
    {
        return $this
            ->getMockBuilder('Yo\Yo')
            ->disableOriginalConstructor()
            ->setMethods($methodNames)
            ->getMock()
        ;
    }

    /**
     * @param  Command              $command
     * @param  HelperInterface|null $helper
     * @return CommandTester
     */
    protected function getCommandTester(Command $command, HelperInterface $helper = null)
    {
        if (null !== $helper) {
            $command->getHelperSet()->set($helper, $helper->getName());
        }

        return new CommandTester($command);
    }
}
