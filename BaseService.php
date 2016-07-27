<?php

namespace miaoxing\plugin
{

    use miaoxing\plugin\services\App;
    use services\BaiduApi;
    use services\BaseQueue;
    use services\Chart;
    use services\Coll;
    use services\CsvExporter;
    use services\DataProvider;
    use services\dataProviders;
    use services\Excel;
    use services\Handlebars;
    use services\Html;
    use services\Lbs;
    use services\QueueWorker;
    use services\Random;
    use services\RedisQueue;
    use services\SafeUrl;
    use services\SinaApi;
    use services\StatsD;
    use services\SyncQueue;
    use services\TaobaoApi;
    use services\Xml;

    /**
     * @property    \Wei\Db $appDb
     * @property    \Wei\Event $event
     * @method      \miaoxing\plugin\BaseModel appDb($table = null)
     * @method      \miaoxing\plugin\BaseModel db($table = null)
     * @property    \Wei\Logger $syncLogger 同步用户的日志
     * @property    \Wei\Logger $tmpLogger 临时日志,记录在tmp.log文件,用于调试等
     * @property    App $app 应用管理服务
     * @property    CsvExporter $csvExporter Csv格式数据导出服务
     * @property    SafeUrl $safeUrl 生成带签名的安全URL
     * @property    \miaoxing\plugin\services\Plugin $plugin 插件管理器
     * @property    Handlebars $handlebars Handlebars模板引擎
     * @property    Excel $excel Excel操作的快捷服务
     * @property    Chart $chart 构造图表数据的辅助服务
     * @property    Lbs $lbs 基于位置的服务
     * @property    BaiduApi $baiduApi 百度Web服务API
     * @property    SinaApi $sinaApi 新浪Web服务API
     * @property    TaobaoApi $taobaoApi 淘宝Web服务API
     * @property    StatsD $statsD Sends statistics to the stats daemon over UDP
     * @property    BaseQueue $queue 队列服务
     * @property    SyncQueue $syncQueue 同步的队列服务
     * @property    RedisQueue $redisQueue 基于Redis的队列服务
     * @property    QueueWorker $queueWorker 队列后台服务
     * @property    \plugins\file\services\Cdn $cdn CDN服务
     * @property    Xml $xml XML服务
     * @property    Html $html HTML服务
     * @property    Random $random 随机数服务
     * @property    Coll $coll 组合服务
     * @property    DataProvider $dataProvider 创建新项目时,提供数据源的对象
     * @method      dataProviders\Base dataProvider($type = 'common') 初始化数据源对象
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

namespace
{
    if (!function_exists('wei')) {
        /**
         * @return \miaoxing\plugin\BaseService
         */
        function wei()
        {
        }
    }
}
