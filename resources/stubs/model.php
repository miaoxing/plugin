<?= '<?php', "\n" ?>

namespace <?= $namespace ?>;

use miaoxing\plugin\BaseModel;

/**
 * <?= $class, "\n" ?>
 */
class <?= $class ?> extends BaseModel
{
    protected $table = '<?= $table ?>';

    protected $providers = [
        'db' => 'app.db',
    ];

    protected $appIdColumn = 'app_id';

    protected $userIdColumn = 'user_id';

    protected $createdAtColumn = 'created_at';

    protected $updatedAtColumn = 'updated_at';

    protected $createdByColumn = 'created_by';

    protected $updatedByColumn = 'updated_by';

    protected $deletedAtColumn = 'deleted_at';

    protected $deletedByColumn = 'deleted_by';
}
