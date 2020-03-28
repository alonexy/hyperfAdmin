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
use App\Service\xFunc;
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
use App\Middleware\CorsMiddleware;
use Hyperf\View\RenderInterface;
use App\Service\DwzQueueService;
use App\Service\DwzService;

/**
 * @Controller(prefix="/",server="http")
 * @package App\Controller
 */
class IndexController extends AbstractController
{
    use xFunc;
    /**
     * @Inject()
     * @var BinaryExtService
     */
    public $s4Service;
    /**
     * @Inject()
     * @var DwzQueueService
     */
    protected $dwzJobService;

    /**
     * @Inject()
     * @var DwzService
     */
    protected $DwzService;

    protected $redis;

    public function __construct()
    {
        $container   = ApplicationContext::getContainer();
        $this->redis = $container->get(RedisFactory::class)->get('default');
    }

    /**
     * @RequestMapping(path="/",methods="get,post")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(RequestInterface $request, RenderInterface $view)
    {
        $id_count     = $this->DwzService->GetNowId();
        $access_num   = $this->DwzService->GetStatAccessNum();
        $ip_count     = $this->DwzService->GetStatIp();
        $ip_day_count = $this->DwzService->GetStatIp(date('Y-m-d'));

        $id_count     = xFunc::convert($id_count);
        $access_num   = xFunc::convert($access_num);
        $ip_count     = xFunc::convert($ip_count);
        $ip_day_count = xFunc::convert($ip_day_count);
        return $view->render('web.index', compact('ip_count', 'ip_day_count', 'id_count', 'access_num'));
    }

    private function getS4Id()
    {
        $incrId = $this->DwzService->GetIncrId();
        return $this->s4Service->dec2s4($incrId);
    }

    /**
     * @RequestMapping(path="/d",methods="get,post")
     * @Middleware(CorsMiddleware::class)
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function d(RequestInterface $request, ResponseInterface $response)
    {
        $uri = $request->input('uri');
        $ip  = $request->server('remote_addr', "");
        if (empty($uri)) {
            throw new JsonException("源链接不能为空.", 10001);
        }
        if (preg_match('/[\x{4e00}-\x{9fff}]/iu', $uri)) {
            $uri = preg_replace_callback(
                '/[\x{4e00}-\x{9fff}]/iu', function ($d) {
                return urlencode($d[0]);
            }, $uri);
        }
        //RFC 兼容 URL
        if (!filter_var($uri, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
            throw new JsonException("链接格式错误.", 10002);
        }
        list($setRes, $md5Key) = $this->DwzService->IsUniqueUrl($uri);
        if (!$setRes) {
            //已存在
            $s4Id = $this->DwzService->GetUrlUniqueListByKey($md5Key);
            if (!$s4Id) {
                $s4Id = $this->getS4Id();
            }
        }
        else {
            $s4Id = $this->getS4Id();
        }
        $this->DwzService->SetUrlListByS4id($s4Id, $uri);
        $this->DwzService->SetUrlUniqueListByKey($md5Key, $s4Id);
        $juri = $this->DwzService->GetZUrlFormatByS4Id($s4Id);

        $jobData         = [];
        $jobData['type'] = 1;
        $jobData['ip']   = "{$ip}";
        $jobData['uri']  = $uri;
        $jobData['s4id'] = $s4Id;
        $this->dwzJobService->push(json_encode($jobData));

        return $response->json(['status' => 0, 'data' => ['d' => $s4Id, 'uri' => $uri, 'juri' => $juri], 'msg' => '链接转换成功.']);

    }

    public function z($did)
    {
        $juri = $this->DwzService->GetUrlListByS4id($did);
        $ip   = $this->request->server('remote_addr', "");
        if (!$juri) {
            throw new JsonErrException("短ID查询失败[{$did}]", 10003);
        }
        $user_agent = $this->request->getHeader('user-agent')[0]??"";

        $jobData               = [];
        $jobData['type']       = 2;
        $jobData['ip']         = "{$ip}";
        $jobData['s4id']       = $did;
        $jobData['user_agent'] = $user_agent;

        $this->dwzJobService->push(json_encode($jobData));
        return $this->response->redirect($juri, 302);
    }
}
