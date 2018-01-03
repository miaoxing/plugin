<?php

namespace miaoxing\plugin {

    use Miaoxing\Region\Service\BaiduApi;
    use Miaoxing\App\Service\Coll;
    use Miaoxing\Excel\Service\Excel;
    use Miaoxing\Region\Service\Lbs;
    use Miaoxing\Plugin\Service\Migration;
    use Miaoxing\Queue\Service\RedisQueue;

    /**
     * @property    \Miaoxing\Plugin\Service\Db $appDb
     * @property    \Wei\Event $event
     * @method      \miaoxing\plugin\BaseModel appDb($table = null)
     * @method      \miaoxing\plugin\BaseModel db($table = null)
     * @property    \Miaoxing\Plugin\Service\App $app 应用管理服务
     * @property    \Miaoxing\Plugin\Service\Plugin $plugin 插件管理器
     * @property    Excel $excel Excel操作的快捷服务
     * @property    \Miaoxing\Plugin\Service\\Miaoxing\Stat\Service\Chart $chart 构造图表数据的辅助服务
     * @property    Lbs $lbs 基于位置的服务
     * @property    BaiduApi $baiduApi 百度Web服务API
     * @property    \Miaoxing\Queue\Service\BaseQueue $queue 队列服务
     * @property    \Miaoxing\Queue\Service\SyncQueue $syncQueue 同步的队列服务
     * @property    RedisQueue $redisQueue 基于Redis的队列服务
     * @property    \Miaoxing\Queue\Service\QueueWorker $queueWorker 队列后台服务
     * @property    \Miaoxing\App\Service\Random $random 随机数服务
     * @property    Coll $coll 组合服务
     * @property    Migration $migration 迁移服务
     */
    class BaseService extends \Wei\Base
    {
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
