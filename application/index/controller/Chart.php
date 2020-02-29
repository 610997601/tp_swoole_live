<?php
namespace app\index\controller;

use app\common\lib\ali\Sms;
use app\common\lib\Until;
use app\common\lib\Constants;

class Chart
{
    /**
     * 发送短信验证码
     */
    public function index()
    {
        //TODO 用户登录

        if (empty($_POST['game_id']) || empty($_POST['content'])) return Until::show(Constants::STATUS_FAIL, Constants::PARAMS_EMPTY);

        $data = [
            'user' => '用户' . rand(0, 2000),
            'content' => $_POST['content']
        ];
        foreach ($_POST['server']->ports[1]->connections as $fd) {
            $_POST['server']->push($fd, json_encode($data));
        }
        return Until::show(Constants::STATUS_SUCCESS, Constants::SUCCESS, $data);
    }
}
