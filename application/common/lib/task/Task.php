<?php
/**
 * swoole中所有的task异步任务
 */
namespace app\common\lib\task;

use app\common\lib\ali\Sms;
use app\common\lib\Constants;
use app\common\lib\Until;
use app\common\lib\RedisClient;

class Task
{
    /**
     * 发送短信
     * @param $data
     * @param $server: swoole_server 对象
     */
    public function sendSms($data, $server)
    {
        try{
            $result = Sms::send(Constants::SMS_VERIFY_CODE, $data['code'], $data['phone']);
            print_r($result);
        } catch (\Exception $exception) {
            //todo
            return Until::show(Constants::STATUS_FAIL, Constants::ERROR);
        };
        //发送成功后将验证码存入redis
        if ($result->Code === "OK") {
            //在redis中记录
            $redis = RedisClient::getInstance();
            $redis->set(Constants::REDIS_SMS_KEY . $data['phone'], $data['code'], config('redis.out_time'));
            return Until::show(Constants::STATUS_SUCCESS, Constants::SUCCESS);
        } else {
            return Until::show(Constants::STATUS_FAIL, Constants::ERROR);
        }
    }

    /**
     * 直播消息推送
     * @param $data
     * @param $server: swoole_server 对象
     */
    public function pushLive($data, $server)
    {
        try {
            unset($data['server']);
            //获取连接的用户
            $redis = RedisClient::getInstance();
            $clients = $redis->sMembers(Constants::GAME_KEY);
            foreach ($clients as $fd) {
                $server->push($fd, json_encode($data));
            }
        } catch (\Exception $exception) {
            //todo
            return Until::show(Constants::STATUS_FAIL, Constants::ERROR);
        };
    }
}