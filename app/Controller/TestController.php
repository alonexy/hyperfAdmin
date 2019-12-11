<?php

declare(strict_types=1);

namespace App\Controller;

use App\AdminAnnotations\HfAdminC;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\View\RenderInterface;
/**
 * @Controller(prefix="tts",server="http")
 * @Middleware(HfAdminMiddleWare::class)
 * @package App\Controller
 */
class TestController
{
    /**
     * @RequestMapping(path="index",methods="get,post")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(RequestInterface $request ,RenderInterface $render)
    {
        return $render->render('test', ['name' => 'Hyperf']);
    }
}
