<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;
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
    public function __construct(array $options)
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
    public function getStatus()
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
    protected function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @svc
     */
    protected function run()
    {
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

        foreach ($classes as $id => $class) {
            $seeder = $this->instance($classes[$id]);
            $seeder->run();

            $this->db->insert($this->table, [
                'id' => $id,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $this->writeln('<info>Ran: </info>' . $id);
        }
    }

    protected function getSeederClasses()
    {
        return $this->classMap->generate($this->paths, '/Seeder/*.php', 'Seeder', false);
    }

    /**
     * @param array $options
     * @throws \ReflectionException
     * @throws \Exception
     * @svc
     */
    protected function create($options)
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

    protected function generateContent($vars)
    {
        extract($vars);

        ob_start();
        require __DIR__ . '/../Seeder/stubs/seeder.php';
        return ob_get_clean();
    }

    protected function getRanIds()
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
        if (!$this->schema->hasTable($this->table)) {
            $this->schema->table($this->table)
                ->string('id', 128)
                ->timestamp('created_at')
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
