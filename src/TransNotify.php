<?php

namespace Sande;

use Sande\Contract\TransNotifyInterface;

class TransNotify implements TransNotifyInterface
{

    protected $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function isOk(): bool
    {
        return $this->data['respCode'] == '000000';
    }

    public function getMsg(): string
    {
        return $this->data['respMsg'];
    }

    public function getOrderNo(): string
    {
        return $this->data['orderNo'];
    }

    public function getMid(): string
    {
        return $this->data['mid'];
    }

    public function getTransType(): string
    {
        return $this->data['transType'];
    }

    public function getStatus(): string
    {
        return $this->data['orderStatus'];
    }

    public function getStatusOk(): bool
    {
        return $this->getStatus() == '00';
    }

    public function getPayerInfo(): array
    {
        return $this->data['payerInfo'];
    }

    public function getPayerAcc(): string
    {
        return $this->getPayerInfo()['payerAccNo'];
    }

    public function getPayerMemId(): string
    {
        return $this->getPayerInfo()['payerMemID'] ?? '';
    }

    public function getPayerName(): string
    {
        return $this->getPayerInfo()['payerAccName'] ?? '';
    }

    public function getPayeeInfo(): array
    {
        return $this->data['payeeInfo'] ?? [];
    }

    public function getAmount(): string
    {
        return $this->data['amount'] ?? '0';
    }

    public function getPayeeList(): array
    {
        return $this->data['payeeList'] ?? [];
    }

    public function getRemark(): string
    {
        return $this->data['remark'] ?? '';
    }

    public function getPostscript(): string
    {
        return $this->data['postscript'] ?? '';
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getUserFeeAmt(): string
    {
        return $this->data['userFeeAmt'] ?? (string)0.0;
    }

    public function getFeeAmt(): string
    {
        return $this->data['userFeeAmt'] ?? (string)0.0;
    }
}