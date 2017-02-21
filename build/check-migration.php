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

// 2. 先清空数据表,确保不会受Data truncated for column之类的影响
$tables = getTables();
foreach ($tables as $table) {
    if ($table == 'migrations') {
        continue;
    }
    wei()->db->query("TRUNCATE TABLE app." . $table['TABLE_NAME']);
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
$tables = getTables();
if (count($tables) !== 2) {
    // 暂时只剩下apps和user两个表
    err('运行rollback后存在未删除的数据表:' . implode(',', array_column($tables, 'TABLE_NAME')));
} else {
    suc('运行rollback成功');
}
