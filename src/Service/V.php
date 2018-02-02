<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;
use Wei\Record;
use Wei\RetTrait;
use Wei\Validate;

/**
 * A chaining validator
 *
 * @method $this string()
 * @method $this callback(callable $fn)
 * @method $this mobileCn()
 * @method $this notEqualTo($value)
 * @method $this digit()
 * @method $this between($min, $max)
 * @method $this recordExists(string|Record $table, string $field = null)
 * @method $this notRecordExists(string|Record $table, string $field = null)
 * @method $this type($type)
 * @method $this positiveInteger()
 * @method $this greaterThanOrEqual($value)
 * @see Inspired by https://github.com/Respect/Validation/tree/1.1
 */
class V extends BaseService
{
    use RetTrait;

    /**
     * @var Validate
     */
    protected $validator;

    /**
     * @var array
     */
    protected $options = [
        'data' => [],
        'rules' => [],
        'names' => [],
    ];

    /**
     * @var string
     */
    protected $lastKey = '';

    /**
     * @var string
     */
    protected $lastRule;

    /**
     * Create a new validator
     *
     * @param array $options
     * @return $this
     */
    public function __invoke(array $options = [])
    {
        $validator = new self($options + get_object_vars($this));

        return $validator;
    }

    /**
     * Add a new field
     *
     * @param string $name
     * @param string|null $label
     * @return $this
     */
    public function key($name, $label = null)
    {
        $this->lastKey = $name;

        // Rest previous key's last rule
        $this->lastRule = null;

        if (!isset($this->options['rules'][$name])) {
            $this->options['rules'][$name] = [];
        }

        if (isset($label)) {
            $this->label($label);
        }

        return $this;
    }

    /**
     * Add name for current field
     *
     * @param string $label
     * @return $this
     */
    public function label($label)
    {
        $this->options['names'][$this->lastKey] = $label;

        return $this;
    }

    /**
     * Set rule message for current field
     *
     * @param string $ruleOrMessage
     * @param string|null $message
     * @return $this
     */
    public function message($ruleOrMessage, $message = null)
    {
        if (1 === func_num_args()) {
            $rule = $this->lastRule;
            $message = $ruleOrMessage;
        } else {
            $rule = $ruleOrMessage;
        }

        $this->options['messages'][$this->lastKey][$rule] = $message;

        return $this;
    }

    /**
     * Returns the \Wei\Validate object
     *
     * @param mixed $data
     * @return Validate
     */
    public function validate($data = null)
    {
        return $this->getValidator($data);
    }

    /**
     * Returns the validation result
     *
     * @param mixed $data
     * @return bool
     */
    public function isValid($data = null)
    {
        return $this->getValidator($data)->isValid();
    }

    /**
     * Validate the data and return the ret array
     *
     * @param mixed $data
     * @return array
     */
    public function check($data = null)
    {
        $validator = $this->getValidator($data);

        if ($validator->isValid()) {
            return $this->suc();
        } else {
            return $this->err($validator->getFirstMessage());
        }
    }

    /**
     * Custom handler for required rule
     *
     * @param bool $required
     * @return $this
     */
    public function required($required = true)
    {
        return $this->addRule('required', $required);
    }

    /**
     * Set data for validation
     *
     * @param mixed $data
     * @return $this
     */
    public function data($data)
    {
        if (!$data) {
            return $this;
        }

        // Validate without key
        if (!$this->lastKey) {
            $data = ['' => $data];
        }

        $this->options['data'] = $data;

        return $this;
    }

    /**
     * Instance validate object
     *
     * @param mixed $data
     * @return Validate
     */
    protected function getValidator($data = null)
    {
        if (!$this->validator) {
            if ($data) {
                // Validate without key
                if (!$this->lastKey) {
                    $data = ['' => $data];
                }

                $this->options['data'] = $data;
            }

            $this->validator = $this->wei->validate($this->options);
        }

        return $this->validator;
    }

    /**
     * Add rule for current field
     *
     * @param string $name
     * @param mixed $args
     * @return $this
     */
    protected function addRule($name, $args)
    {
        $this->options['rules'][$this->lastKey][$name] = $args;
        $this->lastRule = $name;

        return $this;
    }

    /**
     * Add rule for current field
     *
     * @param string $name
     * @param array $args
     * @return $this
     */
    public function __call($name, $args)
    {
        return $this->addRule($name, $args);
    }
}
