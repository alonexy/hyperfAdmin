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

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Service\HfAdminService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\View\RenderInterface;

/**
 * @AutoController(prefix="/auth")
 * Class AuthController
 */
class AuthController extends AbstractController
{
    /**
     * @Inject
     * @var HfAdminService
     */
    protected $HfAdminService;

    /**
     * @Inject
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

    /**
     * @Inject
     * @var \Hyperf\Contract\SessionInterface
     */
    private $session;

    /**
     * @RequestMapping(methods="get,post")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function login(RequestInterface $request, RenderInterface $view, ResponseInterface $response)
    {
        $errors = null;
        if ($this->HfAdminService->isLoign()) {
            return $response->redirect('/admin/index', 302);
        }
        if ($request->getMethod() == 'POST') {
            $validator = $this->validationFactory->make(
                $request->all(),
                [
                    'email' => 'required|email|bail',
                    'passwd' => 'required|min:6',
                    'remember_token' => 'string|min:11',
                ]
            );
            if ($validator->fails()) {
                // Handle exception
                $errors = $validator->errors();
            } else {
                $email = $request->post('email');
                $passwd = $request->post('passwd');
                $remember_token = $request->post('remember_token', null);
                list($res, $errMsg, $info) = $this->HfAdminService->auth($email, $passwd, $remember_token);
                if (! $res) {
                    $validator->errors()->add('err', $errMsg);
                    $errors = $validator->errors();
                }
                if ($info['status'] !== 1) {
                    $validator->errors()->add('err', '用户状态异常');
                    $errors = $validator->errors();
                } else {
                    $this->session->set('user.info', $info);
                    return $response->redirect('/admin/index', 302);
                }
            }
        }
        return $view->render('auth.login', compact('errors'));
    }

    /**
     * @RequestMapping(methods="get")
     * @return mixed
     */
    public function logout(RequestInterface $request, RenderInterface $view)
    {
        $errors = null;
        $status = 0;
        $this->session->clear();
        return $view->render('auth.login', compact('errors', 'status'));
    }
}
