<?php

namespace Miaoxing\Plugin\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Kernel;

class HttpKernel extends Kernel
{
    /**
     * The bootstrap classes for the application.
     *
     * @var array
     */
    protected $bootstrappers = [
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
        // 由 \Wei\Error 在入口处理
        //\Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        \Illuminate\Foundation\Bootstrap\BootProviders::class,
    ];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
