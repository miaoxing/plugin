<?php

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
$count = wei()->db->count('information_schema.tables', [
    'table_type' => 'BASE TABLE',
    'table_schema' => 'app'
]);
if ($count !== 2) {
    // 暂时只剩下apps和user两个表
    err('运行rollback后存在未删除的数据表');
}

function err($message, $args = null, $args2 = null)
{
    $message = format(func_get_args());
    echo $message . PHP_EOL;

    $dir = 'reports';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $content = $message . str_repeat(PHP_EOL, 2) . str_repeat('=', 70);
    file_put_contents($dir . '/check-migrations.txt', $content);

    return '';
}

function format($args)
{
    $message = $args[0];
    if (isset($args[1])) {
        array_shift($args);
        $message = vsprintf($message, $args);
    }

    return PHP_EOL . $message . PHP_EOL;
}
