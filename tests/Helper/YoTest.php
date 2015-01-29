<?php

namespace Yo\Tests\Helper;

use Yo\Helper\Yo;

class YoTest extends \Yo\Tests\TestCase
{
    protected $yo;
    protected $helperSet;
    protected $config;

    public function setUp()
    {
        $this->yo = new Yo;

        $this->helperSet = $this
            ->getMockBuilder('Symfony\Component\Console\Helper\HelperSet')
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock()
        ;

        $this->config = $this
            ->getMockBuilder('Yo\Helper\Config')
            ->setMethods(['getConfiguration'])
            ->getMock()
        ;
    }

    public function testReturnCorrectName()
    {
        $this->assertSame('yo', $this->yo->getName());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The API token is not defined. Please check the configuration file.
     */
    public function testWeGetInvalidConfiguration()
    {
        $this->config
            ->expects($this->once())
            ->method('getConfiguration')
            ->will($this->returnValue(['foo' => 'bar']))
        ;

        $this->helperSet
            ->expects($this->once())
            ->method('get')
            ->with('config')
            ->will($this->returnValue($this->config))
        ;

        $this->yo->setHelperSet($this->helperSet);

        $this->yo->getYo();
    }

    public function testYoInstanceIsReturned()
    {
        $this->config
            ->expects($this->once())
            ->method('getConfiguration')
            ->will($this->returnValue(['api_token' => '18330bd1-57a5-4h11-701f-216a42c3f2e1']))
        ;

        $this->helperSet
            ->expects($this->once())
            ->method('get')
            ->with('config')
            ->will($this->returnValue($this->config))
        ;

        $this->yo->setHelperSet($this->helperSet);

        $this->assertInstanceOf('Yo\Yo', $this->yo->getYo());
    }
}
