<?php


namespace LastCall\Mannequin\Twig\Tests\Discovery;

use LastCall\Mannequin\Core\Metadata\MetadataFactoryInterface;
use LastCall\Mannequin\Twig\Discovery\TwigFileDiscovery;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Finder\Finder;

class TwigFileDiscoveryTest extends TestCase {

  const FIXTURES_DIR = __DIR__.'/../Resources';

  public function getTestCases() {
    $p1 = new TwigPattern('dHdpZzovL3R3aWctbm8tbWV0YWRhdGEudHdpZw==', ['twig://twig-no-metadata.twig'], new \Twig_Source('', 'twig-no-metadata.twig', 'twig-no-metadata.twig'));
    return [
      [$p1],
    ];
  }

  /**
   * @dataProvider getTestCases
   */
  public function testDiscover(TwigPattern $expected) {
    $loader = new \Twig_Loader_Filesystem(self::FIXTURES_DIR);
    $metadata = $this->prophesize(MetadataFactoryInterface::class);
    $metadata->hasMetadata(Argument::type(TwigPattern::class))->willReturn(FALSE);

    $finder = new Finder();
    $finder->in([self::FIXTURES_DIR]);
    $finder->name($expected->getSource()->getPath());

    $discoverer = new TwigFileDiscovery($loader, $finder, $metadata->reveal());
    $patterns = $discoverer->discover();
    $pattern = $patterns->get($expected->getId());
    $this->assertEquals($expected->getId(), $pattern->getId());
    $this->assertEquals($expected->getAliases(), $pattern->getAliases());
    $this->assertEquals($expected->getDescription(), $pattern->getDescription());
    $this->assertEquals($expected->getName(), $pattern->getName());
    $this->assertEquals($expected->getTags(), $pattern->getTags());
    $this->assertEquals($expected->getVariables(), $pattern->getVariables());

  }
}