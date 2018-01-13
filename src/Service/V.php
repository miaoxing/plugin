<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;
use Wei\RetTrait;

/**
 * 链式校验
 *
 * @method $this string()
 * @method $this callback(callable $fn)
 * @method $this mobileCn()
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

        if (!isset($this->options['rules'][$name])) {
            $this->options['rules'][$name] = [];
        }

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

    /**
     * Set rule message for current field
     *
     * @param string $rule
     * @param string $message
     * @return $this
     */
    public function message($rule, $message)
    {
        $this->options['messages'][$this->lastKey][$rule] = $message;

        return $this;
    }

    protected function getValidator($data = [])
    {
        if (!$this->validator) {
            if ($data) {
                $this->options['data'] = $data;
            }

            $this->validator = wei()->validate($this->options);
        }

        return $this->validator;
    }

    public function validate($data)
    {
        $this->options['data'] = $data;

        $validator = $this->validator = wei()->validate($this->options);
    }

    public function check($data)
    {
        $validator = $this->getValidator($data);

        if ($validator->isValid()) {
            return $this->suc();
        } else {
            return $this->err($validator->getFirstMessage());
        }
    }

    public function assert()
    {
        return $this;
    }

    public function toRet()
    {

    }

    /**
     * Required特殊处理
     *
     * @param bool $required
     */
    public function required($required = true)
    {
        $this->addRule('required', $required);
    }

    protected function addRule($name, $args)
    {
        $this->options['rules'][$this->lastKey][$name] = $args;
    }

    public function __call($name, $args)
    {
        $this->addRule($name, $args);

        return $this;
    }
}
