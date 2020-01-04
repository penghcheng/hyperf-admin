<?php
/**
 * Created by PhpStorm.
 * User: derek
 * Date: 2019/11/11
 * Time: 22:09
 */

namespace App\Annotation;


use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * @Annotation
 * @Target({"METHOD","CLASS"})
 */
class SysLogAnnotation extends AbstractAnnotation
{
}