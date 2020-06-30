<?php

declare(strict_types = 1);

namespace App\Controller\Admin;

use Hyperf\Cache\Helper\Func;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use App\Middleware\HfAdminMiddleWare;
use App\Service\HfAdminService;
/**
 * @Controller(prefix="/admin", server="http")
 * @Middleware(HfAdminMiddleWare::class)
 * Class SystemController
 */
class SystemController
{
    use Func;

    /**
     * @Inject()
     * @var HfAdminService
     */
    protected $HfService;

    /**
     * @GetMapping(path="getUser")
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function getUser(RequestInterface $request, ResponseInterface $response)
    {
        list($res, $info) = $this->HfService->getAuthUser();
        if (!$res) {
            return $response->json($this->getMessageBody("{$info}", [], -1));
        }
        return $response->json($this->getMessageBody("SUC", $info));
    }

    /**
     * @RequestMapping(path="getMenus",methods="get,post")
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getMenus(RequestInterface $request, ResponseInterface $response)
    {
        $menus = $this->HfService->getMenus();
        list($menus, $roleName) = $this->HfService->getViewData($request, $menus);
        return $response->json($this->getMessageBody("SUC", ["menus"=>$menus,"roleName"=>$roleName]));
    }

}
