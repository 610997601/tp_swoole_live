<?php

use think\Container;

class Http
{
    const HOST = '127.0.0.1';
    const PORT = 8811;

    public $http = null;

    public function  __construct()
    {
        $this->http = new swoole_http_server(self::HOST, self::PORT);
        $this->http->set([
            'enable_static_handler' => true,
            'document_root' => '/Users/xiaokang/www/thinkphp_5/public/static',
            'worker_num' => 5,
            'task_worker_num' => 5
        ]);

        $this->http->on('workerStart', [$this, 'onWorkerStart']);
        $this->http->on('request', [$this, 'onRequest']);
        $this->http->on('task', [$this, 'onTask']);

        $this->http->start();
    }

    /**
     * @param $server
     * @param $worker_id
     */
    public function onWorkerStart($server, $worker_id)
    {
        // 定义应用目录
        define('APP_PATH', __DIR__ . '/../application/');
        // ThinkPHP 引导文件
        // 加载基础文件
//        require __DIR__ . '/../thinkphp/base.php';
        require __DIR__ . '/../thinkphp/start.php';
    }

    /**
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response)
    {
        if ($request->server['request_uri'] == '/favicon.ico') {
            return;
        }

        /**
         * 1.swoole进程存活期间，不会释放$_GET、$_POST等超全局变量和url的请求等，所以需要修改框架路由判断的远吗
         * 解决思路:/thinkphp/library/Request.php/path方法和pathinfo方法，去掉对路由null的判断;$_SERVER、$_GET、$_POST初始赋值为空
         *
         * 2.当使用/index/index/index的路由而不用s=index/index/index的路由时，因为是使用swoole终端的方式启动的server服务，框架内会有一个判断，当使用cli模式时
         *   $_SERVER[pathinfo]会返回空
         * 解决思路:修改/thinkphp/library/Request.php/pathinfo方法，增加对$_SERVER[pathinfo]的判断
         */

        $_SERVER = $_GET = $_POST = [];
        //将swoole中获取请求数据的格式转化为原生php的格式
        if (isset($request->server)) {
            foreach ($request->server as $key => $val) {
                $_SERVER[strtoupper($key)] = $val;
            }
        }

        if (isset($request->header)) {
            foreach ($request->header as $key => $val) {
                $_SERVER[strtoupper($key)] = $val;
            }
        }

        if (isset($request->get)) {
            foreach ($request->get as $key => $val) {
                $_GET[$key] = $val;
            }
        }

        if (isset($request->post)) {
            foreach ($request->post as $key => $val) {
                $_POST[$key] = $val;
            }
        }
        $_POST['server'] = $this->http;
        ob_start();
        // 执行应用并响应
        Container::get('app', [APP_PATH])
            ->run()
            ->send();
        $res = ob_get_contents();
        ob_end_clean();
        $response->end($res);
    }

    /**
     * 任务
     * @param $server
     * @param $taskId
     * @param $workerId
     * @param $data
     * @return string
     */
    public function onTask($server, $taskId, $workerId, $data)
    {
        //task任务分发机制，让不同的任务走不同的方法
        $task = new app\common\lib\task\Task();
        $method = $data['method'];
        $flag = $task->$method($data['data']);

        return $flag;
    }
}

$server = new Http();

