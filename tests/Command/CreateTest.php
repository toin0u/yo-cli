<?php

namespace Yo\Tests\Command;

use Yo\Command\Create;

class CreateTest extends \Yo\Tests\TestCase
{
    protected $command;
    protected $yo;
    protected $questionHelper;

    protected function setUp()
    {
        $this->command        = $this->createCommand(new Create);
        $this->yo             = $this->getYo(['exists', 'create']);
        $this->questionHelper = $this
            ->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')
            ->disableOriginalConstructor()
            ->setMethods(['ask'])
            ->getMock()
        ;

        parent::setUp();
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
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

    public function testCannotCreateIfUsernameExists()
    {
        $this->yo
            ->expects($this->once())
            ->method('exists')
            ->with('EXISTING_USERNAME')
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
            'username' => 'existing_username',
        ));

        $this->assertRegExp('/Aborted! `EXISTING_USERNAME` exists./', $commandTester->getDisplay());
    }

    public function testCannotCreateIfPasswordsDoNotMatch()
    {
        $this->yo
            ->expects($this->once())
            ->method('exists')
            ->with('UNKNOWN_USERNAME')
            ->will($this->returnValue(false))
        ;

        $this->yoHelper
            ->expects($this->once())
            ->method('getYo')
            ->will($this->returnValue($this->yo))
        ;

        $this->questionHelper->expects($this->at(0))->method('ask')->will($this->returnValue('password_intial'));
        $this->questionHelper->expects($this->at(1))->method('ask')->will($this->returnValue('password_not_mathching'));

        $commandTester = $this->getCommandTester($this->command, [$this->yoHelper, $this->questionHelper]);
        $commandTester->execute(array(
            'command'  => $this->command->getName(),
            'username' => 'unknown_username',
        ));

        $this->assertRegExp('/Aborted! Passwords do not match!/', $commandTester->getDisplay());
    }

    public function testCorrectParametersButYoServerThowsAnException()
    {
        $this->yo
            ->expects($this->at(0))
            ->method('exists')
            ->with('UNKNOWN_USERNAME')
            ->will($this->returnValue(false))
        ;

        $this->yoHelper
            ->expects($this->once())
            ->method('getYo')
            ->will($this->returnValue($this->yo))
        ;

        $this->questionHelper->expects($this->at(0))->method('ask')->will($this->returnValue('custom_password'));
        $this->questionHelper->expects($this->at(1))->method('ask')->will($this->returnValue('custom_password'));
        $this->questionHelper->expects($this->at(2))->method('ask')->will($this->returnValue('http://sbin.dk/'));
        $this->questionHelper->expects($this->at(3))->method('ask')->will($this->returnValue('contact@sbin.dk'));
        $this->questionHelper->expects($this->at(4))->method('ask')->will($this->returnValue('Foo Bar Baz'));
        $this->questionHelper->expects($this->at(5))->method('ask')->will($this->returnValue(true));

        $this->yo
            ->expects($this->at(1))
            ->method('create')
            ->with('UNKNOWN_USERNAME', 'custom_password', 'http://sbin.dk/', 'contact@sbin.dk', 'Foo Bar Baz', true)
            ->will($this->throwException(new \RuntimeException('Something wrong happened with Yo!')))
        ;

        $commandTester = $this->getCommandTester($this->command, [$this->yoHelper, $this->questionHelper]);
        $commandTester->execute(array(
            'command'  => $this->command->getName(),
            'username' => 'unknown_username',
        ));

        $this->assertRegExp('/Something wrong happened with Yo!/', $commandTester->getDisplay());
    }

    public function testUsernameSuccesfullyCreated()
    {
        $this->yo
            ->expects($this->at(0))
            ->method('exists')
            ->with('UNKNOWN_USERNAME')
            ->will($this->returnValue(false))
        ;

        $this->yoHelper
            ->expects($this->once())
            ->method('getYo')
            ->will($this->returnValue($this->yo))
        ;

        $this->questionHelper->expects($this->at(0))->method('ask')->will($this->returnValue('custom_password'));
        $this->questionHelper->expects($this->at(1))->method('ask')->will($this->returnValue('custom_password'));
        $this->questionHelper->expects($this->at(2))->method('ask')->will($this->returnValue('http://sbin.dk/'));
        $this->questionHelper->expects($this->at(3))->method('ask')->will($this->returnValue('contact@sbin.dk'));
        $this->questionHelper->expects($this->at(4))->method('ask')->will($this->returnValue('Foo Bar Baz'));
        $this->questionHelper->expects($this->at(5))->method('ask')->will($this->returnValue(true));

        $this->yo
            ->expects($this->at(1))
            ->method('create')
            ->with('UNKNOWN_USERNAME', 'custom_password', 'http://sbin.dk/', 'contact@sbin.dk', 'Foo Bar Baz', true)
        ;

        $commandTester = $this->getCommandTester($this->command, [$this->yoHelper, $this->questionHelper]);
        $commandTester->execute(array(
            'command'  => $this->command->getName(),
            'username' => 'unknown_username',
        ));

        $this->assertRegExp('/`UNKNOWN_USERNAME` created successfully\./', $commandTester->getDisplay());
    }
}
