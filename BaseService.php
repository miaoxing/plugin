<?php

namespace miaoxing\plugin {

    use Miaoxing\Region\Service\BaiduApi;
    use Miaoxing\App\Service\Coll;
    use services\CsvExporter;
    use Miaoxing\Excel\Service\Excel;
    use Miaoxing\Region\Service\Lbs;
    use Miaoxing\Plugin\Service\Migration;
    use Miaoxing\Queue\Service\RedisQueue;
    use services\SafeUrl;
    use services\Xml;

    /**
     * @property    \services\Db $appDb
     * @property    \Wei\Event $event
     * @method      \miaoxing\plugin\BaseModel appDb($table = null)
     * @method      \miaoxing\plugin\BaseModel db($table = null)
     * @property    \Miaoxing\Plugin\Service\App $app 应用管理服务
     * @property    CsvExporter $csvExporter Csv格式数据导出服务
     * @property    SafeUrl $safeUrl 生成带签名的安全URL
     * @property    \Miaoxing\Plugin\Service\Plugin $plugin 插件管理器
     * @property    Excel $excel Excel操作的快捷服务
     * @property    \Miaoxing\Plugin\Service\\Miaoxing\Stat\Service\Chart $chart 构造图表数据的辅助服务
     * @property    Lbs $lbs 基于位置的服务
     * @property    BaiduApi $baiduApi 百度Web服务API
     * @property    \Miaoxing\Queue\Service\BaseQueue $queue 队列服务
     * @property    \Miaoxing\Queue\Service\SyncQueue $syncQueue 同步的队列服务
     * @property    RedisQueue $redisQueue 基于Redis的队列服务
     * @property    \Miaoxing\Queue\Service\QueueWorker $queueWorker 队列后台服务
     * @property    Xml $xml XML服务
     * @property    \Miaoxing\App\Service\Random $random 随机数服务
     * @property    Coll $coll 组合服务
     * @property    Migration $migration 迁移服务
     */
    class BaseService extends \Wei\Base
    {
        /**
         * Returns current class name
         *
         * 兼容5.5之前版本获取类名的方法
         *
         * @return string
         */
        public static function className()
        {
            return get_called_class();
        }
    }
}

namespace {

    if (!function_exists('wei')) {
        /**
         * @return \miaoxing\plugin\BaseService
         */
        function wei()
        {
        }
    }
}
