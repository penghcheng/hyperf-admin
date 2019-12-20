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

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class ErrorCode extends AbstractConstants
{
    /**
     * @Message("Server Error！")
     */
    const SERVER_ERROR = 500;

    /**
     * @Message("Token已失效")
     */
    const TOKEN_INVALID = 401;

    /**
     * @Message("用户没有注册")
     */
    const USER_NOT_REGIST = 701;

    /**
     * @Message("参数错误")
     */
    const PARAMS_INVALID = 1000;

    /**
     * @Message("用户不存在")
     */
    const USER_NOT_EXIST = 1001;

    /**
     * @Message("用户越权操作")
     */
    const USER_INVALID = 1002;

    /**
     * @Message("当前记录不存在")
     */
    const NOTE_NOT_EXIST = 1100;

    /**
     * @Message("涉嫌敏感话题，请修改后再试")
     */
    const SPAM_REJECT = 1201;
}
