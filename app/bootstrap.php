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

    // Environment specifics:
    $app["debug"] = getenv("SILEX_DEBUG") !== false ? (bool) getenv("SILEX_DEBUG") : true;
    $app["env"]   = getenv("SILEX_ENVIRONMENT") ?: "dev";

    // Register application paths:
    $app["path.app"]      = __DIR__;
    $app["path.cache"]    = __DIR__ . "/cache";
    $app["path.commands"] = __DIR__ . "/commands";
    $app["path.config"]   = __DIR__ . "/config";
    $app["path.views"]    = __DIR__ . "/views";

    // Register chisel service providers:
    $app->register(new Chisel\Configuration\ConfigurationServiceProvider,
        array(
            // We only check that a configuration file exists
            // if we're in debug mode:
            "chisel.config.check_exists"   => $app["debug"],
            "chisel.config.ignore_missing" => true,

            "chisel.config.files" => array(
                $app["path.config"] . "/app.example.yml",
                $app["path.config"] . "/app_{$app['env']}.yml"
            )
        )
    );

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