<?php
namespace Hyperf\Cache\Helper;
/**
 * Created by PhpStorm.
 * User: alonexy
 * Date: 20/6/28
 * Time: 11:42
 */
trait Func
{
    /**
     * @name 获取消息主体
     * @param string $msg
     * @param array $arr
     * @param int $code
     * @return array
     */
    public  function getMessageBody($msg = '系统错误', $arr = [], $code = 0)
    {
        $data        = array();
        $data['msg'] = "{$msg}";
        if (empty($arr)) {
            $data['data'] = (object)$arr;
        }
        else {
            $data['data'] = $arr;
        }

        $data['status'] = $code;
        return $data;
    }
}