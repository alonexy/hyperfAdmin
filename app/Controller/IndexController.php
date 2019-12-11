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

use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Coroutine;


class IndexController extends AbstractController
{
    public function index()
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();
        $container = ApplicationContext::getContainer();
        $redis = $container->get(\Redis::class);
        $redis->set("hyper","{$user}");
        return [
            'cid'=>Coroutine::id(),
            'method' => $method,
            'message' => "Hello {$user}.",
        ];
    }
}
