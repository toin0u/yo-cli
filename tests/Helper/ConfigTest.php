<?php

namespace Yo\Tests\Helper;

use Yo\Helper\Config;

class ConfigTest extends \Yo\Tests\TestCase
{
    protected $config;
    protected $input;
    protected $tmpFile;

    public function setUp()
    {
        $this->config = new Config;

        $this->input = $this
            ->getMockBuilder('Symfony\Component\Console\Input\InputInterface')
            ->getMock()
        ;

        $this->tmpFile = sprintf('%s%s%s', rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR, 'tmpFile');
    }

    public function testReturnCorrectName()
    {
        $this->assertSame('config', $this->config->getName());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Custom configuration file `not_existing_file` does not exist.
     */
    public function testNoConfigurationFound()
    {
        $this->input
            ->expects($this->once())
            ->method('getParameterOption')
            ->with(['--config', '-c'])
            ->will($this->returnValue('not_existing_file'))
        ;

        $this->config->setInput($this->input);

        $this->config->getConfiguration();
    }

    public function testConfigurationFileLoadedSuccessfully()
    {
        $content = <<<EOF
api_key:
    18330bd1-57a5-4h11-701f-216a42c3f2e1
EOF;
        file_put_contents($this->tmpFile, $content);

        $this->input
            ->expects($this->once())
            ->method('getParameterOption')
            ->with(['--config', '-c'])
            ->will($this->returnValue($this->tmpFile))
        ;

        $this->config->setInput($this->input);

        $configuration = $this->config->getConfiguration();
        $this->assertTrue(is_array($configuration));
        $this->assertCount(1, $configuration);
        $this->assertArrayHasKey('api_key', $configuration);
        $this->assertSame('18330bd1-57a5-4h11-701f-216a42c3f2e1', $configuration['api_key']);

        unlink($this->tmpFile);
    }

    /**
     * @expectedException Symfony\Component\Yaml\Exception\ParseException
     * @expectedExceptionMessage Unable to parse in
     */
    public function testConfigurationFileIsNotInYaml()
    {
        $content = <<<EOF
api_key:
18330bd1-57a5-4h11-701f-216a42c3f2e1
EOF;
        file_put_contents($this->tmpFile, $content);

        $this->input
            ->expects($this->once())
            ->method('getParameterOption')
            ->with(['--config', '-c'])
            ->will($this->returnValue($this->tmpFile))
        ;

        $this->config->setInput($this->input);

        $this->config->getConfiguration();

        unlink($this->tmpFile);
    }

    public function testConfigurationLoadedOnlyOnce()
    {
        $content = <<<EOF
api_key:
    18330bd1-57a5-4h11-701f-216a42c3f2e1
EOF;
        file_put_contents($this->tmpFile, $content);

        // http://php.net/manual/fr/closure.bind.php which is better than \ReflectionClass
        $getConfigIsLoadedProperty = function (Config $config) {
            return $config->isLoaded;
        };
        $getConfigIsLoadedProperty = \Closure::bind($getConfigIsLoadedProperty, null, $this->config);

        $this->input
            ->expects($this->once())
            ->method('getParameterOption')
            ->with(['--config', '-c'])
            ->will($this->returnValue($this->tmpFile))
        ;

        $this->config->setInput($this->input);

        $this->assertFalse($getConfigIsLoadedProperty($this->config));

        $this->config->getConfiguration();

        $this->assertTrue($getConfigIsLoadedProperty($this->config));

        $this->config->getConfiguration();

        $this->assertTrue($getConfigIsLoadedProperty($this->config));

        unlink($this->tmpFile);
    }
}
