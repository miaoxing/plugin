<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;
use Wei\Record;
use Wei\RetTrait;
use Wei\Validate;

/**
 * A chaining validator
 *
 * Data type and composition
 * @method $this album($input) Check if the input contains letters (a-z) and digits (0-9)
 * @method $this notAlbum($input)
 * @method $this alpha($input) Check if the input contains only letters (a-z)
 * @method $this notAlpha($input)
 * @method $this blank($input) Check if the input is blank
 * @method $this contains($input, $search, $regex = false) Check if the input is contains the specified string or pattern
 * @method $this notContains($input, $search, $regex = false)
 * @method $this decimal($input) Check if the input is decimal
 * @method $this notDecimal($input)
 * @method $this digit($input) Check if the input contains only digits (0-9)
 * @method $this notDigit($input)
 * @method $this divisibleBy($input, $divisor) Check if the input could be divisible by specified divisor
 * @method $this notDivisibleBy($input, $divisor)
 * @method $this doubleByte($input) Check if the input contains only double characters
 * @method $this notDoubleByte($input)
 * @method $this present($input) Check if the input is empty
 * @method $this notPresent($input)
 * @method $this endsWith($input, $findMe, $case = false) Check if the input is ends with specified string
 * @method $this notEndsWith($input, $findMe, $case = false)
 * @method $this in($input, array $array, $strict = false) Check if the input is in specified array
 * @method $this notIn($input, array $array, $strict = false)
 * @method $this lowercase($input) Check if the input is lowercase
 * @method $this notLowercase($input)
 * @method $this luhn($input) Check if the input is valid by the Luhn algorithm
 * @method $this notLuhn($input)
 * @method $this naturalNumber($input) Check if the input is a natural number (integer that greater than or equals 0)
 * @method $this notNaturalNumber($input)
 * @method $this null($input) Check if the input is null
 * @method $this notNull($input)
 * @method $this number($input) Check if the input is number
 * @method $this notNumber($input)
 * @method $this positiveInteger($input) Check if the input is a positive integer (integer that greater than 0)
 * @method $this notPositiveInteger($input)
 * @method $this regex($input, $pattern) Check if the input is valid by specified regular expression
 * @method $this notRegex($input, $pattern)
 * @method $this startsWith($input, $findMe, $case = false) Check if the input is starts with specified string
 * @method $this notStartsWith($input, $findMe, $case = false)
 * @method $this type($input, $type) Check if the type of input is equals specified type name
 * @method $this notType($input, $type)
 * @method $this uppercase($input) Check if the input is uppercase
 * @method $this notUppercase($input)
 *
 * Length
 * @method $this length($input, $length, $max = null) Check if the length (or size) of input is equals specified length or in specified length range
 * @method $this notLength($input, $length, $max = null)
 * @method $this charLength($input, $length) Check if the characters length of input is equals specified length
 * @method $this notCharLength($input, $length)
 * @method $this minLength($input, $min) Check if the length (or size) of input is greater than specified length
 * @method $this notMinLength($input, $min)
 * @method $this maxLength($input, $max) Check if the length (or size) of input is lower than specified length
 * @method $this notMaxLength($input, $max)
 *
 * Comparison
 * @method $this equalTo($input, $value) Check if the input is equals to (==) the specified value
 * @method $this notEqualTo($input, $value)
 * @method $this identicalTo($input, $value) Check if the input is equals to (==) the specified value
 * @method $this notIdenticalTo($input, $value)
 * @method $this greaterThan($input, $value) Check if the input is greater than (>=) the specified value
 * @method $this notGreaterThan($input, $value)
 * @method $this greaterThanOrEqual($input, $value) Check if the input is greater than or equal to (>=) the specified value
 * @method $this notGreaterThanOrEqual($input, $value)
 * @method $this lessThan($input, $value) Check if the input is less than (<) the specified value
 * @method $this notLessThan($input, $value)
 * @method $this lessThanOrEqual($input, $value) Check if the input is less than or equal to (<=) the specified value
 * @method $this notLessThanOrEqual($input, $value)
 * @method $this between($input, $min, $max) Check if the input is between the specified minimum and maximum value
 * @method $this notBetween($input, $min, $max)
 *
 * Date and time
 * @method $this date($input, $format = 'Y-m-d') Check if the input is a valid date
 * @method $this notate($input, $format = 'Y-m-d')
 * @method $this dateTime($input, $format = null) Check if the input is a valid datetime
 * @method $this notDateTime($input, $format = null)
 * @method $this time($input, $format = 'H:i:s') Check if the input is a valid time
 * @method $this notTime($input, $format = 'H:i:s')
 *
 * File and directory
 * @method $this dir($input) Check if the input is existing directory
 * @method $this notDir($input)
 * @method $this exists($input) Check if the input is existing file or directory
 * @method $this notExists($input)
 * @method $this file($input, array $options) Check if the input is valid file
 * @method $this notFile($input, array $options)
 * @method $this image($input, array $options = array()) Check if the input is valid image
 * @method $this notImage($input, array $options = array())
 *
 * Network
 * @method $this email($input) Check if the input is valid email address
 * @method $this notEmail($input)
 * @method $this ip($input, array $options = array()) Check if the input is valid IP address
 * @method $this notIp($input, array $options = array())
 * @method $this tld($input) Check if the input is a valid top-level domain
 * @method $this notTld($input)
 * @method $this url($input, array $options = array()) Check if the input is valid URL address
 * @method $this notUrl($input, array $options = array())
 * @method $this uuid($input) Check if the input is valid UUID(v4)
 * @method $this notUuid($input)
 *
 * Region
 * @method $this creditCard($input, $type = null) Check if the input is valid credit card number
 * @method $this notCreditCard($input, $type = null)
 * @method $this phone($input) Check if the input is valid phone number, contains only digit, +, - and spaces
 * @method $this notPhone($input)
 * @method $this chinese($input) Check if the input contains only Chinese characters
 * @method $this notChinese($input)
 * @method $this idCardCn($input) Check if the input is valid Chinese identity card
 * @method $this notIdCardCn($input)
 * @method $this idCardHk($input) Check if the input is valid Hong Kong identity card
 * @method $this notIdCardHk($input)
 * @method $this idCardMo($input) Check if the input is valid Macau identity card
 * @method $this notIdCardMo($input)
 * @method $this idCardTw($input) Check if the input is valid Taiwan identity card
 * @method $this notIdCardTw($input)
 * @method $this phoneCn($input) Check if the input is valid Chinese phone number
 * @method $this notPhoneCn($input)
 * @method $this plateNumberCn($input) Check if the input is valid Chinese plate number
 * @method $this notPlateNumberCn($input)
 * @method $this postcodeCn($input) Check if the input is valid Chinese postcode
 * @method $this notPostcodeCn($input)
 * @method $this qQ($input) Check if the input is valid QQ number
 * @method $this notQQ($input)
 * @method $this mobileCn($input) Check if the input is valid Chinese mobile number
 * @method $this notMobileCn($input)
 *
 * Group
 * @method $this allOf($input, array $rules) Check if the input is valid by all of the rules
 * @method $this notAllOf($input, array $rules)
 * @method $this noneOf($input, array $rules) Check if the input is NOT valid by all of specified rules
 * @method $this notNoneOf($input, array $rules)
 * @method $this oneOf($input, array $rules) Check if the input is valid by any of the rules
 * @method $this notOneOf($input, array $rules)
 * @method $this someOf($input, array $rules, $atLeast) Check if the input is valid by specified number of the rules
 * @method $this notSomeOf($input, array $rules, $atLeast)
 *
 * Others
 * @method $this recordExists($input, $table, $field = 'id') Check if the input is existing table record
 * @method $this notRecordExists($input, $table, $field = 'id')
 * @method $this all($input, array $rules) Check if all of the element in the input is valid by all specified rules
 * @method $this notAll($input, array $rules)
 * @method $this callback($input, \Closure $fn, $message = null) Check if the input is valid by specified callback
 * @method $this notCallback($input, \Closure $fn, $message = null)
 * @method $this color($input) Check if the input is valid Hex color
 * @method $this notColor($input)
 * @method $this password($input, array $options = array()) Check if the input password is secure enough
 * @method $this notPassword($input, array $options = array())
 *
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
