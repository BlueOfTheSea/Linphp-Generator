<?php

namespace Linphp\ServiceController\notice;


use think\response\Json;

/**
 * Class MSG
 * @package Linphp\ServiceController\command
 */
class Msg
{
    public static function JSON($code = '', $msg = '', $data = '')
    {

        $msg_data['code'] = $code;
        $msg_data['msg']  = $msg;
        $msg_data['data'] = $data;
        return json_encode($msg_data);
    }
}