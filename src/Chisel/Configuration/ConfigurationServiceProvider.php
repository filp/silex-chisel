<?php
/**
 * silex-chisel
 * 
 * @author  Filipe Dobreira <https://github.com/filp>
 * @license MIT
 */

namespace Chisel\Configuration;
use Symfony\Component\Yaml\Yaml;
use Silex\Application;
use Silex\ServiceProviderInterface;
use RuntimeException;

/**
 * Adds YAML configuration file support to Silex
 */
class ConfigurationServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        // The chisel.config service, given an array of configuration
        // files, loads them in order and reads them as YAML, performing
        // merges as needed, and caching the results if possible.
        // @todo Caching
        $app["chisel.config"] = $app->share(function() use($app) {
            $files  = (array) $app["chisel.config.files"];
            $config = array();
            foreach($files as $file) {

                if($app["chisel.config.check_exists"] && !file_exists($file)) {
                    if($app["chisel.config.ignore_missing"]) {
                        continue;
                    } else {
                        throw new RuntimeException(
                            "Configuration file '{$file}' not found"
                        );
                    }
                }

                $config = array_replace_recursive(
                    $config,
                    Yaml::parse(file_get_contents($file))
                );
            }

            return $config;
        });
    }

    /**
     * @{inheritDoc}
     */
    public function boot(Application $app) {}
}