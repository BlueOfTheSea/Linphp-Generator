<?php

namespace Linphp\Generator\notice;

/**
 * Class MSG
 * @package Linphp\ServiceController\command
 */
class SendMsg
{
    /**
     * @param string $msg
     * @param $httpCode
     * @param array $data
     * @return mixed
     * @author Administrator
     */
    private static function renderArray($msg = '', $httpCode, $data = [])
    {
        $msg_data['code']    = $httpCode;
        $msg_data['data']    = $data;
        $msg_data['message'] = $msg;
        if (env("app_debug") && !empty(request()->controller())) {
            $msg_data['operate'] = app('http')->getName() . '/' . request()->controller() . '/' . request()->action();
        }
        return $msg_data;
    }

    /**
     * @param array $data
     * @param $httpCode
     * @return mixed
     * @author Administrator
     */
    public static function arrayData($data = [], $httpCode)
    {
        return self::renderArray("success", (int)$httpCode, $data);
    }

    /**
     * 操作数据返回
     * @param array $data
     * @param int $httpCode
     * @param string $type
     * @param array $headers
     * @return \think\Response
     * @author Administrator
     */
    public static function jsonData($data = [], $httpCode = 200, $type = 'json', $headers = [])
    {
        return response(self::arrayData($data, $httpCode), 200, $headers, $type);
    }

    /**
     * 操作弹层/提示/消息/警告返回【强制抛出模式】【简化使用，前置message】
     * @param string $message
     * @param int $code
     * @param array $data
     * @param string $type
     * @param int $httpCode
     * @param array $headers
     * @return \think\Response
     * @author Administrator
     */
    public static function jsonThrow(
        $message = "fail",
        $code = 40000,
        $data = [],
        $type = 'json',
        $httpCode = 200,
        $headers = []
    ) {
        return response(self::renderArray($message, $code, $data), $httpCode, $headers, $type);
    }
}