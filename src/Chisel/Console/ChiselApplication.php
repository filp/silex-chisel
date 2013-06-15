<?php
/**
 * silex-chisel
 * 
 * @author  Filipe Dobreira <https://github.com/filp>
 * @license MIT
 */

namespace Chisel\Console;
use Chisel\Console\Command as ChiselCommand;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Silex\Application as SilexApplication;
use ReflectionClass;

/**
 * Application wrapper for the Chisel CLI tool.
 */
class ChiselApplication extends Application
{
    /** @var Silex\Application */
    private $app;

    /**
     * {@inheritDoc}
     */
    public function __construct(SilexApplication $app, $name = 'chisel', $version = ':)')
    {
        parent::__construct($name, $version);
        $this->app = $app;
    }

    /**
     * Decorate's the base class's add method to inject the application
     * object for Chisel\Console\Command instances.
     * 
     * {@inheritDoc}
     */
    public function add(BaseCommand $command)
    {
        parent::add($command);

        if(is_subclass_of($command, "Chisel\\Console\\Command") && $command->isEnabled()) {
            $command->setApp($this->app);
        }

        return $command;
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

            // The command class must subclass Symfony or Chisel's Command classes,
            // must be concrete, and must not expect any required arguments in its
            // constructor method.
            // 
            // This logic is pretty much the same as what's done for automagic
            // command instantiation in sf's FrameworkBundle.
            if( ($r->isSubclassOf("Symfony\\Component\\Console\\Command\\Command") || $r->isSubclassOf("Chisel\\Console\\Command")) &&
                !$r->isAbstract() && !$r->getConstructor()->getNumberOfRequiredParameters()) {
                $this->add($r->newInstance());
            }
        }
    }
}