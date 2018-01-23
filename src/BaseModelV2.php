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

    protected $enableProperty = true;

    protected $tableV2 = true;

    protected $initV2 = true;

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

    /**
     * @var array
     */
    protected $rawData = [];

    /**
     * 设置原生数据,如从数据库读出的数据
     *
     * @param array $rawData
     * @return BaseModel
     */
    public function setRawData(array $rawData)
    {
        $this->rawData = $rawData;

        if ($rawData) {
            $this->loaded = true;
        }

        return $this;
    }

    /**
     * Executes the generated SQL and returns the found record object or false
     *
     * @param mixed $conditions
     * @return $this|false
     */
    public function find($conditions = false)
    {
        $this->isColl = false;
        $data = $this->fetch($conditions);
        if ($data) {
            $this->rawData = $data + $this->rawData;
            $this->data = [];
            $this->triggerCallback('afterFind');
            return $this;
        } else {
            return false;
        }
    }

    public function set($name, $value = null)
    {
        // 直接设置就行
        return Record::set($name, $value);
    }

    public function get($name)
    {
        $name = $this->filterInputColumn($name);

        // 如果有处理好的数据,直接返回
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        // 通过getXxx处理数据
        $method = 'get' . $this->camel($name) . 'Attribute';
        if (method_exists($this, $method)) {
            $this->data[$name] = $this->$method();

            return $this->data[$name];
        }

        // 通过rawData处理
        if (array_key_exists($name, $this->rawData)) {
            $this->data[$name] = $this->trigger('getValue', [$this->rawData[$name], $name]);
        }

        $this->data[$name] = Record::get($name);

        return $this->data[$name];
    }

    public function save($data = array())
    {
        // 1. Merges data from parameters
        $data && $this->fromArray($data);

        // 将数据转换为数据库数据
        $origData = $this->data;
        $data = $this->rawData;
        foreach ($this->data as $name => $value) {
            $method = 'set' . $this->camel($name) . 'Attribute';
            if (method_exists($this, $method)) {
                $this->$method($value);
                $data[$name] = $this->data[$name];
            } else {
                $data[$name] = $this->trigger('setValue', [$value, $name]);
            }
        }

        $this->data = $data;

        parent::save();

        // 还原原来的数据
        $this->data = $origData + $this->data;

        return $data;
    }

    public function __set($name, $value = null)
    {
        // TODO service 和 column 混用容易出错
        if (in_array($name, ['db'])) {
            $this->$name = $value;

            return;
        }

        if ($this->hasColumn($name)) {
            $this->set($name, $value);

            return;
        }

        // 模型关联
        if (method_exists($this, $name)) {
            $this->$name = $value;

            return;
        }

        if ($this->wei->has($name)) {
            $this->$name = $value;

            return;
        }

        throw new InvalidArgumentException('Invalid property: ' . $name);
    }

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
            return $this->suc(['data' => $this]);
        }
    }

    public function &offsetGet($name)
    {
        $name = $this->filterInputColumn($name);

        parent::offsetGet($name);

        return $this->data[$name];
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function &__get($name)
    {
        // Receive service that conflict with record method name
        if (in_array($name, ['db', 'cache', 'lock', 'ret'])) {
            parent::__get($name);

            return $this->$name;
        }

        // Receive field value
        if ($this->enableProperty) {
            if ($this->hasColumn($name)) {
                $this->get($name);
                $name = $this->filterInputColumn($name);

                return $this->data[$name];
            }
        }

        // Receive relation
        if (method_exists($this, $name)) {
            /** @var BaseModel $related */
            $related = $this->$name();
            $serviceName = $this->getClassServiceName($related);
            $relation = $this->relations[$serviceName];
            $localValue = $this[$relation['localKey']];

            if ($related->isColl()) {
                if ($localValue) {
                    $this->$name = $related->findAll();
                } else {
                    $this->$name = $related;
                }
            } else {
                if ($localValue) {
                    $this->$name = $related->find() ?: null;
                } else {
                    $this->$name = null;
                }
            }

            return $this->$name;
        }

        // Receive service
        return parent::__get($name);
    }
}
