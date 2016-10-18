<?php

namespace miaoxing\plugin;

use JsonSerializable;
use Wei\Record;
use Wei\RetTrait;

/**
 * @method \miaoxing\plugin\BaseModel db($table = null) Create a new record object
 * @property \Wei\BaseCache $cache
 * @property \miaoxing\plugin\services\Plugin $plugin
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

        if (in_array('createTime', $this->getFields()) && !$this['createTime']) {
            $this['createTime'] = date('Y-m-d H:i:s');
        }

        if (in_array('createUser', $this->getFields()) && !$this['createUser']) {
            $this['createUser'] = (int) wei()->curUser['id'];
        }
    }

    public function beforeSave()
    {
        if (in_array('updateTime', $this->getFields())) {
            $this['updateTime'] = date('Y-m-d H:i:s');
        }

        if (in_array('updateUser', $this->getFields())) {
            $this['updateUser'] = (int) wei()->curUser['id'];
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
        return $this->andWhere(['userId' => wei()->curUser['id']]);
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
        $this->data['appId'] = $appId;

        return $this->andWhere(['appId' => $appId]);
    }

    /**
     * Record: 设置当前应用ID
     *
     * @param int $appId
     * @return $this
     */
    public function setAppId($appId = null)
    {
        return $this->set('appId', $appId ?: wei()->app->getId());
    }

    /**
     * QueryBuilder: 筛选未删除的数据
     *
     * @return $this
     */
    public function notDeleted()
    {
        return $this->andWhere([$this->fullTable . '.deleteTime' => '0000-00-00 00:00:00']);
    }

    /**
     * QueryBuilder: 筛选已删除的数据
     *
     * @return $this
     */
    public function deleted()
    {
        return $this->andWhere($this->fullTable . ".deleteTime != '0000-00-00 00:00:00'");
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
            'deleteTime' => date('Y-m-d H:i:s'),
            'deleteUser' => (int) wei()->curUser['id'],
        ]);
    }

    /**
     * Record: 检查该记录是否已经被删除了
     *
     * @return bool
     */
    public function isSoftDeleted()
    {
        return $this['deleteTime'] && $this['deleteTime'] != '0000-00-00 00:00:00';
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
        return wei()->app->getDbName($this['appId']) . '.' . $table;
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
}
