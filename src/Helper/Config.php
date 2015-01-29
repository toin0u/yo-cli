<?php

/*
 * This file is part of the Yo CLI package.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yo\Helper;

use Symfony\Component\Yaml\Yaml;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Config extends \Symfony\Component\Console\Helper\InputAwareHelper
{
    /**
     * @var array
     */
    protected $paths = [];

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var boolean
     */
    protected $isLoaded = false;

    /**
     * @param string[]
     */
    public function __construct(array $paths = [])
    {
        $this->paths = $paths ?: ['.yo.yml', 'yo.yml', sprintf('%s/.yo.yml', getenv('HOME'))];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'config';
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        if (!$this->isLoaded) {
            $this->config   = $this->loadConfiguration();
            $this->isLoaded = true;
        }

        return $this->config;
    }

    /**
     * @throws \RuntimeException
     *
     * @return array
     */
    private function loadConfiguration()
    {
        foreach ($this->getPaths() as $path) {
            if ($config = $this->loadFile($path)) {
                return $config;
            }
        }

        throw new \RuntimeException('No configuration files could be found.');
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    private function getPaths()
    {
        if ($customPath = $this->input->getParameterOption(['--config', '-c'])) {
            if (!file_exists($customPath)) {
                throw new \InvalidArgumentException(sprintf(
                    'Custom configuration file `%s` does not exist.',
                    $customPath
                ));
            }

            return [$customPath];
        }

        return $this->paths;
    }

    /**
     * @param string $path
     *
     * @return array|boolean
     */
    private function loadFile($path)
    {
        if (file_exists($path) && $config = Yaml::parse($path)) {
            return $config;
        }

        return false;
    }
}
