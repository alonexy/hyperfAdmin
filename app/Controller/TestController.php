<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\View\RenderInterface;

/**
 * @Controller(prefix="/admin", server="http")
 */
class TestController
{
    /**
     * @RequestMapping(path="tts", methods="get,post")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(RequestInterface $request, ResponseInterface $render)
    {
        $str = '{"msg":"SUC","data":[{"C":{"Class":"App_Controller_Admin_HomeController","Cpath":"\/admin","Cdisplay":true,"active":false,"Cauto":true,"Cname":"控制面板","Csort":1,"Cstyle":"fa-dashboard"},"sort":1,"F":{"首页":{"fun_path":"index","route":"\/admin\/index","active":false,"display":true,"name":"首页","style":"fa-circle-o"},"代办列表":{"fun_path":"todo_lists","route":"\/admin\/todo_lists","active":false,"display":true,"name":"代办列表","style":"fa-circle-o"}}},{"C":{"Class":"App_Controller_Admin_UserController","Cpath":"\/admin\/user","Cdisplay":true,"active":false,"Cauto":true,"Cname":"账号管理","Csort":2,"Cstyle":"fa-user"},"sort":2,"F":{"账号列表":{"fun_path":"index","route":"\/admin\/user\/index","active":false,"display":true,"name":"账号列表","style":"fa-circle-o"},"新增账号":{"fun_path":"create","route":"\/admin\/user\/create","active":false,"display":true,"name":"新增账号","style":"fa-circle-o"},"编辑账号":{"fun_path":"edit","route":"\/admin\/user\/edit","active":false,"display":false,"name":"编辑账号","style":"fa-circle-o"},"删除账号":{"fun_path":"del","route":"\/admin\/user\/del","active":false,"display":false,"name":"删除账号","style":"fa-circle-o"}}},{"C":{"Class":"App_Controller_Admin_RoleController","Cpath":"\/admin\/role","Cdisplay":true,"active":false,"Cauto":true,"Cname":"角色管理","Csort":3,"Cstyle":"fa-th"},"sort":3,"F":{"角色列表":{"fun_path":"index","route":"\/admin\/role\/index","active":false,"display":true,"name":"角色列表","style":"fa-circle-o"},"创建角色":{"fun_path":"create","route":"\/admin\/role\/create","active":false,"display":true,"name":"创建角色","style":"fa-circle-o"},"编辑角色":{"fun_path":"edit","route":"\/admin\/role\/edit","active":false,"display":false,"name":"编辑角色","style":"fa-circle-o"},"删除角色":{"fun_path":"del","route":"\/admin\/role\/del","active":false,"display":false,"name":"删除角色","style":"fa-circle-o"}}},{"C":{"Class":"App_Controller_Admin_AccountController","Cpath":"\/admin\/account","Cdisplay":true,"active":false,"Cauto":true,"Cname":"客户管理","Csort":20,"Cstyle":"fa-user"},"sort":20,"F":{"用户列表":{"fun_path":"index","route":"\/admin\/account\/index","active":false,"display":true,"name":"用户列表","style":"fa-circle-o"},"添加用户":{"fun_path":"add","route":"\/admin\/account\/add","active":false,"display":true,"name":"添加用户","style":"fa-circle-o"}}}],"status":0}';
        return $render->json(json_decode($str,true));
    }
}
