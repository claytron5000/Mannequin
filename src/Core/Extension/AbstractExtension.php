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

use LastCall\Mannequin\Core\Mannequin;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AbstractExtension implements ExtensionInterface
{
    /**
     * @var Mannequin
     */
    protected $mannequin;

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

    public function register(Mannequin $mannequin)
    {
        $this->mannequin = $mannequin;
    }
}
