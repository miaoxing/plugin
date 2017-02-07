<?php

namespace Miaoxing\Plugin\Traits;

trait Option
{
    /**
     * Constructor
     *
     * @param array $options The property options
     * @throws \InvalidArgumentException When option "wei" is not an instance of "Wei\Wei"
     */
    public function __construct(array $options = array())
    {
        $this->setOption($options);
    }

    public function setOption($name, $value = null)
    {
        // Set options
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->setOption($k, $v);
            }
            return $this;
        }

        if (method_exists($this, $method = 'set' . $name)) {
            return $this->$method($value);
        } else {
            $this->$name = $value;
            return $this;
        }
    }
}
