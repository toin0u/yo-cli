<?php

namespace Yo\Tests\Command;

use Yo\Command\Check;

class CheckTest extends \Yo\Tests\TestCase
{
    protected $command;
    protected $yo;

    protected function setUp()
    {
        $this->command  = $this->createCommand(new Check);
        $this->yo       = $this->getYo(['exists']);

        parent::setUp();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough arguments.
     */
    public function testUsernameArgumentIsMandatory()
    {
        $this->getCommandTester($this->command, [$this->yoHelper])->execute([
            'command' => $this->command->getName(),
        ]);
    }

    public function testUsernameIsInvalid()
    {
        $this->yo
            ->expects($this->once())
            ->method('exists')
            ->will($this->throwException(new \RuntimeException('Boo!')))
        ;

        $this->yoHelper
            ->expects($this->once())
            ->method('getYo')
            ->will($this->returnValue($this->yo))
        ;

        $commandTester = $this->getCommandTester($this->command, [$this->yoHelper]);
        $commandTester->execute(array(
            'command'  => $this->command->getName(),
            'username' => 'invalid_username',
        ));

        $this->assertRegExp('/Boo!/', $commandTester->getDisplay());
    }

    public function testGivenUsernameExists()
    {
        $this->yo
            ->expects($this->once())
            ->method('exists')
            ->will($this->returnValue(true))
        ;

        $this->yoHelper
            ->expects($this->once())
            ->method('getYo')
            ->will($this->returnValue($this->yo))
        ;

        $commandTester = $this->getCommandTester($this->command, [$this->yoHelper]);
        $commandTester->execute(array(
            'command'  => $this->command->getName(),
            'username' => 'foobar',
        ));

        $this->assertRegExp('/`FOOBAR` exists\./', $commandTester->getDisplay());
    }

    public function testGivenUsernameDoesNotExist()
    {
        $this->yo
            ->expects($this->once())
            ->method('exists')
            ->will($this->returnValue(false))
        ;

        $this->yoHelper
            ->expects($this->once())
            ->method('getYo')
            ->will($this->returnValue($this->yo))
        ;

        $commandTester = $this->getCommandTester($this->command, [$this->yoHelper]);
        $commandTester->execute(array(
            'command'  => $this->command->getName(),
            'username' => 'bazqmu',
        ));

        $this->assertRegExp('/`BAZQMU` does not exist\./', $commandTester->getDisplay());
    }
}
