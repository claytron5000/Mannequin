<?php


namespace LastCall\Mannequin\Core\Tests\Render;


use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Render\RenderedInterface;
use LastCall\Mannequin\Core\Render\RendererInterface;
use LastCall\Mannequin\Core\Variable\Definition;
use LastCall\Mannequin\Core\Variable\Set;
use PHPUnit\Framework\TestCase;

abstract class RendererTestCase extends TestCase {

  abstract public function getRenderer(): RendererInterface;
  abstract public function getSupportedPattern(): PatternInterface;

  protected function getUnsupportedPattern(): PatternInterface {
    return $this->createPattern('unsupported')->reveal();
  }

  protected function createPattern($id) {
    $pattern = $this->prophesize(PatternInterface::class);
    $pattern->getId()->willReturn($id);
    $pattern->getVariableSets()->willReturn(['default' => new Set('Default')]);
    $pattern->getVariableDefinition()->willReturn(new Definition());
    return $pattern;
  }

  public function testSupports() {
    $this->assertTrue($this->getRenderer()->supports($this->getSupportedPattern()));
    $this->assertFalse($this->getRenderer()->supports($this->getUnsupportedPattern()));
  }

  public function testRender() {
    $pattern = $this->getSupportedPattern();
    $rendered = $this->getRenderer()->render($pattern, $pattern->getVariableSets()['default']);
    $this->assertInstanceOf(RenderedInterface::class, $rendered);
    $this->assertEquals($pattern, $rendered->getPattern());
    return $rendered;
  }

  /**
   * @expectedException \LastCall\Mannequin\Core\Exception\UnsupportedPatternException
   */
  public function testRenderUnsupported() {
    $pattern = $this->getUnsupportedPattern();
    $this->getRenderer()->render($pattern, $pattern->getVariableSets()['default']);
  }

  public function testRenderSource() {
    $pattern = $this->getSupportedPattern();
    $source = $this->getRenderer()->renderSource($pattern, $pattern->getVariableSets()['default']);
    $this->assertTrue(is_string($source));
    return $source;
  }

  /**
   * @expectedException \LastCall\Mannequin\Core\Exception\UnsupportedPatternException
   */
  public function testRenderSourceUnsupported() {
    $pattern = $this->getUnsupportedPattern();
    $this->getRenderer()->renderSource($pattern, $pattern->getVariableSets()['default']);
  }

}