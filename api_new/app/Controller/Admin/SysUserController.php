<?php
/**
 * Created by PhpStorm.
 * User: derek
 * Date: 2019/12/19
 * Time: 22:11
 */

namespace App\Controller\Admin;


use App\Controller\AbstractController;
use App\Request\LoginRequest;

class SysUserController extends AbstractController
{
    public function login(LoginRequest $request)
    {
        $username = (string)$request->input('username');
        $password = (string)$request->input('password');
    }
}