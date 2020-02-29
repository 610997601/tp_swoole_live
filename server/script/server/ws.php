<?php

use think\Container;

class Ws
{
    const HOST = '127.0.0.1';
    const LIVE_PORT = 8811;
    const CHART_PORT = 8812;

    public $ws = null;

    public function  __construct()
    {
        $this->ws = new swoole_websocket_server(self::HOST, self::LIVE_PORT);

        $this->ws->addListener(self::HOST, self::CHART_PORT, SWOOLE_SOCK_TCP);

        $this->ws->set([
            'enable_static_handler' => true,
            'document_root' => __DIR__ . '/../../../public/static',
            'worker_num' => 5,
            'task_worker_num' => 5
        ]);

        $this->ws->on('start', [$this, 'onStart']);
        $this->ws->on('open', [$this, 'onOpen']);
        $this->ws->on('message', [$this, 'onMessage']);
        $this->ws->on('workerStart', [$this, 'onWorkerStart']);
        $this->ws->on('request', [$this, 'onRequest']);
        $this->ws->on('task', [$this, 'onTask']);
        $this->ws->on('close', [$this, 'onClose']);

        $this->ws->start();
    }

    public function onStart($server)
    {

    }

    /**
     * 监听ws连接事件
     * @param $ws
     * @param $request
     */
    public function onOpen($ws, $request)
    {
        //暂不使用$ws->connections获取连接的客户端，使用redis存储
        //连接时将fd存入redis，close时删除
        \app\common\lib\RedisClient::getInstance()->sAdd(\app\common\lib\Constants::GAME_KEY, $request->fd);
        var_dump($request->fd);
    }

    /**
     * 监听ws消息事件
     * @param $ws
     * @param $frame
     */
    public function onMessage($ws, $frame)
    {
        echo "客户端发送到服务端的消息:{$frame->data}\n";

        $ws->push($frame->fd, "服务端返回的消息:" . date('Y-m-d H:i:s'));
    }

    /**
     * @param $server
     * @param $worker_id
     */
    public function onWorkerStart($server, $worker_id)
    {
        // 定义应用目录
        define('APP_PATH', __DIR__ . '/../../../application/');
        // ThinkPHP 引导文件
        // 加载基础文件
        require __DIR__ . '/../../../thinkphp/start.php';
        //先判断redis中是否有未清空的客户端fd，有的话则清空
        $redis = \app\common\lib\RedisClient::getInstance();
        $key = \app\common\lib\Constants::GAME_KEY;
        if ($redis->exists($key)) $redis->del($key);
    }

    /**
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response)
    {
        if ($request->server['request_uri'] == '/favicon.ico') {
            $response->status(404);
            $response->end();
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

        $_SERVER = $_FILES = $_GET = $_POST = [];
        //将swoole中获取请求数据的格式转化为原生php的格式
        if (isset($request->server)) {
            foreach ($request->server as $key => $val) {
                $_SERVER[strtoupper($key)] = $val;
            }
        }

        if (isset($request->files)) {
            foreach ($request->files as $key => $val) {
                $_FILES[$key] = $val;
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
        $this->writeLog();
        $_POST['server'] = $this->ws;
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
        $flag = $task->$method($data['data'], $server);

        return $flag;
    }

    /**
     * close
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd)
    {
        //连接时将fd存入redis，close时删除
        \app\common\lib\RedisClient::getInstance()->sRem(\app\common\lib\Constants::GAME_KEY, $fd);
        echo "clientId:{$fd}关掉了\n";
    }

    /**
     * 记录日志
     */
    public function writeLog()
    {
        $data = array_merge(
            ['date' => date('Ymd H:i:s')],
            $_GET,
            $_POST,
            $_SERVER
        );
        $log = '';
        foreach ($data as $key => $val) {
            $log .= $key . ':' . $val . ' ';
        }
        $path = APP_PATH . '../runtime/log/' . date('Ym') . '/';
        if(!is_dir($path)) {
            mkdir($path, 0777, true);
            chmod($path, 0777);
        }
        $fp = fopen($path . date('d') . 'access.log', "a+");
        //异步写入日志
        Co\System::fwrite($fp, $log . PHP_EOL);
    }
}

$server = new Ws();

