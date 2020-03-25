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

namespace App\Controller;

use App\Exception\JsonErrException;
use App\Exception\JsonException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Coroutine;
use Hyperf\Redis\RedisFactory;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use App\Service\BinaryExtService;

/**
 * @Controller(prefix="/",server="http")
 * @package App\Controller
 */
class IndexController extends AbstractController
{
    /**
     * @Inject()
     * @var BinaryExtService
     */
    public $s4Service;

    protected $redis;
    public function __construct()
    {
        $container = ApplicationContext::getContainer();
        $this->redis     = $container->get(RedisFactory::class)->get('default');
    }

    /**
     * @RequestMapping(path="/",methods="get,post")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(RequestInterface $request)
    {
        $user      = $request->input('user', 'Hyperf');
        $method    = $request->getMethod();
        $this->redis->set("hyper", "{$user}");
        return [
            'cid' => Coroutine::id(),
            'method' => $method,
            'message' => "Hello {$user}.",
        ];
    }

    private function getS4Id()
    {
        $incrId    = $this->redis->incr("dwz:_id", 1);
        return $this->s4Service->dec2s4($incrId);
    }

    /**
     * @RequestMapping(path="/d",methods="get,post")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function d(RequestInterface $request, ResponseInterface $response)
    {
        $uri = $request->input('uri');
        if (empty($uri)) {
            throw new JsonException("uri err.", 10001);
        }
        //RFC 兼容 URL
        if (!filter_var($uri, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
            throw new JsonException("uri err.", 10002);
        }
        $md5Key    = md5($uri);

        $setRes    = $this->redis->sadd("dwz:_unique", $md5Key);
        $uriLinks  = "dwz:_list";
        if (!$setRes) {
            //已存在
            $s4Id = $this->redis->hget("dwz:_unique_list", "{$md5Key}");
            if (!$s4Id) {
                $s4Id = $this->getS4Id();
            }
        }
        else {
            $s4Id = $this->getS4Id();
        }
        $this->redis->hset($uriLinks, "{$s4Id}", $uri);
        $this->redis->hset("dwz:_unique_list", "{$md5Key}", "{$s4Id}");
        $juri = env("DWZ_HOST","http://127.0.0.1:9501")."/z/{$s4Id}";

        //TODO 统计

        return $response->json(['status' => 0, 'data' => ['d' => $s4Id, 'uri' => $uri, 'juri' => $juri], 'msg' => 'suc']);

    }

    public function z($did)
    {
        $uriLinks  = "dwz:_list";
        $juri  = $this->redis->hget($uriLinks, "{$did}");
        if(!$juri){
            throw new JsonErrException("短ID查询失败[{$did}]",10003);
        }
        //TODO 统计

        return $this->response->redirect($juri,302);
    }

}
