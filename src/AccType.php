<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Sande;

class AccType
{
    /**
     * 注册开户.
     */
    public const REGISTER_ACCOUNT = 'REGISTER_ACCOUNT';

    /**
     * 绑定银行卡.
     */
    public const BIND_BANK_CARD = 'BIND_BANK_CARD ';

    // 设置密码
    public const SET_PASSWORD = 'SET_PASSWORD';

    // 修改密码
    public const CHANGE_PASSWORD = 'CHANGE_PASSWORD';

    // 协议签约
    public const SIGN_PROTOCOL = 'SIGN_PROTOCOL';

    // 附件上传
    public const FILE_UPLOAD = 'FILE_UPLOAD';

    // 一键开户
    public const ONE_STOP_REGISTER_ACCOUNT = 'ONE_STOP_REGISTER_ACCOUNT';

    // 协议解约
    public const CANCEL_PROTOCOL = 'CANCEL_PROTOCOL';

    // 重置会员手机号
    public const RESETING_USER_PHONE_NUMBER = 'RESETING_USER_PHONE_NUMBER ';

    // 快捷绑卡开户
    public const QUICK_BIND_REGISTER_ACCOUNT = 'QUICK_BIND_REGISTER_ACCOUNT ';

    // 销户
    public const CLOSE_ACCOUNT = 'CLOSE_ACCOUNT';

    // 人脸验证
    public const FACE_VERIFICATION = 'FACE_VERIFICATION';
}
