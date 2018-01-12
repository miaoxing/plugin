<?php

namespace Miaoxing\Plugin\Controller\Cli;

use Miaoxing\Plugin\BaseController;
use Miaoxing\Plugin\CliDefinition;
use Miaoxing\Plugin\Service\Migration;

/**
 * @property Migration $migration
 */
class Migrations extends BaseController
{
    use CliDefinition;

    public function indexAction()
    {
        return $this->migrateAction();
    }

    public function migrateAction()
    {
        $this->migration->migrate();

        return $this->suc();
    }

    public function rollbackAction($req)
    {
        $this->rollbackDefinition();

        $this->migration->rollback($req);

        return $this->suc();
    }

    public function createAction($req)
    {
        $this->createDefinition();

        return $this->migration->create($req);
    }

    public function statusAction()
    {
        $this->migration->status();

        return $this->suc();
    }

    public function createFromTableAction($req)
    {
        $this->addArgument('table');

        if (!$req['table']) {
            return $this->err('Require table param');
        }

        $code = '$this->schema->table(\'' . $req['table'] . '\')' . "\n";

        $typeMap = [
            'tinyint' => 'tinyInt',
            'bigint' => 'bigInt',
            'varchar' => 'string',
            'mediumtext' => 'mediumText',
        ];
        $defaultLengths = [
            'int' => 11,
            'varchar' => 255,
            'tinyint' => 3,
        ];

        $space = str_repeat(' ', 12);

        $columns = wei()->db->fetchAll('SHOW FULL COLUMNS FROM ' . $req['table']);
        foreach ($columns as $column) {
            if ($column['Field'] === 'id') {
                $code .= $space . '->id()' . "\n";
                continue;
            }

            list($type, $length) = explode('(', $column['Type']);
            $length = rtrim($length, ')');

            $type = explode('(', $column['Type'])[0];
            $method = (isset($typeMap[$type]) ? $typeMap[$type] : $type);

            if (isset($defaultLengths[$type]) && $length != $defaultLengths[$type]) {
                $codeLength = ', ' . $length;
            } else {
                $codeLength = false;
            }

            if ($type == 'decimal') {
                // 忽略第二个,认为总是2
                $length = explode(',', $length)[0];
                $codeLength = ', ' . $length;
            }

            $code .= $space . '->' . $method . '(\'' . $column['Field'] . '\'' . $codeLength . ')';

            if ($column['Comment']) {
                $code .= '->comment(\'' . $column['Comment'] . '\')';
            }

            // 忽略 0 '' 等,会自动加上
            if ($column['Default'] && $column['Default'] !== '0000-00-00 00:00:00') {
                if (strpos($type, 'int') !== false) {
                    $default = $column['Default'];
                } else {
                    $default = '\'' . addcslashes($column['Default'], "\\\$\'\r\n\t\v\f") . '\'';
                }
                $code .= '->defaults(' . $default . ')';
            }

            $code .= "\n";
        }

        $code .= $space . '->exec();' . "\n\n";

        $code .= $space . '$this->schema->dropIfExists(\'' . $req['table'] .'\');' . "\n";

        return $code;
    }

    protected function rollbackDefinition()
    {
        $this->addOption('target', 't');
    }

    protected function createDefinition()
    {
        $this->addArgument('name');
        $this->addOption('path', 'p');
        $this->addOption('plugin-id', 'i');
    }
}
