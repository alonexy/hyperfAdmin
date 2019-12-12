<?php

declare(strict_types = 1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use App\Service\HfAdminService;
use Hyperf\View\RenderInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
/**
 * @AutoController(prefix="/auth")
 * Class AuthController
 * @package App\Controller\Admin
 */
class AuthController extends AbstractController
{
    /**
     * @Inject
     * @var HfAdminService
     */
    protected $HfAdminService;
    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

    /**
     * @Inject()
     * @var \Hyperf\Contract\SessionInterface
     */
    private $session;

    /**
     * @RequestMapping(methods="get,post")
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function login(RequestInterface $request,RenderInterface $view,ResponseInterface $response)
    {
        $errors = null;
        if($this->HfAdminService->isLoign()){
            return $response->redirect('/admin/index',302);
        }
        if($request->getMethod() == 'POST'){
            $validator = $this->validationFactory->make(
                $request->all(),
                [
                    'email' => 'required|email|bail',
                    'passwd' => 'required|min:6',
                    'remember_token' => 'string|min:11',
                ]
            );
            if ($validator->fails()){
                // Handle exception
                $errors = $validator->errors();
            }else{
                $email          = $request->post('email');
                $passwd         = $request->post('passwd');
                $remember_token = $request->post('remember_token',null);
                list($res,$errMsg,$info) = $this->HfAdminService->auth($email, $passwd, $remember_token);
                if(!$res){
                    $validator->errors()->add('err',$errMsg);
                    $errors = $validator->errors();
                }
                if($info['status'] !== 1){
                    $validator->errors()->add('err',"用户状态异常");
                    $errors = $validator->errors();
                }else{
                    $this->session->set('user.info',$info);
                    return $response->redirect('/admin/index',302);
                }
            }
        }
        return $view->render("auth.login",compact('errors'));
    }

    /**
     * @RequestMapping(methods="get")
     * @param RequestInterface $request
     * @param RenderInterface $view
     * @return mixed
     */
    public function logout(RequestInterface $request,RenderInterface $view)
    {
        $errors = null;
        $status = 0;
        $this->session->clear();
        return $view->render("auth.login",compact('errors','status'));
    }
}
