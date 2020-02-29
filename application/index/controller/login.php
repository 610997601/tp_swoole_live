<?php
namespace app\index\controller;

use app\common\lib\Until;
use app\common\lib\Constants;
use app\common\lib\RedisClient;
class Login
{
    public function index()
    {
        $phone = intval($_GET['phone_num']);
        $code = intval($_GET['code']);
        if (empty($phone) || empty($code)) {
            return Until::show(Constants::STATUS_FAIL, Constants::PARAMS_EMPTY);
        }
        //获取redis中的code
        $redis = RedisClient::getInstance();
        $redisCode = $redis->get(Constants::REDIS_SMS_KEY . $phone);
        if ($code == $redisCode) {
            $userKey = Constants::REDIS_USER_KEY . $phone;
            //写入redis
            $data = [
                'user' =>  $phone,
                'srcKey' => md5($userKey),
                'time' => time(),
                'isLogin' => true
            ];
            $redis->set($userKey, $data);

            return Until::show(Constants::STATUS_SUCCESS, Constants::SUCCESS, $data);
        } else {
            return Until::show(Constants::STATUS_FAIL, Constants::ERROR);
        }
    }
}