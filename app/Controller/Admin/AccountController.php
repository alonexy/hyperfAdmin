<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\AdminAnnotations\HfAdminC;
use App\AdminAnnotations\HfAdminF;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use App\Middleware\HfAdminMiddleWare;
use Hyperf\View\RenderInterface;

/**
 * @AutoController(server="http")
 * @HfAdminC(Cname="客户管理",Cstyle="fa-user",Csort=20)
 * @Middleware(HfAdminMiddleWare::class)
 * Class HomeController
 * @package App\Controller\Admin
 */
class AccountController extends AbstractController
{
    /**
     * @RequestMapping(methods="get,post")
     * @HfAdminF(Fname="用户列表",Fdisplay=true)
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(RequestInterface $request, RenderInterface $render)
    {
        return $render->render('test', ['name' => '用户列表']);
    }
    /**
     * @RequestMapping(methods="get,post")
     * @HfAdminF(Fname="添加用户",Fdisplay=true)
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function add(RequestInterface $request, RenderInterface $render)
    {
        return $render->render('test', ['name' => '添加用户']);
    }
}
