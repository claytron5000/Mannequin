<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Driver;

use LastCall\Mannequin\Twig\Twig\Lexer;
use LastCall\Mannequin\Twig\Twig\MannequinExtension;

/**
 * Knows how to create a simple Twig_Environment with a known root and options.
 */
class SimpleTwigDriver implements TwigDriverInterface
{
    private $twigRoot;
    private $twigOptions;
    private $twig;

    public function __construct(string $twigRoot, array $twigOptions = [])
    {
        $this->twigRoot = $twigRoot;
        $this->twigOptions = $twigOptions;
    }

    public function getTwig(): \Twig_Environment
    {
        if (!$this->twig) {
            $this->twig = $this->createTwig();
        }

        return $this->twig;
    }

    protected function createTwig(): \Twig_Environment
    {
        $loader = new \Twig_Loader_Filesystem($this->twigRoot);

        $twig = new \Twig_Environment($loader, $this->twigOptions);
        $twig->addExtension(new MannequinExtension());
        $twig->setLexer(new Lexer($twig));

        return $twig;
    }

    public function getNamespaces(): array
    {
        $namespaces = [];
        $loader = $this->getTwig()->getLoader();
        if ($loader instanceof \Twig_Loader_Filesystem) {
            foreach ($loader->getNamespaces() as $namespace) {
                $namespaces[$namespace] = $loader->getPaths($namespace);
            }
        }

        return $namespaces;
    }

    public function getTwigRoot(): string
    {
        return $this->twigRoot;
    }
}
