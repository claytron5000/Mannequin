<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Extension;

use LastCall\Mannequin\Core\Application;
use LastCall\Mannequin\Core\ConfigInterface;
use Pimple\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AbstractExtension extends Container implements ExtensionInterface
{
    protected $config;

    /**
     * {@inheritdoc}
     */
    public function getEngines(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscoverers(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe(EventDispatcherInterface $dispatcher)
    {
    }

    public function registerToApp(Application $mannequin)
    {
        $this->mannequin = $mannequin;
    }

    /**
     * Get the configuration instance.
     *
     * Config will be set at the time the container is instantiated, but will
     * not be available in the constructor.
     *
     * @return ConfigInterface|null
     */
    protected function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;
    }
}
