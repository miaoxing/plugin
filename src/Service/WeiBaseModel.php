<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;

/**
 * @internal 逐步完善后移到 Wei 中
 */
abstract class WeiBaseModel extends BaseService implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable
{
    /**
     * @var bool
     */
    protected static $createNewInstance = true;

    protected $createdAtColumn = 'created_at';

    protected $createdByColumn = 'created_by';

    protected $updatedAtColumn = 'updated_at';

    protected $updatedByColumn = 'updated_by';

    /**
     * The name of the table
     *
     * NOTE: Define this property to avoid "Property Invalid" error thrown by the `__set` method
     *
     * @var string
     */
    protected $table;

    /**
     * The column names of the table
     *
     * If leave it blank, it will automatic generate form the database table,
     * or fill it to speed up the record
     *
     * NOTE: Define this property to avoid "Property Invalid" error thrown by the `__set` method
     *
     * @var array
     */
    protected $columns;

    /**
     * The primary key column
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Whether it's a new record and has not save to database
     *
     * @var bool
     */
    protected $new = true;

    /**
     * Whether the data is loaded
     *
     * @var bool
     */
    protected $loaded = false;

    /**
     * The data of model
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The fields that are assignable through fromArray method
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The fields that aren't assignable through fromArray method
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    /**
     * The record data before changed
     *
     * @var array
     */
    protected $changes = [];

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
     * @var array
     */
    protected $relationValues = [];

    /**
     * The value for relation base query
     *
     * @var mixed
     */
    protected $relatedValue;

    /**
     * @var array
     */
    protected $virtual = [];

    /**
     * @var string[]
     */
    protected $hidden = [];

    protected static $booted = [];

    /**
     * The events of model
     *
     * @var array
     */
    protected static $modelEvents = [];

    /**
     * @var array
     */
    protected $requiredServices = [
        'db',
        'cache',
        'logger',
        'ret',
        'str',
    ];

    protected $defaultValues = [
        'date' => '0000-00-00',
        'datetime' => '0000-00-00 00:00:00',
    ];

    /**
     * 数组来源
     *
     * php：数据经过代码处理，例如默认值
     * db：数据来自数据库，是一个未解码/未转换类型的字符串。如果经过处理，则变成php
     * user：数据来自用户设置
     *
     * @var array
     */
    protected $dataSources = [
        '*' => 'php',
    ];

    /**
     * @var array
     */
    protected $virtualAttributes = [];

    /**
     * Returns whether the model was inserted in the this request
     *
     * @var bool
     */
    protected $wasRecentlyCreated = false;

    /**
     * Extra data for saveRelation method
     *
     * @var array
     * @internal may be rename to avoid confuse with relationValues
     */
    protected $relationAttributes = [];
}
