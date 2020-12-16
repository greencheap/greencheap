<?php

namespace GreenCheap\Console\NodeVisitor;

use PhpParser\Lexer;
use PhpParser\Node;
use Symfony\Component\Templating\EngineInterface;

abstract class NodeVisitor
{
    /**
     * @var string
     */
    public $file;

    /**
     * @var array
     */
    public $results = [];

    public function __construct(
        public EngineInterface $engine
    ){}

    /**
     * @return EngineInterface
     */
    public function getEngine() :EngineInterface
    {
        return $this->engine;
    }

    /**
     * Starts traversing an array of files.
     *
     * @param  array $files
     * @return array
     */
    abstract public function traverse(array $files);

    /**
     * @param  string $name
     * @return string
     */
    protected function loadTemplate($name) :string
    {
        return $this->file = $name;
    }
}
