<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Services\Service\ServiceTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\ArrayInput;
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
     * {@inheritdoc}
     */
    public function __construct(string $name = null)
    {
        $this->wei = wei();

        // Convert class name to command name if not provided
        if (null === $name && null === static::getDefaultName()) {
            $str = $this->wei->str;
            $name = $str->snake($str->baseName($this), ':');
        }

        parent::__construct($name);
    }

    /**
     * Executes the current command
     *
     * @return int
     */
    abstract protected function handle();

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        return $this->handle();
    }

    /**
     * Returns all the given arguments merged with the default values.
     *
     * @return array
     */
    protected function getArguments()
    {
        return $this->input->getArguments();
    }

    /**
     * Returns the argument value for a given argument name.
     *
     * @param string $name The argument name
     *
     * @return string|string[]|null The argument value
     *
     * @throws InvalidArgumentException When argument given doesn't exist
     */
    protected function getArgument(string $name)
    {
        return $this->input->getArgument($name);
    }

    /**
     * Returns all the given options merged with the default values.
     *
     * @return array
     */
    protected function getOptions()
    {
        return $this->input->getOptions();
    }

    /**
     * Returns the option value for a given option name.
     *
     * @param string $name The option name
     *
     * @return bool|string|string[]|null The option value
     *
     * @throws InvalidArgumentException When option given doesn't exist
     */
    protected function getOption(string $name)
    {
        return $this->input->getOption($name);
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

    /**
     * Writes a message base on result data
     *
     * @param array $ret
     * @return int
     */
    protected function ret(array $ret): int
    {
        $type = 1 === $ret['code'] ? 'suc' : 'err';
        return $this->{$type}($ret['message'], $ret['code']);
    }

    /**
     * @param string $command
     * @param array $input
     * @return int
     * @throws \Exception
     */
    protected function runCommand(string $command, array $input)
    {
        $command = $this->getApplication()->find($command);
        return $command->run(new ArrayInput($input), $this->output);
    }
}
