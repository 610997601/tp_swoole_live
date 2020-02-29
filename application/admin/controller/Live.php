<?php

namespace app\admin\controller;

use app\common\lib\Until;
use app\common\lib\Constants;
use app\common\lib\RedisClient;

class Live
{
    /**
     * 解说员发送消息
     */
    public function push()
    {
        if (empty($_GET)) return Until::show(Constants::STATUS_FAIL, Constants::PARAMS_EMPTY);
        $teams = [
            1 => [
                'name' => '马刺',
                'log' => '/live/imgs/team1.png'
            ],
            4 => [
                'name' => '火箭',
                'log' => '/live/imgs/team2.png'
            ]
        ];

        $data = [
            'type' => empty($_GET['type']) ? 1 : intval($_GET['type']),//第几节
            'title' => empty($teams[$_GET['team_id']]) ? '解说员' : $teams[$_GET['team_id']]['name'],
            'logo' => empty($teams[$_GET['team_id']]) ? '' : $teams[$_GET['team_id']]['log'],
            'content' => empty($_GET['content']) ? '' : $_GET['content'],
            'image' => empty($_GET['image']) ? '' : $_GET['image'],
        ];
        //赛况信息入库
        //数据组织好，push到直播页面

        $data = [
            'method' => 'pushLive',
            'data' => $data
        ];
        $result = $_POST['server']->task($data);
        return Until::show(Constants::STATUS_SUCCESS, Constants::SUCCESS);
    }

}