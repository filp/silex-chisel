<?php
/**
 * silex-chisel
 * 
 * @author  Filipe Dobreira <https://github.com/filp>
 * @license MIT
 */

namespace Chisel;
use Silex\WebTestCase as BaseWebTestCase;

/**
 * Wrapper around Silex's WebTestCase that already knows
 * how to setup the application.
 */
abstract class WebTestCase extends BaseWebTestCase
{
    /**
     * Loads the application bootstrap and sets it up
     * for functional testing.
     * 
     * Override this method if you change the bootstrap's
     * location.
     * 
     * @return Silex\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . "/../../app/bootstrap.php";
        $app["debug"] = true;
        $app["env"]   = "test";
        $app["exception_handler"]->disable();

        return $app;
    }
}