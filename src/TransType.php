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

class TransType
{
    // 交易类通知 充值
    public const DEPOSIT = 'DEPOSIT';

    // 交易类通知 B2C转账
    public const B2C_TRANSFER = 'B2C_TRANSFER';

    // 交易类通知 C2C转账
    public const C2C_TRANSFER = 'C2C_TRANSFER';

    // 交易类通知 C2B转账
    public const C2B_TRANSFER = 'C2B_TRANSFER';

    // 交易类通知 提现
    public const WITHDRAW = 'WITHDRAW';
    // RETURN_CARD 退卡
    //

    /**
     * 红包发放.
     */
    public const HB_SEND = 'HB_SEND';

    /**
     * 转账退回.
     */
    public const TRANSFER_RETURN = 'TRANSFER_RETURN';

    /**
     * 确认收款.
     */
    public const CONFIRM_RECEIPT = 'CONFIRM_RECEIPT';

    /**
     * 会员付款.
     */
    public const PAYMENT = 'PAYMENT';

    private const _map = [
        self::DEPOSIT => 1,
        self::B2C_TRANSFER => 2,
        self::C2C_TRANSFER => 3,
        self::C2B_TRANSFER => 4,
        self::WITHDRAW => 5,
        self::HB_SEND => 6,
        self::TRANSFER_RETURN => 7,
        self::CONFIRM_RECEIPT => 8,
        self::PAYMENT => 9,
    ];

    /**
     * @return string[] 交易类通知类型
     */
    public static function getTransTypes(): array
    {
        return [
            self::B2C_TRANSFER,
            self::C2B_TRANSFER,
            self::C2C_TRANSFER,
            self::DEPOSIT,
            self::WITHDRAW,
        ];
    }

    /**
     * 获取类型的编码
     */
    public static function getCode(string $type): int
    {
        return self::_map[$type] ?? -1;
    }
}
