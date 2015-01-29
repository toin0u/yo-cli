<?php

namespace Yo\Tests\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Yo\Command\User;

class UserTest extends \Yo\Tests\TestCase
{
    protected $application;
    protected $command;
    protected $yo;
    protected $yoHelper;

    protected function setUp()
    {
        $this->application = new Application;
        $this->application->add(new User);

        $this->command = $this->application->find('user');

        $this->yo = $this
            ->getMockBuilder('Yo\Yo')
            ->disableOriginalConstructor()
            ->setMethods(['user'])
            ->getMock()
        ;

        $this->yoHelper = $this
            ->getMockBuilder('Yo\Helper\Yo')
            ->setMethods(['getYo'])
            ->getMock()
        ;
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough arguments.
     */
    public function testUsernameArgumentIsMandatory()
    {
        $this->command->getHelperSet()->set($this->yoHelper, 'yo');

        (new CommandTester($this->command))->execute([
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

        $this->command->getHelperSet()->set($this->yoHelper, 'yo');

        $commandTester = new CommandTester($this->command);
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

        $this->command->getHelperSet()->set($this->yoHelper, 'yo');

        $commandTester = new CommandTester($this->command);
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

        $this->command->getHelperSet()->set($this->yoHelper, 'yo');

        $commandTester = new CommandTester($this->command);
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

        $this->command->getHelperSet()->set($this->yoHelper, 'yo');

        $commandTester = new CommandTester($this->command);
        $commandTester->execute(array(
            'command'    => $this->command->getName(),
            'username'   => 'toin0u',
            'parameters' => ['123.4', '567.8'],
        ));

        $this->assertRegExp('/Yo `TOIN0U` ~ `123.400000,567.800000`/', $commandTester->getDisplay());
    }
}
