<?php
namespace app\index\controller;

use app\common\lib\ali\Sms;
use app\common\lib\Until;
use app\common\lib\Constants;

class Send
{
    /**
     * 发送短信验证码
     */
    public function index()
    {
//        $phone = request()->get('phone_num', 0, 'intval');
        $phone = intval($_GET['phone_num']);
        if (empty($phone)) {
            return Until::show(Constants::STATUS_FAIL, Constants::PHONE_CAN_NOT_EMPTY);
        }

        //生成验证码(随机数)
        $code = rand(1000, 9999);
        //投递异步任务
        $data = [
            'method' => 'sendSms',
            'data' => [
                'phone' => $phone,
                'code' => $code
            ]
        ];
        $result = $_POST['server']->task($data);
        return Until::show(Constants::STATUS_SUCCESS, Constants::SUCCESS);

//        if ($result->Code === "OK") {
//            //在redis中记录
//            $redis = new \Co\Redis();
//            $redis->connect(config('redis.host'), config('redis.port'));
//            $redis->set(Constants::REDIS_SMS_KEY . $phone, $code, config('redis.out_time'));
//            return Until::show(Constants::STATUS_SUCCESS, Constants::SUCCESS);
//        } else {
//            return Until::show(Constants::STATUS_FAIL, Constants::ERROR);
//        }
    }
}
