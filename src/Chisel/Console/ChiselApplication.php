<?php
/**
 * silex-chisel
 * 
 * @author  Filipe Dobreira <https://github.com/filp>
 * @license MIT
 */

namespace Chisel\Console;
use Symfony\Component\Console\Application;
use ReflectionClass;

/**
 * Application wrapper for the Chisel CLI tool.
 */
class ChiselApplication extends Application
{
    /**
     * {@inheritDoc}
     */
    public function __construct($name = 'chisel', $version = '')
    {
        parent::__construct($name, $version);
    }

    /**
     * Given a directory path, searches within it for all /Command$/
     * files, and attempts to load them as commands for this application.
     * 
     * @param string $sourcePath
     * @param string $globPattern
     */
    public function addFromDirectory($sourcePath, $pattern = "*Command.php")
    {
        $matches = glob(rtrim($sourcePath, "/") . "/" . ltrim($pattern, "/"));
        foreach($matches as $match) {
            $baseName = basename($match, ".php");
            $r        = new ReflectionClass($baseName);

            if( $r->isSubclassOf("Symfony\\Component\\Console\\Command\\Command") &&
                !$r->isAbstract() && !$r->getConstructor()->getNumberOfRequiredParameters()) {
                $this->add($r->newInstance());
            }
        }
    }
}