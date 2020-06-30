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
use App\Service\HfAdminService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\View\RenderInterface;

/**
 * @AutoController(prefix="/admin/role", server="http")
 * @HfAdminC(Cname="角色管理", Cstyle="fa-th", Csort=3)
 * @Middleware(HfAdminMiddleWare::class)
 * Class RoleController
 */
class RoleController extends AbstractController
{
    /**
     * @Inject
     * @var Role
     */
    protected $RoleModel;

    /**
     * @Inject
     * @var HfAdminService
     */
    protected $HfAdminService;

    /**
     * @RequestMapping(methods="get,post")
     * @HfAdminF(Fname="角色列表", Fdisplay=true,Fstyle="shield-key")
     * @param ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(RequestInterface $request, RenderInterface $view)
    {
        $_private_info = $request->getAttribute('_private_info');
        $list = $this->RoleModel->where('status', '<>', 3)->get();

        return $view->render('inx.roles.list', compact('_private_info', 'list'));
    }

    /**
     * @RequestMapping(methods="get,post")
     * @HfAdminF(Fname="创建角色", Fstyle="fa-circle-o", Fdisplay=true,Fstyle="shield-plus")
     */
    public function create(RequestInterface $request, RenderInterface $view, ResponseInterface $response)
    {
        /** @var Role $role */
        $role = new Role();

        $_private_info = $request->getAttribute('_private_info');
        $menus = $_private_info['menus'];
        if ($request->isMethod('post')) {
            try {
                $addData = [];
                $addData['name'] = $request->input('name');
                $addData['desc'] = $request->input('content');
                $addData['status'] = $request->input('status');
                $addData['powers'] = $request->input('power');
                $role->fill($addData);
                $role->save();
                return $response->redirect('/admin/role/index', 302);
            } catch (\Exception $e) {
                return $this->HfAdminService->errorView(403, "{$e->getMessage()}");
            }
        }

        if ($_private_info['info']['id'] == 1) {
            $menusJson = $this->menusHandle($menus);
        } else {
            $roleInfo = $this->RoleModel->find($_private_info['info']['rid']);
            if (empty($roleInfo)) {
                $this->HfAdminService->errorView(302, '角色权限错误，请联系管理员', '/auth/logout');
            }
            $menusJson = $this->menusHandle($menus, $roleInfo->powers, 1);
            foreach ($menusJson as $key => &$val) {
                if (! $val['checked']) {
                    unset($menusJson[$key]);
                }
                $val['checked'] = ! $val['checked'];
            }
            $menusJson = json_encode(array_values($menusJson));
        }

        return $view->render('inx.roles.info', compact('_private_info', 'menusJson'));
    }

    /**
     * @RequestMapping(methods="get,post")
     * @HfAdminF(Fname="编辑角色", Fstyle="fa-circle-o", Fdisplay=false)
     */
    public function edit(RequestInterface $request, RenderInterface $view, ResponseInterface $response)
    {
        $_private_info = $request->getAttribute('_private_info');
        $menus = $_private_info['menus'];
        $id = $request->input('id', 0);
        if (empty($id)) {
            return $this->HfAdminService->errorView(403, '对不起，请求参数错误！');
        }
        $roleInfo = $this->RoleModel->find($id);
        if (empty($roleInfo)) {
            return $this->HfAdminService->errorView(403, '对不起，请求角色错误！');
        }
        if ($request->isMethod('post')) {
            $addData = [];
            $addData['name'] = $request->input('name');
            $addData['desc'] = $request->input('content');
            $addData['status'] = $request->input('status');
            $addData['powers'] = $request->input('power');
            $roleInfo->fill($addData);
            $roleInfo->save();
            return $response->redirect('/admin/role/index', 302);
        }
        $menusJson = $this->menusHandle($menus, $roleInfo->powers);
        return $view->render('inx.roles.info', compact('_private_info', 'menusJson', 'roleInfo'));
    }

    /**
     * @RequestMapping(methods="get,post")
     * @HfAdminF(Fname="删除角色", Fstyle="fa-circle-o", Fdisplay=false)
     * @param RenderInterface $view
     */
    public function del(RequestInterface $request, ResponseInterface $response)
    {
        $id = $request->input('id');
        /** @var Role $role */
        $role = new Role();
        $info = $role->find($id);
        if (empty($info)) {
            return $response->json(['msg' => '找不到该条数据', 'data' => [], 'status' => 13000]);
        }
        $info->status = 3;
        $info->save();
        return $response->json(['msg' => '删除成功', 'data' => [], 'status' => 0]);
    }

    //获取树形菜单json数组
    public function menusHandle($menus, $power = '', $c = false)
    {
        $arrs = [];
        $powers = explode(',', $power);
//        dump($powers);
//        var_dump($menus);
        foreach ($menus as $k => $val) {
            $c_arr['id'] = $k + 1;
            $c_arr['pId'] = 0;
            $c_arr['name'] = $val['C']['Cname'];
            $c_arr['open'] = true;
            $c_arr['controller'] = false;
            $c_arr['fun'] = false;
            $c_arr['checked'] = false;

            foreach ($val['F'] as $fk => $fv) {
                $f_arr['id'] = $k + 10000;
                $f_arr['pId'] = $k + 1;
                $f_arr['name'] = $fk;
                $f_arr['controller'] = $val['C']['Class'];
                $f_arr['fun'] = $fv['fun_path'];
                $f_arr['checked'] = false;
                if (in_array($f_arr['controller'] . '@' . $f_arr['fun'], $powers)) {
                    $c_arr['checked'] = true;
                    $f_arr['checked'] = true;
                }
                $arrs[] = $f_arr;
            }
            $arrs[] = $c_arr;
        }
        if ($c) {
            return $arrs;
        }
//        dump($arrs);
        return json_encode($arrs);
    }
}
