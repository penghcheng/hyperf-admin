<?php

declare(strict_types=1);

namespace App\Kernel\Util;

use App\Constants\ErrorCode;
use App\Dao\SysUserDao;
use App\Exception\BusinessException;
use App\Model\SysUser;
use Firebase\JWT\JWT;
use Hyperf\Utils\Traits\StaticInstance;

class JwtInstance
{
    use StaticInstance;

    const KEY = 'Hyperf-admin';

    /**
     * @var int
     */
    public $user_id;

    /**
     * @var SysUser
     */
    public $sysUser;

    public function encode(SysUser $sysUser)
    {
        $this->user_id = $sysUser->user_id;
        $this->sysUser = $sysUser;

        return JWT::encode([
            'iss' => 'xxx.com', //签发者 可选
            'iat' => time(), //签发时间
            'exp' => time() + config("sys_token_exp"),
            'id' => $sysUser->user_id
        ], self::KEY);
    }

    public function decode(string $token): self
    {
        try {
            $decoded = (array)JWT::decode($token, self::KEY, ['HS256']);
        } catch (\Firebase\JWT\SignatureInvalidException $e) {  //签名不正确
            logger('jwt')->error($e->getMessage());
            throw new BusinessException(ErrorCode::TOKEN_INVALID, $e->getMessage());
        } catch (\Firebase\JWT\ExpiredException $e) {  // token过期
            logger('jwt')->error($e->getMessage());
            throw new BusinessException(ErrorCode::TOKEN_INVALID, $e->getMessage());
        } catch (\Throwable $e) {
            logger('jwt')->error($e->getMessage());
            throw new BusinessException(ErrorCode::SERVER_ERROR, $e->getMessage());
        }

        if ($id = $decoded['id'] ?? null) {
            $this->user_id = $id;
            $this->sysUser = di(SysUserDao::class)->first($id);
        }
        return $this;
    }

    public function build(): self
    {
        if (empty($this->user_id)) {
            throw new BusinessException(ErrorCode::TOKEN_INVALID);
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->user_id;
    }

    /**
     * @return SysUser
     */
    public function getSysUser(): ?SysUser
    {
        if ($this->sysUser === null && $this->user_id) {
            $this->sysUser = di(SysUserDao::class)->first($this->user_id);
        }
        return $this->sysUser;
    }
}
