<?php

namespace LastCall\Mannequin\Core;

use LastCall\Mannequin\Core\Command\RenderCommand;
use LastCall\Mannequin\Core\Command\ServerCommand;
use LastCall\Mannequin\Core\Controller\RenderController;
use LastCall\Mannequin\Core\Controller\UiController;
use LastCall\Mannequin\Core\MimeType\ExtensionMimeTypeGuesser;
use LastCall\Mannequin\Core\Ui\UiRenderer;
use LastCall\Mannequin\Core\Ui\UiWriter;
use LastCall\Mannequin\Core\Console\Application as ConsoleApplication;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;


class Application extends \Silex\Application {

  const APP_NAME = 'Mannequin';
  const APP_VERSION = '0.0.0';

  public function __construct(array $values = []) {
    parent::__construct($values);
    $this['console'] = function() {
      $app = new ConsoleApplication(self::APP_NAME, self::APP_VERSION);
      $app->addCommands([
        new RenderCommand('render', $this['ui.writer'], $this['config']),
        new ServerCommand('server', $this['config_file'], $this['autoload_path'], $this['debug']),
      ]);
      return $app;
    };
    $this['config'] = function() {
      $filename = $this['config_file'];
      if(!file_exists($filename)) {
        throw new \RuntimeException(sprintf('Expected config in %s, but the file does not exist.', $filename));
      }
      $config = require_once $filename;
      if(!$config instanceof  ConfigInterface) {
        throw new \RuntimeException('Config was not returned or not an instance of Config.');
      }
      return $config;
    };
    $this['template_engine'] = function() {
      $loader = new FilesystemLoader([__DIR__.'/Resources/ui/%name%']);
      return new PhpEngine(new TemplateNameParser(), $loader);
    };
    $this['ui.writer'] = function() {
      $this->boot();
      $this->flush();
      return new UiWriter($this['ui.renderer'], $this['url_generator']);
    };
    $this['ui.renderer'] = function() {
      return new UiRenderer($this['config']->getRenderer(), $this['template_engine'], $this['config']->getLabeller());
    };

    $this->register(new ServiceControllerServiceProvider());
    $this['controller.ui'] = function() {
      return new UiController($this['url_generator'], $this['ui.renderer']);
    };
    $this['controller.render'] = function() {
      $generator = $this['url_generator'];
      $collection = $this['config']->getCollection();
      return new RenderController($collection, $this['ui.renderer'], $generator);
    };

    $this->get('/', 'controller.ui:indexAction');
    $this->get('/manifest.json', 'controller.render:manifestAction')->bind('manifest');
    $this->get('/_render/{pattern}', 'controller.render:renderAction')->bind('pattern_render');
    $this->get('/_source/{pattern}', 'controller.render:sourceAction')->bind('pattern_source');
    $this->get('/{name}', 'controller.ui:staticAction')->assert('name','.+');
  }

  public function boot() {
    // Guess file extensions for CSS and JS files.
    MimeTypeGuesser::getInstance()->register(new ExtensionMimeTypeGuesser());
    return parent::boot();
  }

  public function getConsole() {
    return $this['console'];
  }
}