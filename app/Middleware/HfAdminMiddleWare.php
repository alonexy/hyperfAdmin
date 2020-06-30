<?php

declare(strict_types = 1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Middleware;

use App\Exception\JsonErrException;
use App\Exception\JsonException;
use App\Service\HfAdminService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

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


    public function __construct(ContainerInterface $container, RequestInterface $request, HfAdminService $HfAdminService)
    {
        $this->container = $container;
        $this->request   = $request;
        $this->HfService = $HfAdminService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            if (!$this->HfService->isLoign()) {
                throw new JsonErrException("用户信息过期.");
            }
            list($res, $info) = $this->HfService->getAuthUser();
            if (!$res) {
                throw new JsonErrException("{$info}");
            }
            $menus = $this->HfService->getMenus();
            list($menus, $roleName) = $this->HfService->getViewData($request, $menus);
            $_private_info             = [];
            $_private_info['menus']    = $menus;
            $_private_info['info']     = $info;
            $_private_info['roleName'] = $roleName;
            $request                   = $request->withAttribute('_private_info', $_private_info);
        }
        catch (\Exception $e) {
            throw new JsonException("{$e->getMessage()}", 100031);
        }
        return $handler->handle($request);
    }
}
