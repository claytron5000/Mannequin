<?php


namespace LastCall\Mannequin\Cli\Ui;


use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Render\RendererInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\EngineInterface;

class UiRenderer {

  public function __construct(RendererInterface $renderer, EngineInterface $engine) {
    $this->renderer = $renderer;
    $this->templating = $engine;
  }

  public function renderManifest(PatternCollection $collection, UrlGeneratorInterface $generator) {
    $manifest = [];
    foreach($collection as $pattern) {
      $id = $pattern->getId();
      $manifest['patterns'][] = [
        'id' => $id,
        'rendered' => $generator->generate('pattern_render', ['pattern' => $id]),
        'source' => $generator->generate('pattern_source', ['pattern' => $id]),
        'name' => $pattern->getName(),
        'description' => $pattern->getDescription(),
        'tags' => $pattern->getTags(),
      ];
    }
    return $manifest;
  }

  public function renderPattern(PatternInterface $pattern) {
    $rendered = $this->renderer->render($pattern);
    return $this->templating->render('rendered.html.php', [
      'title' => $pattern->getName(),
      'markup' => $rendered->getMarkup(),
      'styles' => $rendered->getStyles(),
      'scripts' => $rendered->getScripts(),
    ]);
  }

  public function renderSource(PatternInterface $pattern) {
    $source = $this->renderer->renderSource($pattern);
    return $this->templating->render('source.html.php', [
      'title' => sprintf('Source for %s', $pattern->getName()),
      'source' => $source,
    ]);
  }
}