<?php

namespace Yo\Tests\Coammnd;

use Yo\Command\All;

class AllTest extends \Yo\Tests\TestCase
{
    protected $command;
    protected $yo;

    protected function setUp()
    {
        $this->command  = $this->createCommand(new All);
        $this->yo       = $this->getYo(['all']);

        parent::setUp();
    }

    public function testNotUrlIsGiven()
    {
        $this->yo
            ->expects($this->once())
            ->method('all')
        ;

        $this->yoHelper
            ->expects($this->once())
            ->method('getYo')
            ->will($this->returnValue($this->yo))
        ;

        $commandTester = $this->getCommandTester($this->command, $this->yoHelper);
        $commandTester->execute(array(
            'command' => $this->command->getName(),
        ));

        $this->assertRegExp('/Yo `ALL`/', $commandTester->getDisplay());
    }

    public function testGivenUrlIsIgnoredBecauseItIsAnInvalidUrl()
    {
        $this->yo
            ->expects($this->once())
            ->method('all')
        ;

        $this->yoHelper
            ->expects($this->once())
            ->method('getYo')
            ->will($this->returnValue($this->yo))
        ;

        $commandTester = $this->getCommandTester($this->command, $this->yoHelper);
        $commandTester->execute(array(
            'command' => $this->command->getName(),
            'url'     => 'invalid_url',
        ));

        $this->assertRegExp('/Yo `ALL`/', $commandTester->getDisplay());
    }

    public function testGivenUrlIsValid()
    {
        $this->yo
            ->expects($this->once())
            ->method('all')
        ;

        $this->yoHelper
            ->expects($this->once())
            ->method('getYo')
            ->will($this->returnValue($this->yo))
        ;

        $commandTester = $this->getCommandTester($this->command, $this->yoHelper);
        $commandTester->execute(array(
            'command' => $this->command->getName(),
            'url'     => 'http://sbin.dk/',
        ));

        $this->assertRegExp('/Yo `ALL` ~ `http:\/\/sbin.dk\/`/', $commandTester->getDisplay());
    }

    public function testCannotYoAll()
    {
        $this->yo
            ->expects($this->once())
            ->method('all')
            ->will($this->throwException(new \RuntimeException('Boo!')))
        ;

        $this->yoHelper
            ->expects($this->once())
            ->method('getYo')
            ->will($this->returnValue($this->yo))
        ;

        $commandTester = $this->getCommandTester($this->command, $this->yoHelper);
        $commandTester->execute(array(
            'command' => $this->command->getName(),
        ));

        $this->assertRegExp('/Boo!/', $commandTester->getDisplay());
    }
}
