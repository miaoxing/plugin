<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;
use Wei\RetTrait;

/**
 * 链式校验
 *
 * @method $this required($required)
 * @method $this string()
 * @link Inspired by https://github.com/Respect/Validation/tree/1.1
 */
class V extends BaseService
{
    use RetTrait;

    protected $validator;

    protected $options = [
        'data' => [],
        'rules' => [],
        'names' => [],
    ];

    protected $lastKey = 'default';

    /**
     * @param array $options
     * @return $this
     */
    public function __invoke(array $options = [])
    {
        $validator = new self($options + get_object_vars($this));

        return $validator;
    }

    public function key($name, $label = null)
    {
        $this->lastKey = $name;

        if (isset($label)) {
            $this->label($label);
        }

        return $this;
    }

    public function label($label)
    {
        $this->options['names'][$this->lastKey] = $label;

        return $this;
    }

    public function validate($data)
    {
        $this->options['data'] = $data;

        $validator = $this->validator = wei()->validate($this->options);

        if ($validator->isValid()) {
            return $this->suc();
        } else {
            return $this->err($validator->getFirstMessage());
        }
    }

    public function check()
    {
        return $this;
    }

    public function assert()
    {
        return $this;
    }

    public function toRet()
    {

    }

    public function __call($name, $args)
    {
        $this->options['rules'][$this->lastKey][$name] = $args;

        return $this;
    }
}
