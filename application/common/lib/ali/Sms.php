<?php

namespace app\common\lib\ali;


class Sms
{
    const APP_CODE = '0d961a34dffa40df935b04b6bde61fe0';
    const SIGN = '无双110';
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
        $accessKeyId = "LTAI7qIs9avI22uS";
        $accessKeySecret = "Rvp7DHSwwcMIEREFTSrrk3PGXXM9ZS";

        $content = null;
        $phoneList = explode(',', $phone);
        foreach ($phoneList as $phone) {

            $params["PhoneNumbers"] = $phone;
            $params["SignName"] = "三体云";
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

    /*
     * 互视科技的短信
     * */
    public static function sendhushi($code, $product, $expiry, $phone, $templateCode = 'SMS_13050150')
    {
        $params = array();
        $accessKeyId = "LTAIW2GDwWGBzvyF";
        $accessKeySecret = "iOHharpO6kBK0M5IMGZKB3hNFTvDND";

        $content = null;
        $phoneList = explode(',', $phone);
        foreach ($phoneList as $phone) {

            $params["PhoneNumbers"] = $phone;
            $params["SignName"] = "互视云视频";
            $params["TemplateCode"] = $templateCode;
            $params['TemplateParam'] = Array(
                "code" => $code,
                "product" => $product,
                "expiry" => $expiry
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
            } catch (Exception $ex) {
                $content = 'issue';
            }
        }
        return $content;
    }

    /**
     * 对接华为云连麦直播扣费服务告警
     * 使用方法 https://market.aliyun.com/products/57002003/cmapi011900.html
     * @param $service 服务名称
     * @param $message 告警信息
     * @param $phone   目标手机号
     * @param string   模板CODE
     * @return mixed
     */
    public static function sendLova($service, $message, $phone, $templateCode = 'SMS_94755028', $log = 'huaweiCloudTryTimesWarning.log')
    {
        $env = (YII_ENV_PRE || YII_ENV_ONLINE || YII_ENV_ONLINE_HZ) ? '生产' : '测试';
        $paramString['service'] = $service . $env;
        $paramString['message'] = $message;
        $host = "http://sms.market.alicloudapi.com";
        $path = "/singleSendSms";
        $method = "GET";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . self::APP_CODE);

        $phoneList = explode(',', $phone);
        foreach ($phoneList as $phone) {
            $querys = 'ParamString=' . urlencode(json_encode($paramString)) . '&RecNum=' . $phone . '&SignName=' . urlencode(self::SIGN) . '&TemplateCode=' . urlencode($templateCode);
            $url = $host . $path . "?" . $querys;

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_FAILONERROR, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, true);
            if (1 == strpos("$" . $host, "https://")) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            }
            ll($url, $log);
            $res = curl_exec($curl);
        }
        return $res;
    }

}
