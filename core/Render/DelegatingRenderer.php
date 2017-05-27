<?php


namespace LastCall\Mannequin\Core\Render;


use LastCall\Mannequin\Core\Exception\UnsupportedPatternException;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Variable\Set;
use LastCall\Mannequin\Core\Variable\VariableSet;

class DelegatingRenderer implements RendererInterface {

  private $renderers = [];

  public function __construct(array $renderers = []) {
    foreach($renderers as $renderer) {
      if(!$renderer instanceof RendererInterface) {
        throw new \InvalidArgumentException('Renderer must implement RendererInterface.');
      }
    }
    $this->renderers = $renderers;
  }

  public function supports(PatternInterface $pattern): bool {
    return (bool) $this->findRendererFor($pattern, FALSE);
  }

  private function findRendererFor(PatternInterface $pattern, $require = TRUE) {
    foreach($this->renderers as $renderer) {
      if($renderer->supports($pattern)) {
        return $renderer;
      }
    }
    if($require) {
      throw new UnsupportedPatternException(sprintf('Unable to find a renderer for %s', get_class($pattern)));
    }
  }

  public function render(PatternInterface $pattern, Set $set): RenderedInterface {
    return $this->findRendererFor($pattern)->render($pattern, $set);
  }

  public function renderSource(PatternInterface $pattern): string {
    return $this->findRendererFor($pattern)->renderSource($pattern);
  }
}