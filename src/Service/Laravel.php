<?php

namespace Miaoxing\Plugin\Service;

use Illuminate\Config\Repository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\MySqlConnection;
use Illuminate\Queue\QueueManager;
use Miaoxing\Plugin\BaseService;
use Illuminate\Support\Facades\Facade;
use Miaoxing\Plugin\Laravel\ConsoleKernel;
use Miaoxing\Services\Service\StaticTrait;
use Wei\Db;

/**
 * @property Db db
 */
class Laravel extends BaseService
{
    use StaticTrait;

    protected $app;

    public function bootstrap()
    {
        if (php_sapi_name() === 'cli') {
            $this->bootstrapConsole();
        } else {
            $this->bootstrapHttp();
        }

        $this->shareConfig();
    }

    /**
     * @api
     */
    protected function getApp()
    {
        if (!$this->app) {
            $this->app = $this->createApp();
        }
        return $this->app;
    }

    protected function createApp()
    {
        /*
        |--------------------------------------------------------------------------
        | Create The Application
        |--------------------------------------------------------------------------
        |
        | The first thing we will do is create a new Laravel application instance
        | which serves as the "glue" for all the components of Laravel, and is
        | the IoC container for the system binding all of the various parts.
        |
        */

        $app = new \Illuminate\Foundation\Application(
            $_ENV['APP_BASE_PATH'] ?? realpath('.')
        );

        /*
        |--------------------------------------------------------------------------
        | Bind Important Interfaces
        |--------------------------------------------------------------------------
        |
        | Next, we need to bind some important interfaces into the container so
        | we will be able to resolve them when needed. The kernels serve the
        | incoming requests to this application from both the web and CLI.
        |
        */

        $app->singleton(
            \Illuminate\Contracts\Http\Kernel::class,
            \App\Http\Kernel::class
        );

        $app->singleton(
            \Illuminate\Contracts\Console\Kernel::class,
            ConsoleKernel::class
        );

        $app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \App\Exceptions\Handler::class
        );

        // TODO 1. autoload 失效
        $app->afterResolving(QueueManager::class, function () {
            wei()->queue->setConfig();
        });

        /*
        |--------------------------------------------------------------------------
        | Return The Application
        |--------------------------------------------------------------------------
        |
        | This script returns the application instance. The instance is given to
        | the calling script so we can separate the building of the instances
        | from the actual running of the application and sending responses.
        |
        */

        return $app;
    }

    protected function bootstrapHttp()
    {
        $app = $this->getApp();

        /** @var \Illuminate\Foundation\Http\Kernel $kernel */
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

        $request = new \Illuminate\Http\Request();
        $app->instance('request', $request);
        Facade::clearResolvedInstance('request');

        $kernel->bootstrap();
    }

    protected function bootstrapConsole()
    {
        $app = $this->getApp();

        /** @var ConsoleKernel $kernel */
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
    }

    protected function shareConfig()
    {
        $app = $this->getApp();

        // Database
        /** @var Repository $config */
        $config = $app['config'];
        $config->set('database', [
            'default' => 'mysql',
            'connections' => [
                'mysql' => [

                ],
            ],
            'redis' => [
                'client' => 'phpredis',
                'default' => [
                    'host' => 'redis',
                    'password' => 'password',
                    'port' => env('REDIS_PORT', 6379),
                    'database' => env('REDIS_DB', 0),
                ],
            ],
        ]);
        $app->extend(DatabaseManager::class, function (DatabaseManager $manager) {
            $manager->extend('mysql', function () {
                return new MySqlConnection($this->db->getPdo(), $this->db->getDbname(), $this->db->getTablePrefix());
            });
            return $manager;
        });
    }
}
