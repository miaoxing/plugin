<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Schedule\Task;
use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Fixture\Command\Test;
use MiaoxingTest\Plugin\Fixture\Task\DoSomething;
use Symfony\Component\Process\Process;

/**
 * @mixin \SchedulePropMixin
 */
class ScheduleTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->schedule->clearTasks();
    }

    public function testAddCallback()
    {
        $this->schedule->add(static function () {
            return 'abc';
        })->everyMinute();

        $result = $this->schedule->run();

        $this->assertSame('abc', $result[0]);
    }

    public function testAddCommand()
    {
        $this->schedule->addCommand(Test::class)->everyMinute();

        $result = $this->schedule->run();

        $this->assertSame('test', $result[0]);
    }

    public function testAddShell()
    {
        $this->schedule->addShell('php -m')->everyMinute();

        $result = $this->schedule->run();

        $this->assertStringContainsString('pdo_mysql', $result[0]);
    }

    public function testAddShellArray()
    {
        $this->schedule->addShell(['php', '-m'])->everyMinute();

        $result = $this->schedule->run();

        $this->assertStringContainsString('pdo_mysql', $result[0]);
    }

    public function testAddShellProcess()
    {
        $this->schedule->addShell(new Process(['php', '-m']));

        $result = $this->schedule->run();

        $this->assertStringContainsString('pdo_mysql', $result[0]);
    }

    public function testAddTask()
    {
        $this->schedule->add(DoSomething::class);

        $result = $this->schedule->run();

        $this->assertStringContainsString('do something', $result[0]);
    }

    public function testRunEmpty()
    {
        $result = $this->schedule->run();

        $this->assertEmpty($result);
    }

    public static function dataProviderForCron(): array
    {
        return [
            ['everyMinute', [], '2023-01-01 00:01:00'],
            ['everyTwoMinutes', [], '2023-01-01 00:02:00'],
            ['everyThreeMinutes', [], '2023-01-01 00:03:00'],
            ['everyFourMinutes', [], '2023-01-01 00:04:00'],
            ['everyFiveMinutes', [], '2023-01-01 00:05:00'],
            ['everySixMinutes', [], '2023-01-01 00:06:00'],
            ['everyMinutes', [30], '2023-01-01 00:30:00'],
            ['hourly', [], '2023-01-01 01:00:00'],
            ['hourlyAt', [30], '2023-01-01 00:30:00'],
            ['hourlyAt', [31, 32], '2023-01-01 00:31:00'],
            ['everyHours', [2], '2023-01-01 02:00:00'],
            ['everyHours', [3], '2023-01-01 03:00:00'],
            ['everyTwoHours', [], '2023-01-01 02:00:00'],
            ['everyThreeHours', [], '2023-01-01 03:00:00'],
            ['everyFourHours', [], '2023-01-01 04:00:00'],
            ['everySixHours', [], '2023-01-01 06:00:00'],
            ['everyDay', [], '2023-01-02 00:00:00'],
            ['everyDays', [2], '2023-01-03 00:00:00'],
            ['everyDays', [3], '2023-01-04 00:00:00'],
            ['mondays', [], '2023-01-02 00:00:00'],
            ['tuesdays', [], '2023-01-03 00:00:00'],
            ['wednesdays', [], '2023-01-04 00:00:00'],
            ['thursdays', [], '2023-01-05 00:00:00'],
            ['fridays', [], '2023-01-06 00:00:00'],
            ['saturdays', [], '2023-01-07 00:00:00'],
            ['sundays', [], '2023-01-08 00:00:00'],
            ['weekdays', [], '2023-01-02 00:00:00'],
            ['weekends', [], '2023-01-07 00:00:00'],
            ['monthly', [], '2023-02-01 00:00:00'],
            ['quarterly', [], '2023-04-01 00:00:00'],
            ['yearly', [], '2024-01-01 00:00:00'],
        ];
    }

    /**
     * @dataProvider dataProviderForCron
     * @param mixed $method
     * @param mixed $params
     * @param mixed $next
     */
    public function testCron($method, $params, $next)
    {
        $task = new Task();
        $task->{$method}(...$params);
        $task->setNow(new \DateTime('2023-01-01 00:00:00'));
        $date = $task->getNextRunDate();
        $this->assertSame($next, $date->format('Y-m-d H:i:s'));
    }
}
