<?php

namespace Yo\Tests\Command;

use Yo\Command\User;

class UserTest extends \Yo\Tests\TestCase
{
    protected $command;
    protected $yo;

    protected function setUp()
    {
        $this->command = $this->createCommand(new User);
        $this->yo      = $this->getYo(['user']);

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
            ->method('user')
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

    public function testGivenParametersAreIgnored()
    {
        $this->yo
            ->expects($this->once())
            ->method('user')
            ->will($this->returnValue(true))
        ;

        $this->yoHelper
            ->expects($this->once())
            ->method('getYo')
            ->will($this->returnValue($this->yo))
        ;

        $commandTester = $this->getCommandTester($this->command, [$this->yoHelper]);
        $commandTester->execute(array(
            'command'    => $this->command->getName(),
            'username'   => 'toin0u',
            'parameters' => ['foo', 'bar', 'baz'],
        ));

        $this->assertRegExp('/Yo `TOIN0U`/', $commandTester->getDisplay());
    }

    public function testLinkParameter()
    {
        $this->yo
            ->expects($this->once())
            ->method('user')
            ->will($this->returnValue(true))
        ;

        $this->yoHelper
            ->expects($this->once())
            ->method('getYo')
            ->will($this->returnValue($this->yo))
        ;

        $commandTester = $this->getCommandTester($this->command, [$this->yoHelper]);
        $commandTester->execute(array(
            'command'    => $this->command->getName(),
            'username'   => 'toin0u',
            'parameters' => ['http://sbin.dk/'],
        ));

        $this->assertRegExp('/Yo `TOIN0U` ~ `http:\/\/sbin.dk\/`/', $commandTester->getDisplay());
    }

    public function testLocationParameter()
    {
        $this->yo
            ->expects($this->once())
            ->method('user')
            ->will($this->returnValue(true))
        ;

        $this->yoHelper
            ->expects($this->once())
            ->method('getYo')
            ->will($this->returnValue($this->yo))
        ;

        $commandTester = $this->getCommandTester($this->command, [$this->yoHelper]);
        $commandTester->execute(array(
            'command'    => $this->command->getName(),
            'username'   => 'toin0u',
            'parameters' => ['123.4', '567.8'],
        ));

        $this->assertRegExp('/Yo `TOIN0U` ~ `123.400000,567.800000`/', $commandTester->getDisplay());
    }
}
