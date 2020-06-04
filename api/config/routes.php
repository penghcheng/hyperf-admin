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

Router::post('/sys/login', 'App\Controller\Admin\SysUserController@login'); //管理员登录

Router::addGroup('/sys/', function () {

    Router::get('menu/nav', 'App\Controller\Admin\SysMenuController@sysMenuNav'); // 登录用户的菜单和权限
    Router::get('menu/list', 'App\Controller\Admin\SysMenuController@sysMenuList'); // 获取Menu列表
    Router::get('menu/select', 'App\Controller\Admin\SysMenuController@sysMenuSelect'); // 选择Menu列表
    Router::get('menu/info/{id:\d+}', 'App\Controller\Admin\SysMenuController@sysMenuInfo'); // 获取某个菜单信息
    Router::post('menu/save', 'App\Controller\Admin\SysMenuController@sysMenuSave'); // 保存Menu
    Router::post('menu/update', 'App\Controller\Admin\SysMenuController@sysMenuUpdate'); // 更新Menu
    Router::post('menu/delete/{id:\d+}', 'App\Controller\Admin\SysMenuController@sysMenuDelete'); // 更新Menu

    Router::get('user/info', 'App\Controller\Admin\SysUserController@getInfoByLoginUserId'); // 登录的用户信息
    Router::get('user/info/{id:\d+}', 'App\Controller\Admin\SysUserController@getInfoByUserId'); // 获取用户信息
    Router::get('user/list', 'App\Controller\Admin\SysUserController@sysUserList'); // 管理员用户列表
    Router::post('user/save', 'App\Controller\Admin\SysUserController@sysUserSave'); // 保存管理员
    Router::post('user/update', 'App\Controller\Admin\SysUserController@sysUserUpdate'); // update管理员
    Router::post('user/delete', 'App\Controller\Admin\SysUserController@sysUserDelete'); // 删除管理员
    Router::post('user/password', 'App\Controller\Admin\SysUserController@password'); // 修改密码

    Router::get('role/list', 'App\Controller\Admin\SysRoleController@sysRoleList'); // 角色管理列表
    Router::get('role/select', 'App\Controller\Admin\SysRoleController@sysRoleSelect'); // select角色列表
    Router::get('role/info/{id:\d+}', 'App\Controller\Admin\SysRoleController@sysRoleInfo'); // 获取角色信息
    Router::post('role/save', 'App\Controller\Admin\SysRoleController@sysRoleSave'); // 新增角色
    Router::post('role/update', 'App\Controller\Admin\SysRoleController@sysRoleUpdate'); // 更新角色
    Router::post('role/delete', 'App\Controller\Admin\SysRoleController@sysRoleDelete'); // 删除角色

    Router::get('log/list', 'App\Controller\Admin\SysLogController@sysLogList'); // 日志列表

    Router::get('oss/list', 'App\Controller\Admin\SysOssController@sysOssList'); // OSS列表
    Router::get('oss/config', 'App\Controller\Admin\SysOssController@sysOssConfig'); // OSS配置
    Router::post('oss/delete', 'App\Controller\Admin\SysOssController@sysOssDelete'); // OSS删除
    Router::post('oss/saveConfig', 'App\Controller\Admin\SysOssController@sysOssSaveConfig'); // 保存OSS配置
    Router::post('oss/upload', 'App\Controller\Admin\SysOssController@sysOssUpload'); // 上传文件

    Router::get('config/list', 'App\Controller\Admin\SysConfigController@sysConfigList'); // 参数列表
    Router::get('config/info/{id:\d+}', 'App\Controller\Admin\SysConfigController@sysConfigInfo'); // 获取参数
    Router::post('config/save', 'App\Controller\Admin\SysConfigController@sysConfigSave'); // 新增参数
    Router::post('config/update', 'App\Controller\Admin\SysConfigController@sysConfigUpdate'); // 新增参数
    Router::post('config/delete', 'App\Controller\Admin\SysConfigController@sysConfigDelete'); // 删除参数

    Router::post('logout', 'App\Controller\Admin\SysUserController@sysLogout'); // 退出登录
},
    ['middleware' => [App\Middleware\Admin\AdminAuthMiddleware::class]]
);
