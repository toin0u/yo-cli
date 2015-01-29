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

use Ivory\HttpAdapter\CurlHttpAdapter;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class Yo extends \Symfony\Component\Console\Helper\InputAwareHelper
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'yo';
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @return Yo
     */
    public function getYo()
    {
        $config = $this->getHelperSet()->get('config')->getConfiguration();

        if (!isset($config['api_token'])) {
            throw new \InvalidArgumentException('The API token is not defined. Please check the configuration file.');
        }

        return new \Yo\Yo(new CurlHttpAdapter, $config['api_token']);
    }
}
