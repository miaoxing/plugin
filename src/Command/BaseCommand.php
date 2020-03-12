<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Services\Service\ServiceTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wei\Wei;

abstract class BaseCommand extends Command
{
    use ServiceTrait;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var Wei
     */
    protected $wei;

    /**
     * {@inheritDoc}
     */
    public function __construct(string $name = null)
    {
        $this->wei = wei();

        parent::__construct($name);
    }

    /**
     * Executes the current command
     *
     * @return int
     */
    abstract protected function handle();

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        return $this->handle();
    }

    /**
     * Writes a error message
     *
     * @param string $message
     * @param int $code
     * @return int
     */
    protected function err(string $message, int $code = -1)
    {
        $this->output->writeln('<error>' . $message . '</error>');
        return $code;
    }

    /**
     * Writes a success message
     *
     * @param string $message
     * @param int $code
     * @return int
     */
    protected function suc(string $message, int $code = 0)
    {
        $this->output->writeln('<info>' . $message . '</info>');
        return $code;
    }
}
