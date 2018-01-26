<?php

namespace Miaoxing\Plugin;

use InvalidArgumentException;
use Miaoxing\Plugin\Model\CamelCaseTrait;
use Miaoxing\Plugin\Model\CastTrait;
use Miaoxing\Plugin\Model\QuickQueryTrait;
use Wei\Record;

class BaseModelV2 extends BaseModel
{
    use CamelCaseTrait;
    use CastTrait;
    use QuickQueryTrait;

    protected $providers = [
        'db' => 'app.db',
    ];

    protected $appIdColumn = 'app_id';

    protected $createdAtColumn = 'created_at';

    protected $createdByColumn = 'created_by';

    protected $updatedAtColumn = 'updated_at';

    protected $updatedByColumn = 'updated_by';

    protected $deletedByColumn = 'deleted_by';

    protected $deletedAtColumn = 'deleted_at';

    protected $userIdColumn = 'user_id';

    protected $toArrayV2 = true;

    protected $tableV2 = true;

    protected $guarded = [
        'id',
        'app_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected $hidden = [
        'app_id',
        'deleted_at',
        'deleted_by',
    ];

    protected $dataSources = [
        '*' => 'php',
    ];

    protected $virtualData = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        if (!$this->isNew) {
            $this->setDataSource('*', 'db');
        }
    }

    /**
     * Returns the success result with model data
     *
     * @param array $merge
     * @return array
     */
    public function toRet(array $merge = [])
    {
        if ($this->isColl()) {
            return $this->suc($merge + [
                    'data' => $this,
                    'page' => $this->getSqlPart('page'),
                    'rows' => $this->getSqlPart('limit'),
                    'records' => $this->count(),
                ]);
        } else {
            return $this->suc($merge + ['data' => $this]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value = null)
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
     * {@inheritdoc}
     */
    public function get($name)
    {
        $name = $this->filterInputColumn($name);
        $value = Record::get($name);
        $source = $this->getDataSource($name);

        if ($source === 'php') {
            return $value;
        }

        // 先通过setter转换为db数据
        if ($source === 'user') {
            $value = $this->getSetValue($name, $value);
        }

        // 通过getter处理数据
        $result = $this->callGetter($name, $this->data[$name]);
        if ($result) {
            $this->setDataSource($name, 'php');

            return $this->data[$name];
        }

        // 通过事件处理数据
        $this->data[$name] = $this->trigger('getValue', [$value, $name]);
        $this->setDataSource($name, 'php');

        return $this->data[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function &__get($name)
    {
        // Receive service that conflict with record method name
        if (in_array($name, ['db', 'cache', 'ret'])) {
            parent::__get($name);

            return $this->$name;
        }

        // Receive field value
        if ($this->hasColumn($name)) {
            return $this->getColumnValue($name);
        }

        // Receive virtual column value
        if ($this->hasVirtual($name)) {
            return $this->getVirtualValue($name);
        }

        // Receive relation
        if ($this->hasRelation($name)) {
            return $this->getRelation($name);
        }

        // Receive service
        parent::__get($name);

        return $this->$name;
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

        if ($this->hasColumn($name)) {
            return $this->set($name, $value);
        }

        if ($this->hasVirtual($name)) {
            return $this->setVirtualValue($name, $value);
        }

        if ($this->hasRelation($name)) {
            return $this->setRelationValue($name, $value);
        }

        if ($this->wei->has($name)) {
            return $this->$name = $value;
        }

        throw new InvalidArgumentException('Invalid property: ' . $name);
    }

    /**
     * {@inheritdoc}
     */
    public function &offsetGet($name)
    {
        $name = $this->filterInputColumn($name);

        if ($this->hasVirtual($name)) {
            return $this->getVirtualValue($name);
        }

        parent::offsetGet($name);

        return $this->data[$name];
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

        // 清空原来的数据
        if ($result) {
            $this->setDataSource('*', 'db');
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll($conditions = false)
    {
        $this->isColl = true;
        $data = $this->fetchAll($conditions);

        $records = array();
        foreach ($data as $key => $row) {
            /** @var $records BaseModelV2[] */
            $records[$key] = $this->db->init($this->table, [], false);
            $records[$key]->setRawData($row);
            $records[$key]->triggerCallback('afterFind');
        }

        $this->data = $records;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function save($data = array())
    {
        // 1. Merges data from parameters
        $data && $this->fromArray($data);

        // 将数据转换为数据库数据
        $origData = $this->data;
        $this->data = $this->generateDbData();

        parent::save();

        // 还原原来的数据+save过程中生成的主键数据
        $this->data = $origData + $this->data;

        return $this;
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

    protected function getSetValue($name, $value)
    {
        $method = 'set' . $this->camel($name) . 'Attribute';
        if (method_exists($this, $method)) {
            $this->$method($value);
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

    protected function &getColumnValue($name)
    {
        $this->get($name);

        return $this->data[$this->filterInputColumn($name)];
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
     * @return mixed
     */
    protected function setVirtualValue($name, $value)
    {
        $method = 'set' . $this->camel($name) . 'Attribute';
        if (method_exists($this, $method)) {
            return $this->$method($value);
        }

        throw new InvalidArgumentException('Invalid virtual column: ' . $name);
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
     */
    protected function setRelationValue($name, $value)
    {
        $this->$name = $value;
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
}
