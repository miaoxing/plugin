<?php

namespace miaoxing\plugin;

use JsonSerializable;
use Wei\Logger;
use Wei\Record;
use Wei\RetTrait;

/**
 * @method \miaoxing\plugin\BaseModel db($table = null) Create a new record object
 * @property \Wei\BaseCache $cache
 * @property \Miaoxing\Plugin\Service\Plugin $plugin
 * @property Logger $logger
 */
class BaseModel extends Record implements JsonSerializable
{
    use RetTrait;

    protected $guarded = [
        //'id', 需要区分,象skuConfig表就要从外部设置id
        'appId',
        'createTime',
        'createUser',
        'updateTime',
        'updateUser',
    ];

    /**
     * 插入时是否自动生成递增的数字编号
     *
     * @var bool
     */
    protected $autoId = false;

    /**
     * 是否启用垃圾箱,启用后,删除的记录将会被转移到trash表
     *
     * @var bool
     */
    protected $enableTrash = false;

    /**
     * {@inheritdoc}
     */
    protected $defaultCacheTime = 1800;

    protected $appIdColumn = 'appId';

    protected $createAtColumn = 'createTime';

    protected $updateAtColumn = 'updateTime';

    protected $createdByColumn = 'createUser';

    protected $updatedByColumn = 'updateUser';

    protected $deletedAtColumn = 'deleteTime';

    protected $deletedByColumn = 'deleteUser';

    protected $userIdColumn = 'userId';

    /**
     * The relation configs
     *
     * @var array
     */
    protected $relations = [];

    /**
     * The relations have been loaded
     *
     * @var array
     */
    protected $loadedRelations = [];

    /**
     * The value for relation base query
     *
     * @var mixed
     */
    protected $relationValue;

    /**
     * @return BaseModel|BaseModel[]
     */
    public function __invoke()
    {
        if (!$this->table) {
            $this->detectTable();
        }
        $this->db->addRecordClass($this->table, get_class($this));

        return $this->db($this->table);
    }

    public function beforeCreate()
    {
        if ($this->autoId && !$this['id']) {
            $this['id'] = wei()->seq();
        }

        $fields = $this->getFields();

        if (in_array($this->createAtColumn, $fields) && !$this[$this->createAtColumn]) {
            $this[$this->createAtColumn] = date('Y-m-d H:i:s');
        }

        if (in_array($this->createdByColumn, $fields) && !$this[$this->createdByColumn]) {
            $this[$this->createdByColumn] = (int) wei()->curUser['id'];
        }
    }

    public function beforeSave()
    {
        $fields = $this->getFields();

        if (in_array($this->updateAtColumn, $fields)) {
            $this[$this->updateAtColumn] = date('Y-m-d H:i:s');
        }

        if (in_array($this->updatedByColumn, $fields)) {
            $this[$this->updatedByColumn] = (int) wei()->curUser['id'];
        }
    }

    public function beforeDestroy()
    {
        if ($this->enableTrash) {
            $this->db->insert('trash', [
                'tableName' => $this->fullTable,
                'data' => json_encode($this->data),
                'deleteTime' => date('Y-m-d H:i:s'),
                'deleteUser' => (int) wei()->curUser['id'],
            ]);
        }
    }

    /**
     * QueryBuilder: 筛选属于当前登录用户的记录
     *
     * @return $this
     */
    public function mine()
    {
        return $this->andWhere([$this->userIdColumn => wei()->curUser['id']]);
    }

    /**
     * QueryBuilder: 筛选属于当前应用的数据
     *
     * @return $this
     */
    public function curApp()
    {
        $appId = wei()->app->getId();
        // 设置默认数据,初始化时就会带上
        $this->data[$this->appIdColumn] = $appId;

        return $this->andWhere([$this->appIdColumn => $appId]);
    }

    /**
     * Record: 设置当前应用ID
     *
     * @param int $appId
     * @return $this
     */
    public function setAppId($appId = null)
    {
        return $this->set($this->appIdColumn, $appId ?: wei()->app->getId());
    }

    /**
     * QueryBuilder: 筛选未删除的数据
     *
     * @return $this
     */
    public function notDeleted()
    {
        return $this->andWhere([$this->fullTable . '.' . $this->deletedAtColumn => '0000-00-00 00:00:00']);
    }

    /**
     * QueryBuilder: 筛选已删除的数据
     *
     * @return $this
     */
    public function deleted()
    {
        return $this->andWhere($this->fullTable . '.' . $this->deletedAtColumn . " != '0000-00-00 00:00:00'");
    }

    /**
     * QueryBuilder: 筛选启用的数据
     *
     * @return $this
     */
    public function enabled()
    {
        return $this->andWhere(['enable' => true]);
    }

    /**
     * @return string
     * @todo 移到视图hepler?
     */
    public function getFormAction()
    {
        return $this->isNew ? 'create' : 'update';
    }

    public function getHttpMethod()
    {
        return $this->isNew ? 'POST' : 'PUT';
    }

    public function beColl()
    {
        $this->data = [];
        $this->isColl = true;

        return $this;
    }

    /**
     * 软删除
     *
     * @return $this
     */
    public function softDelete()
    {
        return $this->save([
            $this->deletedAtColumn => date('Y-m-d H:i:s'),
            $this->deletedByColumn => (int) wei()->curUser['id'],
        ]);
    }

    /**
     * Record: 检查该记录是否已经被删除了
     *
     * @return bool
     */
    public function isSoftDeleted()
    {
        return $this[$this->deletedAtColumn] && $this[$this->deletedAtColumn] != '0000-00-00 00:00:00';
    }

    /**
     * 不经过fillable检查,设置数据并保存
     *
     * @param array $data
     * @return $this
     */
    public function saveData($data = [])
    {
        $data && $this->setData($data);

        return $this->save();
    }

    /**
     * 如果提供了ID,检查该ID的数据是否存在,如果不存在则抛出404异常
     * 即FindOneOrInitById的缩写
     *
     * @param mixed $id
     * @param array|\ArrayAccess $data
     * @return $this
     * @todo 和已有的find方法可能会混淆,是否要改为seek或lookup
     */
    public function findId($id, $data = [])
    {
        if ($id) {
            $this->findOneById($id);
        } else {
            // Reset status when record not found
            $this->isNew = true;
        }

        return $this->fromArray($data);
    }

    public function findAllByIds($ids)
    {
        if (!$ids) {
            return $this->beColl();
        }

        return $this->findAll(['id' => $ids]);
    }

    /**
     * 设置缓存的标签为当前表名+用户ID
     *
     * @return $this
     */
    public function tagByUser()
    {
        return $this->tags($this->getUserTag());
    }

    /**
     * Record: 清除当前记录的缓存
     *
     * @return $this
     */
    public function clearRecordCache()
    {
        if ($this['id']) {
            $this->cache->remove($this->getRecordCacheKey());
        }

        return $this;
    }

    /**
     * Record: 获取当前记录的缓存键名
     *
     * @param int|null $id
     * @return string
     */
    public function getRecordCacheKey($id = null)
    {
        return $this->db->getDbname() . ':' . $this->table . ':' . ($id ?: $this['id']);
    }

    /**
     * @return $this
     */
    public function clearTagCacheByUser()
    {
        $tag = $this->getUserTag();
        $this->tagCache($tag)->clear();

        return $this;
    }

    /**
     * @return string
     */
    public function getUserTag()
    {
        return $this->table . ':' . ($this['userId'] ?: wei()->curUser['id']);
    }

    /**
     * 获取包含数据库名词的数据表,如app.user,方便跨库查询
     *
     * @param string $table
     * @return string
     */
    public function getDbTable($table)
    {
        return wei()->app->getDbName($this[$this->appIdColumn]) . '.' . $table;
    }

    /**
     * 将类名的最后一段作为数据表名称
     */
    protected function detectTable()
    {
        if (!$this->table) {
            // 适合类名:plugins\editor\services\Page
            $parts = explode('\\', get_class($this));
            $this->table = lcfirst(end($parts));
        }
    }

    /**
     * Returns the record data
     *
     * @return $this[]|array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Reset all SQL parts and parameters
     *
     * @return $this
     */
    public function resetQuery()
    {
        $this->params = [];
        $this->paramTypes = [];

        return $this->resetSqlParts();
    }

    /**
     * Specifies an item that is not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param string|array $fields
     * @return $this
     */
    public function selectExcept($fields)
    {
        $fields = array_diff($this->getFields(), is_array($fields) ? $fields : [$fields]);

        return $this->select($fields);
    }

    /**
     * {@inheritdoc}
     */
    protected function loadData($offset)
    {
        if (!$this->loaded && !$this->isNew) {
            if ($this->table !== 'user') {
                wei()->statsD->increment('record.loadData.' . $this->table);
            }
        }
        parent::loadData($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param \Closure $fn
     * @deprecated 使用filter
     */
    public function filterDeprecated(\Closure $fn)
    {
        $this->data = array_filter($this->data, $fn);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($returnFields = array())
    {
        if (!$this->isLoaded()) {
            $this->loadData($this->isColl() ? 0 : 'id');
        }

        return parent::toArray($returnFields);
    }

    /**
     * @param string $record
     * @param string|null $foreignKey
     * @param string|null $localKey
     * @return $this
     */
    public function hasOne($record, $foreignKey = null, $localKey = null)
    {
        /** @var BaseModel $related */
        $related = $this->wei->$record();

        $localKey || $localKey = $this->getPrimaryKey();
        $foreignKey || $foreignKey = $this->getForeignKey();
        $this->relations[$record] = ['foreignKey' => $foreignKey, 'localKey' => $localKey];

        $related->where([$foreignKey => $this->getRelationValue($localKey)]);

        return $related;
    }

    /**
     * @param string $record
     * @param string|null $foreignKey
     * @param string|null $localKey
     * @return $this
     */
    public function hasMany($record, $foreignKey = null, $localKey = null)
    {
        return $this->hasOne($record, $foreignKey, $localKey)->beColl();
    }

    /**
     * @param string $record
     * @param string|null $foreignKey
     * @param string|null $localKey
     * @return BaseModel
     */
    public function belongsTo($record, $foreignKey = null, $localKey = null)
    {
        $foreignKey || $foreignKey = $this->getPrimaryKey();
        $localKey || $localKey = $this->snake($record) . '_' . $this->getPrimaryKey();

        return $this->hasOne($record, $foreignKey, $localKey);
    }

    /**
     * @param string $record
     * @param string|null $junctionTable
     * @param string|null $foreignKey
     * @param string|null $relatedKey
     * @return BaseModel
     */
    public function belongsToMany($record, $junctionTable = null, $foreignKey = null, $relatedKey = null)
    {
        /** @var BaseModel $related */
        $related = $this->wei->$record();

        $primaryKey = $this->getPrimaryKey();
        $junctionTable || $junctionTable = $this->getJunctionTable($related);
        $foreignKey || $foreignKey = $this->getForeignKey();
        $relatedKey || $relatedKey = $this->snake($record) . '_' . $primaryKey;
        $this->relations[$record] = [
            'junctionTable' => $junctionTable,
            'relatedKey' => $relatedKey,
            'foreignKey' => $foreignKey,
            'localKey' => $primaryKey,
        ];

        $relatedTable = $related->getTable();
        $related->select($relatedTable . '.*')
            ->where([$junctionTable . '.' . $foreignKey => $this->getRelationValue($primaryKey)])
            ->innerJoin(
                $junctionTable,
                sprintf('%s.%s = %s.%s', $junctionTable, $relatedKey, $relatedTable, $primaryKey)
            )
            ->beColl();

        return $related;
    }

    public function load($names)
    {
        foreach ((array) $names as $name) {
            // 1. Load relation config
            list($name, $next) = explode('.', $name, 2);
            if (isset($this->loadedRelations[$name])) {
                continue;
            }

            /** @var BaseModel $related */
            $related = $this->$name();
            $isColl = $related->isColl();
            $serviceName = $this->getClassServiceName($related);
            $relation = $this->relations[$serviceName];

            // 2. Fetch relation record data
            $ids = $this->getAll($relation['localKey']);
            $ids = array_unique(array_filter($ids));
            if ($ids) {
                $this->relationValue = $ids;
                $related = $this->$name();
                $this->relationValue = null;
            } else {
                $related = null;
            }

            // 3. Load relation data
            if (isset($relation['junctionTable'])) {
                $records = $this->loadBelongsToMany($related, $relation, $name);
            } else if ($isColl) {
                $records = $this->loadHasMany($related, $relation, $name);
            } else {
                $records = $this->loadHasOne($related, $relation, $name);
            }

            // 4. Load nested relations
            if ($next && $records) {
                $records->load($next);
            }

            $this->loadedRelations[$name] = true;
        }

        return $this;
    }

    protected function loadHasOne(Record $related = null, $relation, $name)
    {
        if ($related) {
            $records = $related->findAll()->indexBy($relation['foreignKey']);
        } else {
            $records = [];
        }
        foreach ($this->data as $row) {
            $row->$name = isset($records[$row[$relation['localKey']]]) ? $records[$row[$relation['localKey']]] : null;
        }

        return $records;
    }

    protected function loadHasMany(Record $related = null, $relation, $name)
    {
        $serviceName = $this->getClassServiceName($related);
        $records = $related ? $related->fetchAll() : [];
        foreach ($this->data as $row) {
            $rowRelation = $row->$name = $this->wei->$serviceName()->beColl();
            foreach ($records as $record) {
                if ($record[$relation['foreignKey']] == $row[$relation['localKey']]) {
                    $rowRelation[] = $this->wei->$serviceName()->fromArray($record);
                }
            }
        }

        return $records;
    }

    protected function loadBelongsToMany(Record $related = null, $relation, $name)
    {
        if ($related) {
            $related->addSelect($relation['junctionTable'] . '.' . $relation['foreignKey']);
        }

        return $this->loadHasMany($related, $relation, $name);
    }

    public function __get($name)
    {
        // Receive service that conflict with record method name
        if (in_array($name, ['cache', 'lock'])) {
            return parent::__get($name);
        }

        // Receive field value
        if (array_key_exists($name, $this->data)) {
            $this->logger->info(sprintf('Field "%s" conflicts with service name', $name));
//            return $this->get($name);
        }

        // Receive relation
        if (method_exists($this, $name)) {
            /** @var BaseModel $related */
            $related = $this->$name();
            $serviceName = $this->getClassServiceName($related);
            $relation = $this->relations[$serviceName];
            if (!$this[$relation['localKey']]) {
                return $this->$name = null;
            }

            if ($related->isColl()) {
                return $this->$name = $related->findAll();
            }

            return $this->$name = $related->find() ?: null;
        }

        // Receive service
        return parent::__get($name);
    }

    protected function snake($input)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    protected function getClassServiceName($object)
    {
        return lcfirst(end(explode('\\', get_class($object))));
    }

    protected function getForeignKey()
    {
        return $this->snake($this->getClassServiceName($this)) . '_' . $this->getPrimaryKey();
    }

    protected function getJunctionTable(BaseModel $related)
    {
        $tables = [$this->getTable(), $related->getTable()];
        sort($tables);

        return implode('_', $tables);
    }

    protected function getRelationValue($field)
    {
        return $this->relationValue ?: $this[$field];
    }
}
