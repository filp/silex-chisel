<?php
/**
 * silex-chisel
 * 
 * @author  Filipe Dobreira <https://github.com/filp>
 * @license MIT
 * 
 * Bootstrap file for the Silex application.
 */
require __DIR__ . "/../vendor/autoload.php";

use Silex\Application;

return call_user_func(function() {
    $app = new Silex\Application;

    $app["debug"] = (bool) getenv("SILEX_DEBUG");

    // Register application paths:
    $app["path.app"]      = __DIR__;
    $app["path.cache"]    = __DIR__ . "/cache";
    $app["path.commands"] = __DIR__ . "/commands";
    $app["path.config"]   = __DIR__ . "/config";
    $app["path.views"]    = __DIR__ . "/views";

    // Register base service providers:
    $app->register(new Silex\Provider\TwigServiceProvider,
        array(
            "twig.path" => $app["path.views"],
            "twig.options" => array(
                "cache" => $app["path.cache"] . "/twig"
            )
        )
    );

    return $app;
});