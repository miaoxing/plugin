<?php

namespace Miaoxing\Plugin\Service;

/**
 * @property \Wei\Request $request
 */
class CliApp extends \miaoxing\plugin\BaseService
{
    /**
     * 默认的地址,如http://example.com
     *
     * CLI下环境变量没有host参数,需配置地址才可生成正确的URL,
     * 用于发模板消息,邮件等包含地址的场景.
     *
     * @var string
     */
    protected $defaultUrl;

    /**
     * {@inheritdoc}
     */
    public function __construct($options = [])
    {
        parent::__construct($options);

        if (isset($_SERVER['argv'][1])) {
            $this->parseUri($_SERVER['argv'][1]);
        }

        if ($_SERVER['argv']) {
            $this->parseArgs($_SERVER['argv']);
        }
    }

    /**
     * 转换URI为Request的属性
     *
     * @param string $uri
     * @link https://github.com/symfony/http-foundation/blob/master/Request.php
     */
    protected function parseUri($uri)
    {
        $server = [
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => 80,
            'HTTP_HOST' => 'localhost',
            'HTTP_USER_AGENT' => 'Wei/0.9.X',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5',
            'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'REMOTE_ADDR' => '127.0.0.1',
            'SERVER_ADDR' => '127.0.0.1',
            'SCRIPT_NAME' => '',
            'SCRIPT_FILENAME' => '/index.php',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'PHP_SELF' => '/index.php',
            'REQUEST_TIME' => time(),
        ];

        $components = parse_url($uri);
        if (!isset($components['host']) && $this->defaultUrl) {
            $components['path'] = '/' . $components['path'];
            $components += parse_url($this->defaultUrl);
        }

        if (isset($components['host'])) {
            $server['SERVER_NAME'] = $components['host'];
            $server['HTTP_HOST'] = $components['host'];
        }

        if (isset($components['scheme'])) {
            if ('https' === $components['scheme']) {
                $server['HTTPS'] = 'on';
                $server['SERVER_PORT'] = 443;
            } else {
                unset($server['HTTPS']);
                $server['SERVER_PORT'] = 80;
            }
        }

        if (isset($components['port'])) {
            $server['SERVER_PORT'] = $components['port'];
            $server['HTTP_HOST'] = $server['HTTP_HOST'] . ':' . $components['port'];
        }

        if (isset($components['user'])) {
            $server['PHP_AUTH_USER'] = $components['user'];
        }

        if (isset($components['pass'])) {
            $server['PHP_AUTH_PW'] = $components['pass'];
        }

        if (!isset($components['path'])) {
            $components['path'] = '/';
        }

        $query = [];

        $queryString = '';
        if (isset($components['query'])) {
            parse_str(html_entity_decode($components['query']), $qs);
            if ($query) {
                $query = array_replace($qs, $query);
                $queryString = http_build_query($query, '', '&');
            } else {
                $query = $qs;
                $queryString = $components['query'];
            }
        } elseif ($query) {
            $queryString = http_build_query($query, '', '&');
        }
        $server['REQUEST_URI'] = $components['path'] . ('' !== $queryString ? '?' . $queryString : '');
        $server['QUERY_STRING'] = $queryString;

        // 设置Request对象属性
        $this->request->setOption('data', $query);
        $this->request->setOption('servers', $server + $this->request->getOption('servers'));
    }

    /**
     * @param array $argv
     * @link https://gist.github.com/magnetik/2959619
     */
    protected function parseArgs(array $argv)
    {
        $out = array();
        $skip = array();

        foreach ($argv as $idx => $arg) {
            if (in_array($idx, $skip)) {
                continue;
            }

            $arg = preg_replace('#\s*\=\s*#si', '=', $arg);
            $arg = preg_replace('#(--+[\w-]+)\s+[^=]#si', '${1}=', $arg);

            if (substr($arg, 0, 2) == '--') {
                $eqPos = strpos($arg, '=');

                if ($eqPos === false) {
                    $key = trim($arg, '- ');
                    //$val = true;//isset($out[$key]);

                    // We handle case: --user-id 123 -> this is a long option with a value passed.
                    // the actual value comes as the next element from the array.
                    // We check if the next element from the array is not an option.
                    if (isset($argv[$idx + 1]) && !preg_match('#^-#si', $argv[$idx + 1])) {
                        $out[$key] = trim($argv[$idx + 1]);
                        $skip[] = $idx;
                        $skip[] = $idx + 1;
                        continue;
                    }

                    $out[$key] = true;
                } else {
                    $key = substr($arg, 2, $eqPos - 2);
                    $out[$key] = substr($arg, $eqPos + 1);
                }
            } else {
                if (substr($arg, 0, 1) == '-') {
                    if (substr($arg, 2, 1) == '=') {
                        $key = substr($arg, 1, 1);
                        $out[$key] = substr($arg, 3);
                    } else {
                        $chars = str_split(substr($arg, 1));

                        foreach ($chars as $char) {
                            $key = $char;
                            $out[$key] = isset($out[$key]) ? $out[$key] : true;
                        }
                    }
                } else {
                    $out[] = $arg;
                }
            }
        }

        $this->request->set($out);
    }
}
