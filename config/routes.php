<?php

declare(strict_types=1);

use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');

Router::post('/renren-fast/sys/login', 'App\Controller\Admin\SysUserController@login'); //管理员登录

Router::addGroup('/renren-fast/', function () {

    Router::get('sys/menu/nav', 'App\Controller\Admin\SysUserController@menu_nav'); // 登录用户的菜单和权限
    Router::get('sys/user/info', 'App\Controller\Admin\SysUserController@getInfoByLoginUserId'); // 登录的用户信息
    Router::get('sys/user/info/{id:\d+}', 'App\Controller\Admin\SysUserController@getInfoByUserId'); // 获取用户信息
    Router::get('sys/user/list', 'App\Controller\Admin\SysUserController@sysUserList'); // 管理员用户列表
    Router::get('sys/role/list', 'App\Controller\Admin\SysUserController@sysRoleList'); // 角色管理列表
    Router::get('sys/role/select', 'App\Controller\Admin\SysUserController@sysRoleSelect'); // select角色列表
    Router::post('sys/user/save', 'App\Controller\Admin\SysUserController@sysUserSave'); // 保存管理员
    Router::post('sys/user/update', 'App\Controller\Admin\SysUserController@sysUserUpdate'); // update管理员

},
    ['middleware' => [App\Middleware\AdminMiddleware::class]]
);
