<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;
use Miaoxing\Plugin\Command\BaseCommand;
use Miaoxing\Plugin\Schedule\Task;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * @mixin \EventPropMixin
 * @experimental
 */
class Schedule extends BaseService
{
    /**
     * @var array<Task>
     */
    protected $tasks = [];

    /**
     * @var OutputInterface|null
     */
    protected $output;

    /**
     * @return array<Task>
     */
    public function getTasks(): array
    {
        $this->event->trigger('schedule', [$this]);

        return $this->tasks;
    }

    /**
     * @return array<Task>
     */
    public function getDueTasks(): array
    {
        return array_filter($this->getTasks(), static function (Task $task) {
            return $task->isDue();
        });
    }

    /**
     * @param string|class-string<Task>|\Closure $task
     * @param mixed|null $name
     * @return Task
     */
    public function add($task, $name = null): Task
    {
        if ($task instanceof \Closure) {
            $object = new Task();
            $object->setCallback($task);
        } else {
            $object = new $task();
        }

        if ($name) {
            $object->setName($name);
        } elseif ($task instanceof \Closure) {
            $fn = new \ReflectionFunction($task);
            $object->setName('Closure: ' . $fn->getFileName() . ':' . $fn->getStartLine());
        }

        $this->tasks[] = $object;
        return $object;
    }

    /**
     * @param string|class-string<BaseCommand> $class
     * @param array $parameters
     * @return Task
     */
    public function addCommand(string $class, array $parameters = []): Task
    {
        return $this->add(static function () use ($class, $parameters) {
            $command = new $class();
            $output = new BufferedOutput();
            $command->run(new ArrayInput($parameters), $output);
            return $output->fetch();
        }, $class);
    }

    /**
     * @param string|array|Process|mixed $command
     * @return Task
     */
    public function addShell($command): Task
    {
        if (is_array($command)) {
            $name = implode(' ', $command);
        } elseif (is_string($command)) {
            $name = $command;
        } elseif ($command instanceof Process) {
            $name = $command->getCommandLine();
        } else {
            $name = null;
        }

        return $this->add(static function () use ($command) {
            if (is_array($command)) {
                $process = new Process($command);
            } elseif (is_string($command)) {
                $process = Process::fromShellCommandline($command);
            } elseif ($command instanceof Process) {
                $process = $command;
            } else {
                throw new \InvalidArgumentException(sprintf(
                    'Expected argument of type string, array or Process, "%s" given',
                    is_object($command) ? get_class($command) : gettype($command)
                ));
            }
            $process->run();
            return $process->getOutput();
        }, $name);
    }

    /**
     * Remove all tasks
     *
     * @return $this
     */
    public function clearTasks(): self
    {
        $this->tasks = [];
        return $this;
    }

    /**
     * @return array
     */
    public function run(): array
    {
        $tasks = $this->getDueTasks();
        if (!$tasks) {
            $this->writeln('<info>No task to run.</info>');
        }

        $results = [];
        foreach ($tasks as $task) {
            $results[] = $this->runTask($task);
        }
        return $results;
    }

    /**
     * Run specified task
     *
     * @param Task $task
     * @return mixed
     */
    public function runTask(Task $task)
    {
        $this->writeln(sprintf('Start task <info>%s</info>', $task->getName()));

        $result = $task->run();

        $this->writeln(sprintf('End task <info>%s</info>', $task->getName()));

        return $result;
    }

    /**
     * Run the task by name
     *
     * @param string $name
     * @return mixed
     */
    public function runByName(string $name)
    {
        $task = current(array_filter($this->getTasks(), static function (Task $task) use ($name) {
            return $task->getName() === $name;
        }));
        if (!$task) {
            throw new \InvalidArgumentException(sprintf('Task %s not found', $name));
        }
        return $this->runTask($task);
    }

    /**
     * @param OutputInterface $output
     * @return $this
     */
    public function setOutput(OutputInterface $output): self
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @param iterable|string $output
     * @return void
     */
    protected function writeln($output): void
    {
        if ($this->output) {
            $this->output->writeln($output);
        }
    }
}
