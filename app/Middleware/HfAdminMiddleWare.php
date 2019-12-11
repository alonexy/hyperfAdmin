<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\AdminAnnotations\HfAdminC;
use App\AdminAnnotations\HfAdminF;
use App\Exception\ErrViewException;
use FastRoute\Dispatcher;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\View\RenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Service\HfAdminService;

class HfAdminMiddleWare implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * @var HfAdminService;
     */
    protected $HfService;

    /**
     * @Inject
     * @var \Hyperf\Contract\SessionInterface
     */
    private $session;

    public function __construct(ContainerInterface $container, RequestInterface $request, HfAdminService $HfAdminService)
    {
        $this->container = $container;
        $this->request   = $request;
        $this->HfService = $HfAdminService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $menus = $this->HfService->getMenus();
//        print_r($request->getAttributes());
        echo "\n";
        if (!$this->HfService->isLoign()) {
            return $this->HfService->errorView(302, "请登陆！",'/auth/login');
        }
        list($res, $info) = $this->HfService->getAuthUser();
        if (!$res) {
            return $this->HfService->errorView(403, "用户信息过期！");
        }
        list($menus, $roleName) = $this->HfService->getViewData($request, $menus);
        $_private_info             = array();
        $_private_info['menus']    = $menus;
        $_private_info['info']     = $info;
        $_private_info['roleName'] = $roleName;
        $request                   = $request->withAttribute('_private_info', $_private_info);
        return $handler->handle($request);
    }
}