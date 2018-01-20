<?php

namespace Miaoxing\Plugin;

use Wei\Request;

/**
 * @property Request $request
 */
trait CliDefinitionTrait
{
    /**
     * @var int
     */
    protected $argumentIndex = 0;

    /**
     * @param string $name
     * @param int|null $index
     * @return CliDefinitionTrait
     */
    protected function addArgument($name, $index = null)
    {
        if ($index === null) {
            $index = $this->argumentIndex++;
        }

        // $argv looks like ['index.php', 'command', 'arg1', 'arg2']
        // arguments are start from the third element
        return $this->addOption($name, $index + 2);
    }

    /**
     * @param string $name
     * @param string|null $shortcut
     * @return $this
     */
    protected function addOption($name, $shortcut = null)
    {
        if (isset($this->request[$shortcut]) && !isset($this->request[$name])) {
            $this->request[$name] = $this->request[$shortcut];
        }

        return $this;
    }
}
