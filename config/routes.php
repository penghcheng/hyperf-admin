<?php

declare(strict_types=1);

use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');

Router::post('/renren-fast/sys/login', 'App\Controller\Admin\SysUserController@login'); //管理员登录

Router::addGroup('/renren-fast/', function () {

    Router::get('sys/menu/nav', 'App\Controller\Admin\SysMenuController@sysMenuNav'); // 登录用户的菜单和权限
    Router::get('sys/menu/list', 'App\Controller\Admin\SysMenuController@sysMenuList'); // 获取Menu列表
    Router::get('sys/user/info', 'App\Controller\Admin\SysUserController@getInfoByLoginUserId'); // 登录的用户信息
    Router::get('sys/menu/select', 'App\Controller\Admin\SysMenuController@sysMenuSelect'); // 选择Menu列表

    Router::get('sys/user/info/{id:\d+}', 'App\Controller\Admin\SysUserController@getInfoByUserId'); // 获取用户信息
    Router::get('sys/user/list', 'App\Controller\Admin\SysUserController@sysUserList'); // 管理员用户列表
    Router::post('sys/user/save', 'App\Controller\Admin\SysUserController@sysUserSave'); // 保存管理员
    Router::post('sys/user/update', 'App\Controller\Admin\SysUserController@sysUserUpdate'); // update管理员
    Router::post('sys/user/delete', 'App\Controller\Admin\SysUserController@sysUserDelete'); // 删除管理员

    Router::get('sys/role/list', 'App\Controller\Admin\SysRoleController@sysRoleList'); // 角色管理列表
    Router::get('sys/role/select', 'App\Controller\Admin\SysRoleController@sysRoleSelect'); // select角色列表
    Router::post('sys/role/save', 'App\Controller\Admin\SysRoleController@sysRoleSave'); // 新增角色
    Router::get('sys/role/info/{id:\d+}', 'App\Controller\Admin\SysRoleController@sysRoleInfo'); // 获取角色信息
    Router::post('sys/role/update', 'App\Controller\Admin\SysRoleController@sysRoleUpdate'); // 更新角色
    Router::post('sys/role/delete', 'App\Controller\Admin\SysRoleController@sysRoleDelete'); // 删除角色

    Router::get('sys/logout', 'App\Controller\Admin\SysUserController@sysLogout'); // 退出登录

},
    ['middleware' => [App\Middleware\AdminMiddleware::class]]
);
