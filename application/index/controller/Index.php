<?php
namespace app\index\controller;

use app\common\lib\ali\Sms;

class Index
{
    public function index()
    {
        return '';
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }

    public function test()
    {
        echo time();
    }

    public function sms()
    {
        try {
            $res = Sms::send(123,12345, 18553546580);
            echo json_encode($res);
        } catch (\Exception $exception) {
            //todo
        }
    }
}
