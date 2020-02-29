<?php

namespace app\common\lib\ali;


class Sms
{
    const APP_CODE = '';
    const SIGN = '';
    /**
     * 使用方法 https://market.aliyun.com/products/57002003/cmapi011900.html
     * @param string $service 服务名称
     * @param string $message 告警信息
     * @param string $phone 目标手机号
     * @param string   模板CODE
     * @return mixed
     */
    public static function send($service, $message, $phone, $templateCode = 'SMS_94755028')
    {
        return static::sendCaptcha($service, $phone, $templateCode, $message);
    }
    /*
     * 阿里云短信服务器迁移后调用
     *
     * */
    public static function sendCaptcha($service, $phone, $templateCode = 'SMS_107825044', $message = '')
    {
        $params = array();
        $accessKeyId = "";
        $accessKeySecret = "";

        $content = null;
        $phoneList = explode(',', $phone);
        foreach ($phoneList as $phone) {

            $params["PhoneNumbers"] = $phone;
            $params["SignName"] = "";
            $params["TemplateCode"] = $templateCode;
            $params['TemplateParam'] = Array(
                "service" => $service,
                "message" => $message
            );
            if (!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
                $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
            }
            $helper = new SignatureHelper();
            try {
                // 此处可能会抛出异常，注意catch
                $content = $helper->request(
                    $accessKeyId,
                    $accessKeySecret,
                    "dysmsapi.aliyuncs.com",
                    array_merge($params, array(
                        "RegionId" => "cn-hangzhou",
                        "Action" => "SendSms",
                        "Version" => "2017-05-25",
                    ))
                );
            } catch (\Exception $ex) {
                $content = 'issue';
            }
        }
        return $content;
    }
}
