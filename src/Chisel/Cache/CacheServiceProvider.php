<?php
/**
 * silex-chisel
 * 
 * @author  Filipe Dobreira <https://github.com/filp>
 * @license MIT
 */

namespace Chisel\Cache;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Stash\Pool;
use Stash\Driver\Ephemeral;
use Stash\Driver\Apc;
use Stash\Driver\FileSystem;
use Stash\Driver\Composite;

/**
 * Wraps Stash under a service, as a cache provider.
 */
class CacheServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        $app["cache"] = $app["chisel.cache"] = $app->share(function() use($app) {
            $drivers = array();

            // If we're in testing or development mode, use the Ephemeral
            // cache driver so we don't actually put anything into cache.
            if(in_array($app["env"], array("test", "dev"))) {
                $drivers[] = new \Stash\Driver\Ephemeral;

            // If we're in production, stack multiple drivers, and namespace
            // them to the specific environment to avoid clashes:
            } else {

                $drivers[] = new \Stash\Driver\Apc(
                    array("namespace" => md5($app["env"]))
                );

                $drivers[] = new \Stash\Driver\FileSystem(
                    array("path" => "{$app['path.cache']}/stash_{$app['env']}")
                );
            }

            return new Pool(new \Stash\Driver\Composite($drivers));
        });
    }

    /**
     * @{inheritDoc}
     */
    public function boot(Application $app) {}
}