<?php

namespace Yo\Tests\Command;

use Yo\Command\Total;

class TotalTest extends \Yo\Tests\TestCase
{
    protected $command;
    protected $yo;

    protected function setUp()
    {
        $this->command  = $this->createCommand(new Total);
        $this->yo       = $this->getYo(['total']);

        parent::setUp();
    }

    public function testDisplayTotalSubscribers()
    {
        $this->yo
            ->expects($this->once())
            ->method('total')
            ->will($this->returnValue(123))
        ;

        $this->yoHelper
            ->expects($this->once())
            ->method('getYo')
            ->will($this->returnValue($this->yo))
        ;

        $commandTester = $this->getCommandTester($this->command, [$this->yoHelper]);
        $commandTester->execute(array(
            'command' => $this->command->getName(),
        ));

        $this->assertRegExp('/`123` subscribers\./', $commandTester->getDisplay());
    }

    public function testCannotFetchTotalSubscribers()
    {
        $this->yo
            ->expects($this->once())
            ->method('total')
            ->will($this->throwException(new \RuntimeException('Boo!')))
        ;

        $this->yoHelper
            ->expects($this->once())
            ->method('getYo')
            ->will($this->returnValue($this->yo))
        ;

        $commandTester = $this->getCommandTester($this->command, [$this->yoHelper]);
        $commandTester->execute(array(
            'command' => $this->command->getName(),
        ));

        $this->assertRegExp('/Boo!/', $commandTester->getDisplay());
    }
}
