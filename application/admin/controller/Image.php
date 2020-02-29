<?php

namespace app\admin\controller;

use app\common\lib\Until;
use app\common\lib\Constants;

class Image
{
    /**
     * 解说员上传图片
     */
    public function index()
    {
        $file = request()->file('file');
        $info = $file->move('../public/static/upload');
        if ($info) {
            $data = [
                'image' => Constants::DOMAIN . Constants::PIC_PATH . '/' . $info->getSaveName()
            ];
            return Until::show(Constants::STATUS_SUCCESS, Constants::SUCCESS, $data);
        } else {
            return Until::show(Constants::STATUS_FAIL, Constants::ERROR);
        }
    }

}