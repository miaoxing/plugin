<?php

require 'functions.php';

// 1. 加载配置,初始化服务容器
$dirs = [
    '.',
    'vendor/miaoxing/plugin',
];
foreach ($dirs as $dir) {
    if (is_file($dir . '/tests/init.php')) {
        require $dir . '/tests/init.php';
    }
}
$wei = wei();

// 2. 先清空数据表,确保不会受Data truncated for column之类的影响
$tables = getTables();
foreach ($tables as $table) {
    if ($table['TABLE_NAME'] == 'migrations') {
        continue;
    }
    $wei->db->query("TRUNCATE TABLE app." . $table['TABLE_NAME']);
}

// 3. 运行全部rollback的SQL
$migrations = $wei->migration->getStatus();
try {
    $wei->migration->rollback([
        'target' => $migrations[0]['id']
    ]);
} catch (\Exception $e) {
    return err((string) $e);
}

// 4. 检查数据表
$allowTables = ['apps', 'migrations', 'user'];
$leftTables = array_column(getTables(), 'TABLE_NAME');
$leftTables = array_diff($leftTables, $allowTables);
if ($leftTables) {
    err('运行rollback后存在未删除的数据表:' . implode(',', $leftTables));
} else {
    suc('运行rollback成功');
}
