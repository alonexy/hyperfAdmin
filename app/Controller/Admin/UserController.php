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

use App\AdminAnnotations\HfAdminC;
use App\AdminAnnotations\HfAdminF;
use App\Controller\AbstractController;
use App\Middleware\HfAdminMiddleWare;
use App\Model\Role;
use App\Model\User;
use App\Service\HfAdminService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\View\RenderInterface;

/**
 * @AutoController(prefix="/admin/user", server="http")
 * @HfAdminC(Cname="账号管理", Cstyle="fa-user", Csort=2)
 * @Middleware(HfAdminMiddleWare::class)
 * Class UserController
 */
class UserController extends AbstractController
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
     * @HfAdminF(Fname="账号列表", Fdisplay=true, Fstyle="fa-circle-o")
     * @param RenderInterface $response
     * @return mixed
     */
    public function index(RequestInterface $request, RenderInterface $view)
    {
        $_private_info = $request->getAttribute('_private_info');
        $s_title = $request->input('s_title', 0);
        /** @var User $user */
        $user = new User();
        if (! empty($s_title)) {
            $list = $user
                ->select('users.name as name', 'users.id as id', 'users.email as email', 'users.rid as rid', 'users.status as status', 'roles.name as r_name', 'roles.name as r_name', 'users.created_at as created_at', 'users.updated_at as updated_at')
                ->where('users.name', 'like', '%' . $s_title . '%')
                ->orderBy('users.id', 'desc')->get();
        } else {
            $list = $user
                ->select('users.name as name', 'users.id as id', 'users.email as email', 'users.rid as rid', 'users.status as status', 'roles.name as r_name', 'roles.name as r_name', 'users.created_at as created_at', 'users.updated_at as updated_at')
                ->leftJoin('roles', 'users.rid', '=', 'roles.id')->where('users.status', '<>', 3)->where('users.id', '>', 1)
                ->orderBy('users.id', 'desc')->get();
        }
        return $view->render('inx.users.list', compact('_private_info', 'list'));
    }

    /**
     * @RequestMapping(methods="get,post")
     * @HfAdminF(Fname="新增账号", Fdisplay=true, Fstyle="fa-circle-o")
     */
    public function create(RequestInterface $request, RenderInterface $view, ResponseInterface $response)
    {
        $_private_info = $request->getAttribute('_private_info');
        $errors = null;
        $roles = $this->getActiveRoles();
        if ($request->isMethod('post')) {
            $messages = [
                'name.required' => '昵称不能为空 .',
                'email.required' => '邮箱不能为空',
                'email.email' => '邮箱格式错误',
                'passwd.required' => '密码不能为空',
                'passwd.confirmed' => '两次密码不一致',
                'roleid.required' => '请先去增加角色',
                'roleid.numeric' => '角色值错误',
            ];
            $validator = $validator = $this->validationFactory->make(
                $request->all(),
                [
                    'name' => 'required|max:255',
                    'email' => 'required|email',
                    'passwd' => 'required|confirmed',
                    'roleid' => 'required|numeric',
                ],
                $messages
            );
            if ($validator->fails()) {
                $errors = $validator->errors();
            } else {
                if (empty($request->input('roleid')) || $request->input('roleid') == 0) {
                    return $this->HfAdminService->errorView(403, '角色不能为空');
                }
                /** @var User $res */
                $res = User::create(
                    [
                        'name' => $request->input('name'),
                        'email' => $request->input('email'),
                        'password' => $this->HfAdminService->passSign($request->input('passwd')),
                        'rid' => $request->input('roleid'),
                        'status' => $request->input('status'),
                    ]
                );
                if ($res) {
                    return $response->redirect('/admin/user/index', 302);
                }
            }
        }
        return $view->render('inx.users.info', compact('_private_info', 'roles', 'errors'));
    }

    /**
     * @RequestMapping(methods="get,post")
     * @HfAdminF(Fname="编辑账号", Fdisplay=false, Fstyle="fa-circle-o")
     */
    public function edit(RequestInterface $request, RenderInterface $view, ResponseInterface $response)
    {
        $_private_info = $request->getAttribute('_private_info');
        $errors = null;
        $userid = $request->input('id', 0);
        if ($userid == 1) {
            return $this->HfAdminService->errorView(403, '对不起，请求参数错误！');
        }
        if (empty($userid)) {
            return $this->HfAdminService->errorView(403, '对不起，请求参数错误！');
        }
        /** @var User $user */
        $user = new User();
        $roles = $this->getActiveRoles();
        $Info = $user
            ->select('users.name as name', 'users.id as id', 'users.email as email', 'users.rid as rid', 'users.status as status', 'roles.name as r_name', 'users.created_at as created_at', 'users.updated_at as updated_at')
            ->leftJoin('roles', 'users.rid', '=', 'roles.id')->where('users.status', '<>', 3)->where('users.id', '=', $userid)
            ->orderBy('users.id', 'desc')->first();
        if (empty($Info)) {
            return $this->HfAdminService->errorView(403, '对不起，请求参数错误！');
        }
        if ($request->isMethod('post')) {
            $messages = [
                'name.required' => '昵称不能为空 .',
                'email.required' => '邮箱不能为空',
                'email.email' => '邮箱格式错误',
                'passwd.required' => '密码不能为空',
                'passwd.confirmed' => '两次密码不一致',
                'roleid.required' => '请先去增加角色',
                'roleid.numeric' => '角色值错误',
            ];
            $validator = $this->validationFactory->make(
                $request->all(),
                [
                    'name' => 'required|max:255',
                    'email' => 'required|email',
                    'passwd' => 'required|confirmed',
                    'roleid' => 'required|numeric',
                ],
                $messages
            );
            if ($validator->fails()) {
                $errors = $validator->errors();
            } else {
                if (empty($request->input('roleid')) || $request->input('roleid') == 0) {
                    return $this->HfAdminService->errorView(403, '对不起，角色不能为空！');
                }
                $Info->name = $request->input('name');
                $Info->email = $request->input('email');
                $Info->password = $this->HfAdminService->passSign($request->input('passwd'));
                $Info->rid = $request->input('roleid');
                $Info->status = $request->input('status');
                $res = $Info->save();
                if ($res) {
                    return $response->redirect('/admin/user/index', 302);
                }
            }
        }
        return $view->render('inx.users.info', compact('_private_info', 'Info', 'roles', 'errors'));
    }

    /**
     * @RequestMapping(methods="get,post")
     * @HfAdminF(Fname="删除账号", Fdisplay=false, Fstyle="fa-circle-o")
     */
    public function del(RequestInterface $request, ResponseInterface $response)
    {
        $id = $request->input('id');
        $user = new User();
        $info = $user->find($id);
        if (empty($info)) {
            return $response->json(['msg' => '找不到该条数据', 'data' => [], 'status' => 13000]);
        }
        $info->status = 3;
        $info->save();
        return $response->json(['msg' => '删除成功', 'data' => [], 'status' => 0]);
    }

    //获取有效的角色列表
    public function getActiveRoles()
    {
        /** @var Role $role */
        $role = new Role();
        return $role->where('status', 1)->get();
    }
}
