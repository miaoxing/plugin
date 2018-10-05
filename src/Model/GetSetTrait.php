<?php

namespace Miaoxing\Plugin\Model;

use InvalidArgumentException;
use Miaoxing\Plugin\BaseService;
use Wei\Record;

trait GetSetTrait
{
    protected $dataSources = [
        '*' => 'php',
    ];

    /**
     * @var array
     */
    protected $virtualData = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options = [])
    {
        if (isset($options['isNew']) && $options['isNew'] === false) {
            $this->setRawData($options['data']);
            unset($options['data']);
        }

        parent::__construct($options);
    }

    /**
     * {@inheritdoc}
     */
    public function fromArray($data)
    {
        foreach ($data as $key => $value) {
            if (is_int($key) || $this->isFillable($key, $data)) {
                $this->set($key, $value, false);
            }
        }

        return $this;
    }

    /**
     * Set raw data to model
     *
     * @param array $data
     * @return $this
     */
    public function setRawData(array $data)
    {
        $this->data = $data + $this->data;

        if ($data) {
            $this->loaded = true;
            $this->setDataSource('*', 'db');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function &get($name, &$exists = null, $throwException = true)
    {
        $exists = true;

        // Receive field value
        if ($this->isCollKey($name) || $this->hasColumn($name)) {
            return $this->getColumnValue($name);
        }

        // Receive virtual column value
        if ($this->hasVirtual($name)) {
            return $this->getVirtualValue($name);
        }

        // Receive relation
        if ($this->hasRelation($name)) {
            return $this->getRelationValue($name);
        }

        $exists = false;
        if ($throwException) {
            throw new InvalidArgumentException('Invalid property: ' . $name);
        } else {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value = null, $throwException = true)
    {
        if ($this->isCollKey($name) || $this->hasColumn($name)) {
            return $this->setColumnValue($name, $value);
        }

        if ($this->hasVirtual($name)) {
            return $this->setVirtualValue($name, $value);
        }

        if ($this->hasRelation($name)) {
            return $this->setRelationValue($name, $value);
        }

        if ($throwException) {
            throw new InvalidArgumentException('Invalid property: ' . $name);
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function &__get($name)
    {
        // Receive service that conflict with record method name
        if (in_array($name, $this->requiredServices)) {
            return $this->getServiceValue($name);
        }

        $value = &$this->get($name, $exists, false);
        if ($exists) {
            return $value;
        }

        // Receive other services
        return $this->getServiceValue($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function __set($name, $value = null)
    {
        // Required services first
        if (in_array($name, $this->requiredServices)) {
            return $this->$name = $value;
        }

        $result = $this->set($name, $value, false);
        if ($result) {
            return;
        }

        if ($this->wei->has($name)) {
            return $this->$name = $value;
        }

        throw new InvalidArgumentException('Invalid property: ' . $name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    protected function setColumnValue($name, $value)
    {
        // Ignore $coll[] = $value
        if ($name !== null) {
            $name = $this->filterInputColumn($name);

            // 直接设置就行
            Record::set($name, $value);

            $this->setDataSource($name, 'user');
        } else {
            Record::set($name, $value);
        }

        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    protected function &getColumnValue($name)
    {
        $name = $this->filterInputColumn($name);
        $value = Record::get($name);

        $source = $this->getDataSource($name);
        if ($source === 'php') {
            return $this->data[$name];
        }

        // 用户数据则先转换为db数据
        if ($source === 'user') {
            $value = $this->getSetValue($name, $value);
        }

        // 通过getter处理数据
        $this->data[$name] = $this->getGetValue($name, $value);
        $this->setDataSource($name, 'php');

        return $this->data[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function &offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @param string $source
     */
    protected function setDataSource($name, $source)
    {
        $this->dataSources[$name] = $source;
    }

    /**
     * {@inheritdoc}
     */
    public function find($conditions = false)
    {
        $result = parent::find($conditions);
        if ($result) {
            $this->setDataSource('*', 'db');
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function save($data = [])
    {
        $data && $this->fromArray($data);

        // 将数据转换为数据库数据
        $origData = $this->data;
        $this->data = $this->generateDbData();
        $isNew = $this->isNew;

        parent::save();

        // 解决保存之前调用了$this->id导致变为null的问题
        if ($isNew && array_key_exists($this->primaryKey, $origData)) {
            $origData[$this->primaryKey] = $this->data[$this->primaryKey];
            $this->setDataSource($this->primaryKey, 'db');
        }

        // 还原原来的数据+save过程中生成的主键数据
        $this->data = $origData + $this->data;

        return $this;
    }

    public function reload()
    {
        $this->dataSources = ['*' => 'db'];
        return parent::reload();
    }

    /**
     * Returns the data source of specified column name
     *
     * @param string $name
     * @return string
     */
    protected function getDataSource($name)
    {
        return isset($this->dataSources[$name]) ? $this->dataSources[$name] : $this->dataSources['*'];
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    protected function getGetValue($name, $value)
    {
        $result = $this->callGetter($name, $value);
        if ($result) {
            return $value;
        } else {
            return $this->trigger('getValue', [$value, $name]);
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    protected function getSetValue($name, $value)
    {
        $result = $this->callSetter($name, $value);
        if ($result) {
            $value = $this->data[$name];
        } else {
            $value = $this->trigger('setValue', [$value, $name]);
        }

        return $value;
    }

    /**
     * Generates data for saving to database
     *
     * @return array
     */
    protected function generateDbData()
    {
        $dbData = [];
        foreach ($this->data as $name => $value) {
            if ($this->getDataSource($name) !== 'db') {
                $dbData[$name] = $this->getSetValue($name, $value);
            } else {
                $dbData[$name] = $value;
            }
        }

        return $dbData;
    }

    /**
     * Returns the service object
     *
     * @param string $name
     * @return BaseService
     * @throws \Exception
     */
    protected function &getServiceValue($name)
    {
        parent::__get($name);

        return $this->$name;
    }

    /**
     * Returns the virtual column value
     *
     * @param string $name
     * @return mixed
     */
    protected function &getVirtualValue($name)
    {
        $result = $this->callGetter($name, $this->virtualData[$name]);
        if ($result) {
            return $this->virtualData[$name];
        }

        throw new InvalidArgumentException('Invalid virtual column: ' . $name);
    }

    /**
     * Sets the virtual column value
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    protected function setVirtualValue($name, $value)
    {
        $result = $this->callSetter($name, $value);
        if (!$result) {
            throw new InvalidArgumentException('Invalid virtual column: ' . $name);
        }

        return $this;
    }

    /**
     * Check if the name is virtual column
     *
     * @param string $name
     * @return bool
     */
    protected function hasVirtual($name)
    {
        $name = $this->filterInputColumn($name);

        return in_array($name, $this->virtual);
    }

    /**
     * Sets relation value
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    protected function setRelationValue($name, $value)
    {
        $this->$name = $value;

        return $this;
    }

    /**
     * Check if model has specified relation
     *
     * @param string $name
     * @return bool
     */
    protected function hasRelation($name)
    {
        return method_exists($this, $name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    protected function callGetter($name, &$value)
    {
        $method = 'get' . $this->camel($name) . 'Attribute';
        if ($result = method_exists($this, $method)) {
            $value = $this->$method();
        }

        return $result;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    protected function callSetter($name, $value)
    {
        $method = 'set' . $this->camel($name) . 'Attribute';
        if ($result = method_exists($this, $method)) {
            $this->$method($value);
        }

        return $result;
    }
}
