<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Dotenv\Dotenv;
use Wei\Wei;

/**
 * @mixin \ClassMapPropMixin
 * @mixin \ConfigPropMixin
 * @experimental
 */
class ConsoleApp extends BaseService
{
    /**
     * @var string[]
     */
    protected array $commandDirs = [
        'src',
        'plugins/*/src',
    ];

    public function __invoke(): int
    {
        if (is_file('.env')) {
            $dotenv = new Dotenv();
            $dotenv->loadEnv('.env');
        }

        $this->config->preloadGlobal();

        $app = new Application('Wei', Wei::VERSION);

        $classes = $this->classMap->generate($this->commandDirs, '/Command/*.php', 'Command');
        foreach ($classes as $class) {
            if (is_subclass_of($class, Command::class)) {
                $app->add(new $class());
            }
        }

        return $app->run();
    }
}
