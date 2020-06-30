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

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Service\HfAdminService;
use Hyperf\Cache\Helper\Func;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\View\RenderInterface;
use Qbhy\HyperfAuth\AuthManager;

/**
 * @Controller(prefix="/auth")
 * Class AuthController
 */
class AuthController extends AbstractController
{
    use Func;

    /**
     * @Inject()
     * @var AuthManager
     */
    protected $auth;
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
     * @PostMapping(path="login")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function login(RequestInterface $request, ResponseInterface $response)
    {

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
            return $response->json($this->getMessageBody($validator->errors()->first(), ["errors" => $errors]));
        }
        else {
            $email          = $request->post('email');
            $passwd         = $request->post('passwd');
            $remember_token = $request->post('remember_token', null);
            list($res, $errMsg, $info) = $this->HfAdminService->auth($email, $passwd, $remember_token);
            if (!$res) {
                return $response->json($this->getMessageBody($errMsg, [], -1));
            }
            if ($info->status !== 1) {
                return $response->json($this->getMessageBody($errMsg, [], -1));
            }
            else {
                $token = $this->auth->login($info);
                return $response->json($this->getMessageBody("SUC", ["token" => $token]));
            }
        }

    }

    /**
     * @GetMapping(path="logout")
     * @return mixed
     */
    public function logout(RequestInterface $request, ResponseInterface $response)
    {
        $this->auth->logout();
        return $response->json($this->getMessageBody("logOut Suc."));
    }
}
