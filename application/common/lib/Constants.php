<?php

namespace app\common\lib;

class Constants {
    const HOST = '127.0.0.1';
    const PORT = '8811';

    //API Status
    const STATUS_FAIL = 0;
    const STATUS_SUCCESS = 1;

    const PHONE_CAN_NOT_EMPTY = '手机号不能为空';
    const SMS_VERIFY_CODE = '短信验证码';
    const ERROR = '服务异常';
    const SUCCESS = '操作成功';

    const PARAMS_EMPTY = '参数不能为空';

    const REDIS_SMS_KEY = 'sms_';
    const REDIS_USER_KEY = 'user_';
    const GAME_KEY = 'game_key';

    const DOMAIN = 'http://local.3ttech.cn:8811/';
    const PIC_PATH = 'upload';
}