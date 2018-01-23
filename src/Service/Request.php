<?php

namespace Miaoxing\Plugin\Service;

class Request extends \Wei\Request
{
    /**
     * 因为 &offsetGet 产生的额外键名
     *
     * @var array
     */
    protected $extraKeys = [];

    public function json()
    {
        return $this->acceptJson();
    }

    public function csv()
    {
        return $this['_format'] == 'csv';
    }

    public function &offsetGet($offset)
    {
        if (!isset($this->data[$offset])) {
            $this->extraKeys[$offset] = true;
        }

        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        unset($this->extraKeys[$offset]);

        return $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->extraKeys[$offset]);
        unset($this->data[$offset]);
    }

    public function toArray()
    {
        $this->removeExtraKeys();

        return parent::toArray();
    }

    public function getIterator()
    {
        $this->removeExtraKeys();

        return new \ArrayIterator($this->data);
    }

    protected function removeExtraKeys()
    {
        foreach ($this->extraKeys as $offset => $value) {
            if ($this->data[$offset] === null) {
                unset($this->data[$offset]);
            }
        }
        $this->extraKeys = [];
    }

    public function count()
    {
        $this->removeExtraKeys();

        return parent::count();
    }
}
