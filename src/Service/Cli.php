<?php

namespace Miaoxing\Plugin\Service;

use miaoxing\plugin\BaseService;

class Cli extends BaseService
{
    const REST = 0;

    const RED = 31;

    const GREEN = 32;

    /**
     * @param string $text
     * @return string
     */
    public function success($text)
    {
        return $this->format(self::GREEN, $text);
    }

    /**
     * @param string $text
     * @return string
     */
    public function error($text)
    {
        return $this->format(self::RED, $text);
    }

    /**
     * @param string $color
     * @param string $text
     * @return string
     */
    protected function format($color, $text)
    {
        return "\033[0;{$color}m" . $text . "\x1b[0m";
    }
}
