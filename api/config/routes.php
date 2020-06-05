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

use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');

Router::post('/sys/login', 'App\Controller\Admin\UserController@login'); //管理员登录

Router::addGroup('/sys/', function () {

    Router::get('menu/nav', 'App\Controller\Admin\MenuController@sysMenuNav'); // 登录用户的菜单和权限
    Router::get('menu/list', 'App\Controller\Admin\MenuController@sysMenuList'); // 获取Menu列表
    Router::get('menu/select', 'App\Controller\Admin\MenuController@sysMenuSelect'); // 选择Menu列表
    Router::get('menu/info/{id:\d+}', 'App\Controller\Admin\MenuController@sysMenuInfo'); // 获取某个菜单信息
    Router::post('menu/save', 'App\Controller\Admin\MenuController@sysMenuSave'); // 保存Menu
    Router::post('menu/update', 'App\Controller\Admin\MenuController@sysMenuUpdate'); // 更新Menu
    Router::post('menu/delete/{id:\d+}', 'App\Controller\Admin\MenuController@sysMenuDelete'); // 更新Menu

    Router::get('user/info', 'App\Controller\Admin\UserController@getInfoByLoginUserId'); // 登录的用户信息
    Router::get('user/info/{id:\d+}', 'App\Controller\Admin\UserController@getInfoByUserId'); // 获取用户信息
    Router::get('user/list', 'App\Controller\Admin\UserController@sysUserList'); // 管理员用户列表
    Router::post('user/save', 'App\Controller\Admin\UserController@sysUserSave'); // 保存管理员
    Router::post('user/update', 'App\Controller\Admin\UserController@sysUserUpdate'); // update管理员
    Router::post('user/delete', 'App\Controller\Admin\UserController@sysUserDelete'); // 删除管理员
    Router::post('user/password', 'App\Controller\Admin\UserController@password'); // 修改密码

    Router::get('role/list', 'App\Controller\Admin\RoleController@sysRoleList'); // 角色管理列表
    Router::get('role/select', 'App\Controller\Admin\RoleController@sysRoleSelect'); // select角色列表
    Router::get('role/info/{id:\d+}', 'App\Controller\Admin\RoleController@sysRoleInfo'); // 获取角色信息
    Router::post('role/save', 'App\Controller\Admin\RoleController@sysRoleSave'); // 新增角色
    Router::post('role/update', 'App\Controller\Admin\RoleController@sysRoleUpdate'); // 更新角色
    Router::post('role/delete', 'App\Controller\Admin\RoleController@sysRoleDelete'); // 删除角色

    Router::get('log/list', 'App\Controller\Admin\LogController@sysLogList'); // 日志列表

    Router::get('oss/list', 'App\Controller\Admin\OssController@sysOssList'); // OSS列表
    Router::get('oss/config', 'App\Controller\Admin\OssController@sysOssConfig'); // OSS配置
    Router::post('oss/delete', 'App\Controller\Admin\OssController@sysOssDelete'); // OSS删除
    Router::post('oss/saveConfig', 'App\Controller\Admin\OssController@sysOssSaveConfig'); // 保存OSS配置
    Router::post('oss/upload', 'App\Controller\Admin\OssController@sysOssUpload'); // 上传文件

    Router::get('config/list', 'App\Controller\Admin\ConfigController@sysConfigList'); // 参数列表
    Router::get('config/info/{id:\d+}', 'App\Controller\Admin\ConfigController@sysConfigInfo'); // 获取参数
    Router::post('config/save', 'App\Controller\Admin\ConfigController@sysConfigSave'); // 新增参数
    Router::post('config/update', 'App\Controller\Admin\ConfigController@sysConfigUpdate'); // 新增参数
    Router::post('config/delete', 'App\Controller\Admin\ConfigController@sysConfigDelete'); // 删除参数

    Router::post('logout', 'App\Controller\Admin\UserController@sysLogout'); // 退出登录
}
,['middleware' => [App\Middleware\Admin\AdminAuthMiddleware::class]]
);
