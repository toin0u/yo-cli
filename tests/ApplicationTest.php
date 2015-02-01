<?php

namespace Yo\Tests;

use Yo\Application;

class ApplicationTest extends TestCase
{
    protected $application;

    public function setUp()
    {
        $this->application = new Application;
    }

    public function testCommandsAreRegistred()
    {
        $this->assertInstanceOf('Symfony\Component\Console\Command\Command', $this->application->get('all'));
        $this->assertInstanceOf('Symfony\Component\Console\Command\Command', $this->application->get('check'));
        $this->assertInstanceOf('Symfony\Component\Console\Command\Command', $this->application->get('create'));
        $this->assertInstanceOf('Symfony\Component\Console\Command\Command', $this->application->get('total'));
        $this->assertInstanceOf('Symfony\Component\Console\Command\Command', $this->application->get('user'));
    }

    public function testConfigOptionIsRegistered()
    {
        $this->assertSame('config', $this->application->getDefinition()->getOption('config')->getName());
    }

    public function testCustomerHelperAreRegistered()
    {
        $helperSet = $this->application->getHelperSet();

        $this->assertTrue($helperSet->has('config'));
        $this->assertTrue($helperSet->has('yo'));
    }
}
