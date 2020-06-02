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

namespace App\Service;

use App\AdminAnnotations\HfAdminC;
use App\AdminAnnotations\HfAdminF;
use App\Model\Role;
use App\Model\User;
use Doctrine\Common\Annotations\AnnotationReader;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Mapping;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ServerRequestInterface;

class HfAdminService
{
    /**
     * @Inject
     * @var User
     */
    public $UserModel;

    /**
     * @Inject
     * @var Role
     */
    public $RoleModel;

    /**
     * @Inject
     * @var RenderInterface
     */
    public $view;

    /**
     * @Inject
     * @var \Hyperf\Contract\SessionInterface
     */
    private $session;

    /**
     * 验证用户.
     * @param $email
     * @param $passwd
     * @param null $rememberToken
     * @return array
     */
    public function auth($email, $passwd, $rememberToken = null)
    {
        try {
            $ret = $this->UserModel->where('email', $email)
                ->where('password', $this->passSign($passwd))
                ->firstOrFail();
        }
        catch (ModelNotFoundException $e) {
            return [false, 'login Fail', null];
        }
        catch (\Exception $e) {
            return [false, $e->getMessage(), null];
        }
        return [true, '', $ret->toArray()];
    }

    public function passSign($passwd, $signSalt = null)
    {
        if (empty($signSalt)) {
            return md5($passwd . $signSalt);
        }
        return md5($passwd . base64_encode($signSalt));
    }

    /**
     * 获取当前的请求控制器和和路由.
     * @return array
     */
    public function getCurrentActionAndRoute(ServerRequestInterface $request)
    {
        $arr = $request->getAttributes();
        $ret = array_values($arr);
        if (empty($request) || !isset($ret[0])) {
            return [false, null];
        }
        $action = $ret[0]->handler->callback[0];
        $func   = $ret[0]->handler->callback[1];
        $route  = $ret[0]->handler->route;
        return [true, "{$action}@{$func}", $route];
    }

    /**
     * 获取后台菜单树.
     * @return array
     */
    public function getMenus()
    {
        $ClassArr = AnnotationCollector::getClassByAnnotation(HfAdminC::class);
        $FuncArr  = AnnotationCollector::getMethodByAnnotation(HfAdminF::class);
        $Menus    = [];
        $reader   = new AnnotationReader();
        foreach ($ClassArr as $k => $c) {
            $C['Class']                             = $k;
            $C['Cpath']                             = '';
            $C['Cdisplay']                          = false; //默认隐藏
            $C['active']                            = false; //默认不选中
            $reflClass                              = new \ReflectionClass(new $k());
            $Rname                                  = $reflClass->getName();
            $pathArr                                = explode('\\', $Rname);
            $slicePathArr                           = array_slice($pathArr, 2);
            $slicePathArr[count($slicePathArr) - 1] = preg_replace('/controller/', '', strtolower(end($slicePathArr)));
            $C['Cpath']                             = strtolower(implode('/', $slicePathArr));
            $classAnnotations                       = $reader->getClassAnnotations($reflClass);
            $C['Cauto']                             = false;
            foreach ($classAnnotations as $cAnnot) {
                if ($cAnnot instanceof AutoController) {
                    if (!empty($cAnnot->prefix)) {
                        $C['Cpath'] = $cAnnot->prefix;
                    }
                    $C['Cauto'] = true;
                }
                elseif ($cAnnot instanceof Controller) {
                    if (!empty($cAnnot->prefix)) {
                        $C['Cpath'] = $cAnnot->prefix;
                    }
                }
            }
            if (!preg_match('/^\\/.*/', $C['Cpath'])) {
                $C['Cpath'] = '/' . $C['Cpath'];
            }
            $C['Cname']             = $c->Cname;
            $C['Csort']             = $c->Csort;
            $C['Cstyle']            = $c->Cstyle;
            $Menus[md5($k)]['C']    = $C;
            $Menus[md5($k)]['sort'] = $c->Csort;
        }
        foreach ($FuncArr as $k => $f) {
            $farr              = [];
            $c                 = $f['class'];
            $method            = $f['method'];
            $farr['fun_path']  = $f['method']; //默认使用方法名
            $farr['route']     = $f['method'];
            $refectMethod      = new \ReflectionMethod(new $c(), $method);
            $methodAnnotations = $reader->getMethodAnnotations($refectMethod);
            foreach ($methodAnnotations as $mAnnot) {
                if ($mAnnot instanceof Mapping) {
                    if (!empty($mAnnot->path)) {
                        $farr['fun_path'] = $mAnnot->path;
                    }
                }
            }
            $farr['active']  = false;
            $farr['display'] = $f['annotation']->Fdisplay;
            $farr['name']    = $f['annotation']->Fname;
            $farr['style']   = $f['annotation']->Fstyle;
            /*
             * C  不存在默认忽略
             */
            if (isset($Menus[md5($f['class'])]['C'])) {
                //自动方法使用方法名为路由
                if ($Menus[md5($f['class'])]['C']['Cauto'] === true) {
                    $farr['route'] = $Menus[md5($f['class'])]['C']['Cpath'] . '/' . $farr['route'];
                }
                else {
                    //如果方法里面有／说明路由是根地址
                    if (preg_match('/^\\/.*/', $farr['fun_path'])) {
                        $farr['route'] = $farr['fun_path'];
                    }
                    else {
                        $farr['route'] = $Menus[md5($f['class'])]['C']['Cpath'] . '/' . $farr['fun_path'];
                    }
                }
                $Menus[md5($f['class'])]['F'][$farr['name']] = $farr;
            }
        }
        $Menus = $this->arrays_sort_by_item($Menus, 'SORT_ASC', 'sort');
        return array_values($Menus);
    }

    /**
     * 二维码数组排序.
     * @param $arr
     * @param string $direction
     * @param string $field
     * @return mixed
     */
    public function arrays_sort_by_item($arr, $direction = 'SORT_DESC', $field = 'id')
    {
        $sort    = [
            'direction' => $direction, //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field' => $field,       //排序字段
        ];
        $arrSort = [];
        foreach ($arr as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $arrSort[$key][$uniqid] = $value;
            }
        }
        if ($sort['direction']) {
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $arr);
        }
        return $arr;
    }

    /**
     * 获取用户角色.
     * @throws \Exception
     * @return array
     */
    public function getAuthRole()
    {
        list($res, $user) = $this->getAuthUser();
        if (!$res) {
            return ['--', 0, []];
        }
        $rid = (int)$user['rid'];
        if ($rid === 0) {
            return ['超级管理员', 1, []];
        }
        $info = $this->RoleModel->where('id', $rid)->first();
        if (empty($info)) {
            throw new \Exception('Role is ERr');
        }
        $powers = [];
        if (!empty($info->powers)) {
            $powers = explode(',', $info->powers);
        }
        return [$info->name, 2, $powers];
    }

    /**
     * 获取授权的用户信息.
     * @throws \Exception
     * @return mixed
     */
    public function getAuthUser()
    {
        try {
            $user = $this->session->get('user.info');
            if (empty($user)) {
                throw new \Exception('Auth is ERr');
            }
            return [true, $user];
        }
        catch (\Exception $e) {
            return [false, null];
        }
    }

    /**
     * 获取权限.
     * @return array
     */
    public function getPowers(int $rid)
    {
        $ret = $this->RoleModel->where('status', 1)->find($rid);
        if (empty($ret)) {
            return [false, null];
        }
        return [true, $ret->powers];
    }

    /**
     * 获取渲染的数据.
     * @param $menus
     * @throws \Exception
     * @return array
     */
    public function getViewData(ServerRequestInterface $request, $menus)
    {
//        $path = $request->getRequestTarget();
        //$RoleType 0 无 1 superAdmin 2 admin
        list($RoleName, $RoleType, $Powers) = $this->getAuthRole(); //获取角色
        list($res, $action, $route) = $this->getCurrentActionAndRoute($request);
        if (!$res) {
            throw new \Exception('1023 Err.');
        }
        if (!in_array($action, $Powers) && $RoleType !== 1) {
            throw new \Exception('没有权限.');
        }
        list($c, $f) = explode('@', $action);
        foreach ($menus as &$menu) {
            if (md5($c) == md5($menu['C']['Class'])) {
                $menu['C']['active'] = true;  // 控制页面是否选中
            }
            foreach ($menu['F'] as &$mF) {
                if ($RoleType !== 1) {
                    //存在权限
                    if (in_array($menu['C']['Class'] . '@' . $mF['fun_path'], $Powers)) {
                        if ($mF['display']) {
                            $mF['display'] = true;
                        }
                    }
                    else {
                        $mF['display'] = false;
                    }
                }
                if ($mF['display']) {
                    $menu['C']['Cdisplay'] = true; // 控制控制器权限
                }
                if (md5($mF['fun_path']) == md5($f)) {
                    $mF['active'] = true;
                }
            }
        }
        return [$menus, $RoleName];
    }

    /**
     * 判断是否是登陆状态
     * @return bool|\Hyperf\Contract\bool
     */
    public function isLoign()
    {
        return $this->session->has('user');
    }

    /**
     * 错误页面渲染.
     * @param $code
     * @param $errMsg
     * @param null|mixed $jump
     * @return mixed
     */
    public function errorView($code, $errMsg, $jump = null)
    {
        return $this->view->render("errors.{$code}", compact('errMsg', 'jump'));
    }
}
