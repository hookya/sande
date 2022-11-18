<?php

declare(strict_types=1);

namespace Sande;

use Sande\Contract\AccNotifyInterface;

class AccNotify implements
    AccNotifyInterface
{

    protected $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getBizType(): string
    {
        return $this->data['bizType'] ?? '';
    }

    public function getBizUser(): string
    {
        return $this->data['bizUserNo'] ?? '';
    }

    public function getUserInfo(): array
    {
        return $this->data['userInfo'] ?? [];
    }

    public function getPwdInfo(): array
    {
        return $this->data['pwdInfo'] ?? [];
    }

    public function getSignProtocolInfo(): array
    {
        return $this->data['ignProtocolInfo'] ?? [];
    }

    public function getCardInfo(): array
    {
        return $this->data['cardInfo'] ?? [];
    }

    public function getVerifyInfo(): array
    {
        return $this->data['verifyInfo'] ?? [];
    }

    public function isOk(): bool
    {
        return $this->data['respCode'] == '000000';
    }

    public function getMsg(): string
    {
        return $this->data['respMsg'] ?? '';
    }

    public function getOrderNo(): string
    {
        return $this->data['orderNo'] ?? '';
    }

    public function getMid(): string
    {
        return $this->data['mid'] ?? '';
    }
}