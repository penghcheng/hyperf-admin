<?php

declare(strict_types=1);

use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');

Router::post('/renren-fast/sys/login', 'App\Controller\Admin\SysUserController@login'); //管理员登录

Router::addGroup('/renren-fast/', function () {
    Router::get('sys/menu/nav', 'App\Controller\Admin\SysUserController@menu_nav'); // 登录用户的菜单和权限
    Router::get('sys/user/info', 'App\Controller\Admin\SysUserController@info'); // 登录的用户信息
    Router::get('sys/user/list', 'App\Controller\Admin\SysUserController@sysUserList'); // 管理员用户列表
},
    ['middleware' => [App\Middleware\AdminMiddleware::class]]
);
