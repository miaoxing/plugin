<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;
use Miaoxing\Plugin\Seeder\BaseSeeder;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Seeder
 *
 * @mixin \DbMixin
 * @mixin \SchemaMixin
 * @mixin \ClassMapMixin
 */
class Seeder extends BaseService
{
    /**
     * @var array
     */
    protected $paths = [
        'src',
        'plugins/*/src',
    ];

    /**
     * @var string
     */
    protected $defaultPath = 'src/Seeder';

    /**
     * @var string
     */
    protected $defaultNamespace = 'App\Seeder';

    /**
     * @var string
     */
    protected $table = 'seeders';

    /**
     * @var OutputInterface|null
     */
    protected $output;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->prepareTable();
    }

    /**
     * Output the seeder status table
     */
    public function status()
    {
        $status = $this->getStatus();
        if (!$status) {
            $this->writeln('No seeders found.');

            return;
        }

        $this->writeln(' Ran?    Name ');
        $this->writeln('--------------');

        foreach ($status as $row) {
            if ($row['ran']) {
                $mark = '<info>Y</info>';
            } else {
                $mark = '<error>N</error>';
            }
            $this->writeln(' ' . $mark . '       ' . $row['id']);
        }
    }

    /**
     * @return array
     */
    public function getStatus(): array
    {
        $data = [];
        $ranIds = $this->getRanIds();
        $classes = $this->getSeederClasses();

        foreach ($classes as $id => $class) {
            $data[] = [
                'id' => $id,
                'ran' => in_array($id, $ranIds, true),
            ];
        }

        return $data;
    }

    /**
     * @param OutputInterface $output
     * @return $this
     * @svc
     */
    protected function setOutput(OutputInterface $output): self
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @svc
     */
    protected function run(array $options = [])
    {
        if (isset($options['name'])) {
            $this->runOne($options['name']);
            return;
        }

        if (isset($options['from'])) {
            $this->runFrom($options['from']);
            return;
        }

        $classes = $this->getSeederClasses();

        $seeders = $this->db->init($this->table)
            ->desc('id')
            ->indexBy('id')
            ->fetchAll();

        foreach ($seeders as $id => $seeder) {
            if (isset($classes[$id])) {
                unset($classes[$id]);
            }
        }

        if (!$classes) {
            $this->writeln('<info>Nothing to run.</info>');
            return;
        }

        foreach ($classes as $name => $class) {
            $this->runByName($name, $classes);
        }
    }

    protected function runFrom($name)
    {
        $classes = $this->getSeederClasses();

        if ('root' !== $name) {
            if (!isset($classes[$name])) {
                $this->writeln(sprintf('<error>Seeder "%s" not found</error>', $name));
                return;
            }
            $classes = array_slice($classes, array_search($name, array_keys($classes), true));
        }

        foreach ($classes as $name => $class) {
            $this->runByName($name, $classes);
        }
    }

    protected function runOne($name)
    {
        $classes = $this->getSeederClasses();
        $this->runByName($name, $classes);
    }

    protected function runByName($name, $classes)
    {
        if (!isset($classes[$name])) {
            $this->writeln(sprintf('<error>Seeder "%s" not found</error>', $name));
            return;
        }

        $seeder = $this->newInstance($classes[$name]);
        $seeder->run();

        if ($this->db->select($this->table, ['id' => $name])) {
            $this->db->update($this->table, [
                'updated_at' => date('Y-m-d H:i:s'),
            ], ['id' => $name]);
        } else {
            $this->db->insert($this->table, [
                'id' => $name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->writeln('<info>Ran: </info>' . $name);
    }

    protected function getSeederClasses(): array
    {
        return $this->classMap->generate($this->paths, '/Seeder/*.php', 'Seeder', false);
    }

    /**
     * @param array $options
     * @throws \Exception
     * @svc
     */
    protected function create(array $options)
    {
        $class = 'V' . date('YmdHis') . ucfirst($options['name']);
        $path = $options['path'] ?? $this->defaultPath;
        $namespace = $options['namespace'] ?? $this->defaultNamespace;

        if (!$path) {
            $this->writeln('<error>Path should not be empty</error>');
            return;
        }

        $content = $this->generateContent(compact('namespace', 'class'));

        $this->makeDir($path);
        $file = $path . '/' . $class . '.php';
        file_put_contents($file, $content);
        $this->writeln(sprintf('<info>Created the file: %s</info>', $file));
    }

    protected function generateContent($vars): string
    {
        extract($vars);

        ob_start();
        require __DIR__ . '/../Seeder/stubs/seeder.php';
        return ob_get_clean();
    }

    protected function getRanIds(): array
    {
        $seeders = $this->db->init($this->table)
            ->desc('id')
            ->indexBy('id')
            ->fetchAll();

        return array_keys($seeders);
    }

    /**
     * Create a instance from seeder class
     *
     * @param string $class
     * @return BaseSeeder
     */
    protected function newInstance(string $class): BaseSeeder
    {
        return new $class([
            'wei' => $this->wei,
        ]);
    }

    /**
     * Check if table exists, if not exists, create table
     */
    protected function prepareTable()
    {
        if (!$this->schema->hasTable($this->table)) {
            $this->schema->table($this->table)
                ->string('id', 128)
                ->timestamp('created_at')
                ->timestamp('updated_at')
                ->exec();
        }
    }

    protected function writeln($output)
    {
        if ($this->output) {
            $this->output->writeln($output);
        }
    }

    protected function makeDir($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
            chmod($path, 0777);
        }
    }
}
