<?php

require 'functions.php';

// 1. 加载配置,初始化服务容器
$dirs = [
    '.',
    'vendor/miaoxing/plugin',
];
foreach ($dirs as $dir) {
    if (is_file($dir . '/tests/bootstrap.php')) {
        require $dir . '/tests/bootstrap.php';
    }
}
$wei = wei();

// 2. 运行全部rollback的SQL
$migrations = $wei->migration->getStatus();
$wei->migration->rollback([
    'target' => $migrations[0]['id']
]);

// 3. 检查数据表
$tables = wei()->db('information_schema.tables')
    ->select('TABLE_NAME')
    ->where([
        'TABLE_TYPE' => 'BASE TABLE',
        'TABLE_SCHEMA' => 'app'
    ])
    ->fetchAll();
if (count($tables) !== 2) {
    // 暂时只剩下apps和user两个表
    err('运行rollback后存在未删除的数据表:' . implode(',', array_column($tables, 'TABLE_NAME')));
} else {
    suc('运行rollback成功');
}

