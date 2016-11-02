<?php

namespace Miaoxing\Plugin\Service;

use miaoxing\plugin\BaseService;
use Wei\Db;
use Wei\RetTrait;

/**
 * @property Db $db
 * @property Scheme $scheme
 * @property Cli $cli
 */
class Migration extends BaseService
{
    use RetTrait;

    /**
     * @var array
     */
    protected $paths = [
        '.',
        'plugins/*',
        'vendor/*/*/src',
    ];

    /**
     * @var string
     */
    protected $table = 'migrations';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options)
    {
        parent::__construct($options);
        $this->prepareTable();
    }

    public function migrate()
    {
        $classes = $this->getMigrationClasses();

        $migrations = $this->db($this->table)
            ->desc('id')
            ->indexBy('id')
            ->fetchAll();

        foreach ($migrations as $id => $migration) {
            if (isset($classes[$id])) {
                unset($classes[$id]);
            }
        }

        if (!$classes) {
            $this->writeln($this->cli->success('Nothing to migrate.'));

            return;
        }

        foreach ($classes as $id => $class) {
            $migration = $this->instance($classes[$id]);
            $migration->up();

            $this->db->insert($this->table, [
                'id' => $id,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $this->writeln($this->cli->success('Migrated: ') . $id);
        }
    }

    protected function getMigrationClasses()
    {
        $classMap = $this->plugin->generateClassMap($this->paths, '/Migration/*.php', 'Migration');

        return $classMap;
    }

    public function rollback($req)
    {
        $cli = $this->cli;
        $classes = $this->getMigrationClasses();
        $migrationIds = $this->getMigratedIds();

        if ($req['target']) {
            $index = array_search($req['target'], $migrationIds, true);
            if (false === $index) {
                $this->writeln($cli->error(sprintf('Target "%s" not found', $req['target'])));

                return;
            }

            // Return migrations included target
            $migrationIds = array_slice($migrationIds, 0, $index + 1, true);
        } else {
            $migrationIds = [current($migrationIds)];
        }

        if (!$migrationIds) {
            $this->writeln($cli->success('Nothing to rollback.'));
            return;
        }

        foreach ($migrationIds as $id) {
            if (isset($classes[$id])) {
                $migration = $this->instance($classes[$id]);
                $migration->down();
            } else {
                $this->writeln(sprintf($cli->error('Missing migration "%s"'), $id));
            }

            $this->db->delete($this->table, ['id' => $id]);
            $this->writeln($cli->success('Rolled back: ') . $id);
        }
    }

    public function create($req)
    {
        $class = 'V' . date('YmdHis') . $req['name'];
        $path = $req['path'] . '/' . $class . '.php';

        if (!is_dir($req['path'])) {
            mkdir($req['path'], 0777, true);
            chmod($req['path'], 0777);
        }

        ob_start();
        require  'vendor/miaoxing/plugin/resources/stubs/migration.php';
        $content = ob_get_clean();

        file_put_contents($path, $content);
        chmod($path, 0777);

        return $this->suc();
    }

    /**
     * Output the migration status table
     */
    public function status()
    {
        $status = $this->getStatus();
        if (!$status) {
            $this->writeln('No migrations found.');
            return;
        }

        $this->writeln(' Ran?    Name ');
        $this->writeln('--------------');

        foreach ($status as $row) {
            if ($row['migrated']) {
                $mark = $this->cli->success('Y');
            } else {
                $mark = $this->cli->error('N');
            }
            $this->writeln(' ' . $mark . '       ' . $row['id']);
        }
    }

    /**
     * @return array
     */
    public function getStatus()
    {
        $data = [];
        $migratedIds = $this->getMigratedIds();
        $classes = $this->getMigrationClasses();

        foreach ($classes as $id => $class) {
            $data[] = [
                'id' => $id,
                'migrated' => in_array($id, $migratedIds),
            ];
        }

        return $data;
    }

    protected function getMigratedIds()
    {
        $migrations = $this->db($this->table)
            ->desc('id')
            ->indexBy('id')
            ->fetchAll();

        return array_keys($migrations);
    }

    /**
     * Create a instance from migration class
     *
     * @param string $class
     * @return BaseService
     */
    protected function instance($class)
    {
        $object = new $class([
            'wei' => $this->wei,
        ]);

        return $object;
    }

    /**
     * Check if table exists, if not exists, create table
     */
    protected function prepareTable()
    {
        if (!$this->scheme->hasTable($this->table)) {
            $this->scheme->table($this->table)
                ->string('id', 128)
                ->timestamp('created_at')
                ->exec();
        }
    }

    /**
     * @param string $message
     */
    protected function writeln($message)
    {
        echo $message . "\n";
    }
}
