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

//管理员登录
Router::post('/renren-fast/sys/login', 'App\Controller\Admin\SysUserController@login');

Router::addGroup('/renren-fast/', function () {
    Router::get('sys/menu/nav', 'App\Controller\Admin\SysUserController@menu_nav');
    Router::get('sys/user/info', 'App\Controller\Admin\SysUserController@info');
},
    ['middleware' => [App\Middleware\AdminMiddleware::class]]
);
