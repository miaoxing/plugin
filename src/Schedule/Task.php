<?php

namespace Miaoxing\Plugin\Schedule;

use Cron\CronExpression;
use Miaoxing\Plugin\BaseService;

/**
 * @experimental
 */
class Task extends BaseService
{
    /**
     * The cron expression
     *
     * @var string
     */
    protected $cron = '* * * * *';

    /**
     * @var callable
     */
    protected $callback;

    /**
     * The name of the task
     *
     * @var string|null
     */
    protected $name;

    /**
     * @var \Datetime|null
     */
    protected $now;

    /**
     * Run the task
     *
     * @return mixed
     */
    public function run()
    {
        return call_user_func($this->callback);
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function setCallback(callable $callback): self
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name ?? static::class;
    }

    /**
     * Set the cron expression
     *
     * @param string $cron
     * @return $this
     */
    public function cron(string $cron): self
    {
        $this->cron = $cron;
        return $this;
    }

    public function getCron(): string
    {
        return $this->cron;
    }

    public function setNow($now)
    {
        $this->now = $now;
    }

    public function now()
    {
        return $this->now;
    }

    protected function getCronExpression(): CronExpression
    {
        return CronExpression::factory($this->getCron());
    }

    public function isDue(): bool
    {
        return $this->getCronExpression()->isDue($this->now() ?? 'now');
    }

    public function getNextRunDate(): \DateTime
    {
        return $this->getCronExpression()->getNextRunDate($this->now());
    }

    public function everyMinute(): self
    {
        return $this->cron('* * * * *');
    }

    public function everyTwoMinutes(): self
    {
        return $this->everyMinutes(2);
    }

    public function everyThreeMinutes(): self
    {
        return $this->everyMinutes(3);
    }

    public function everyFourMinutes(): self
    {
        return $this->everyMinutes(4);
    }

    public function everyFiveMinutes(): self
    {
        return $this->everyMinutes(5);
    }

    public function everySixMinutes(): self
    {
        return $this->everyMinutes(6);
    }

    public function everyMinutes(int $minute): self
    {
        return $this->cron('*/' . $minute . ' * * * *');
    }

    public function hourly(): self
    {
        return $this->cron('0 * * * *');
    }

    public function hourlyAt(int ...$minute): self
    {
        return $this->cron(implode(',', $minute) . ' * * * *');
    }

    public function everyHours($hour): self
    {
        return $this->cron('0 */' . $hour . ' * * *');
    }

    public function everyTwoHours(): self
    {
        return $this->everyHours(2);
    }

    public function everyThreeHours(): self
    {
        return $this->everyHours(3);
    }

    public function everyFourHours(): self
    {
        return $this->everyHours(4);
    }

    public function everySixHours(): self
    {
        return $this->everyHours(6);
    }

    public function everyDay(): self
    {
        return $this->cron('0 0 * * *');
    }

    public function everyDays(int ...$days): self
    {
        return $this->cron('0 0 */' . implode(',', $days) . ' * *');
    }

    public function mondays(): self
    {
        return $this->cron('0 0 * * 1');
    }

    public function tuesdays(): self
    {
        return $this->cron('0 0 * * 2');
    }

    public function wednesdays(): self
    {
        return $this->cron('0 0 * * 3');
    }

    public function thursdays(): self
    {
        return $this->cron('0 0 * * 4');
    }

    public function fridays(): self
    {
        return $this->cron('0 0 * * 5');
    }

    public function saturdays(): self
    {
        return $this->cron('0 0 * * 6');
    }

    public function sundays(): self
    {
        return $this->cron('0 0 * * 7');
    }

    public function weekdays(): self
    {
        return $this->cron('0 0 * * 1-5');
    }

    public function weekends(): self
    {
        return $this->cron('0 0 * * 6,0');
    }

    public function monthly(): self
    {
        return $this->cron('0 0 1 * *');
    }

    public function quarterly(): self
    {
        return $this->cron('0 0 1 */3 *');
    }

    public function yearly(): self
    {
        return $this->cron('0 0 1 1 *');
    }
}
