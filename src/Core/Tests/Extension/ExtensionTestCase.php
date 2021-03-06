<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Extension;

use LastCall\Mannequin\Core\Asset\AssetManager;
use LastCall\Mannequin\Core\Config\ConfigInterface;
use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Mannequin;
use LastCall\Mannequin\Core\Variable\VariableResolver;
use LastCall\Mannequin\Core\YamlMetadataParser;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Asset\PackageInterface;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class ExtensionTestCase extends TestCase
{
    public function testSubscribe()
    {
        $extension = $this->getExtension();
        $extension->register($this->getMannequin());
        $extension->subscribe(
            $this->getDispatcherProphecy()->reveal()
        );
    }

    public function testGetEngines()
    {
        $extension = $this->getExtension();
        $extension->register($this->getMannequin());
        $engines = $extension->getEngines();
        $this->assertContainsOnlyInstancesOf(
            EngineInterface::class,
            $engines
        );

        return $engines;
    }

    public function testHasDiscoverers()
    {
        $extension = $this->getExtension();
        $extension->register($this->getMannequin());
        $discoverers = $extension->getDiscoverers();
        $this->assertContainsOnlyInstancesOf(
            DiscoveryInterface::class,
            $discoverers
        );

        return $discoverers;
    }

    /**
     * @return \Symfony\Component\ExpressionLanguage\ExpressionFunction[]|null
     */
    public function testGetFunctions()
    {
        $extension = $this->getExtension();
        if ($extension instanceof ExpressionFunctionProviderInterface) {
            $extension->register($this->getMannequin());
            $functions = $extension->getFunctions();
            $this->assertContainsOnlyInstancesOf(
                ExpressionFunction::class,
                $functions
            );

            return $functions;
        } else {
            // Pass this test. No assertions needed.
            $this->addToAssertionCount(1);
        }
    }

    abstract public function getExtension(): ExtensionInterface;

    public function getConfig(): ConfigInterface
    {
        $config = $this->prophesize(ConfigInterface::class);
        $config->getGlobalCss()->willReturn([]);
        $config->getGlobalJs()->willReturn([]);

        return $config->reveal();
    }

    public function getMannequin(ConfigInterface $config = null): Mannequin
    {
        $mannequin = $this->prophesize(Mannequin::class);
        $mannequin->getMetadataParser()->willReturn(new YamlMetadataParser());
        $mannequin->getCache()->willReturn(new NullAdapter());
        $mannequin->getConfig()->willReturn($config ?? $this->getConfig());
        $mannequin->getVariableResolver()->willReturn($this->prophesize(VariableResolver::class));
        $mannequin->getAssetManager()->willReturn($this->prophesize(AssetManager::class));
        $mannequin->getAssetPackage()->willReturn($this->prophesize(PackageInterface::class));
        $generator = $this->prophesize(UrlGeneratorInterface::class);
        $mannequin->getUrlGenerator()->willReturn($generator->reveal());
        $mannequin->getCacheDir()->willReturn(sys_get_temp_dir().'/mannequin-test');

        return $mannequin->reveal();
    }

    protected function getDispatcherProphecy(): ObjectProphecy
    {
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
        $dispatcher->addSubscriber(
            Argument::type(EventSubscriberInterface::class)
        )->shouldNotBeCalled();

        return $dispatcher;
    }
}
