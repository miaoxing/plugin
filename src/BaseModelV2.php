<?php

namespace Miaoxing\Plugin;

use InvalidArgumentException;
use Miaoxing\Plugin\Model\CamelCaseTrait;
use Miaoxing\Plugin\Model\CastTrait;
use Miaoxing\Plugin\Model\QuickQueryTrait;

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

    public function res(array $data = null)
    {
        $req = $this->request;

        return $this->suc([
            'data' => $data === null ? $this : $data,
            'page' => (int) $req['page'],
            'rows' => $this->getSqlPart('limit'),
            'records' => $this->count(),
        ]);
    }
}
