<?php
/**
 * silex-chisel
 * 
 * @author  Filipe Dobreira <https://github.com/filp>
 * @license MIT
 */

namespace Chisel\Console;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Silex\Application;

/**
 * Chisel/Silex-aware wrapper around symfony's Command class
 */
class Command extends BaseCommand
{
    /** @var Silex\Application */
    protected $app;

    /**
     * @param Silex\Application $app
     */
    public function setApp(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return Silex\Application
     */
    public function getApp()
    {
        return $this->app;
    }
}