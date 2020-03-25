<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\AdminAnnotations\HfAdminC;
use App\AdminAnnotations\HfAdminF;
use App\Controller\AbstractController;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use App\Middleware\HfAdminMiddleWare;
use Hyperf\Utils\Context;
use Hyperf\View\RenderInterface;
use App\Service\QueueService;

/**
 * @AutoController(prefix="/admin",server="http")
 * @HfAdminC(Cname="控制面板",Cstyle="fa-dashboard",Csort=1)
 * @Middleware(HfAdminMiddleWare::class)
 * Class HomeController
 * @package App\Controller\Admin
 */
class HomeController extends AbstractController
{
    /**
     * @Inject()
     * @var QueueService
     */
    protected $service;
    /**
     * @RequestMapping(methods="get,post")
     * @HfAdminF(Fname="首页",Fdisplay=true)
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(RequestInterface $request, RenderInterface $render)
    {
        $_private_info = $request->getAttribute('_private_info');
        return $render->render('inx.home.index', compact('_private_info'));
    }
    /**
     * @RequestMapping(methods="get,post")
     * @HfAdminF(Fname="代办列表",Fdisplay=true)
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function todo_lists(RequestInterface $request, RenderInterface $render)
    {
        $time = time();
        $res = $this->service->push([1,2,3,4,5,$time],10);
        var_dump($res);
        return $render->render('test', ['name' => 'todo_lists...']);
    }
}
