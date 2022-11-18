<?php

namespace Sande;

use Sande\Contract\NotifyInterface;

class PaymentNotify implements NotifyInterface
{

    private $orderNo = '';

    private $respCode = '';

    private $payTime = '';

    private $amount = '';

    private $data = [];


    public function __construct(array $data)
    {
        $this->orderNo = $data['body']['orderCode'] ?? '';
        $this->respCode = $data['head']['respCode'] ?? '';
        $this->amount = $data['body']['totalAmount'] ?? '0';
        $this->payTime = $data['body']['payTime'] ?? '';
        $this->data = $data;
    }

    public function getOrderNo(): string
    {
        return $this->orderNo;
    }

    public function isOk(): bool
    {
        return $this->respCode == '000000';
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getPayTime(): string
    {
        return $this->payTime;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getMsg(): string
    {
        return $this->getData()['head']['respMsg'] ?? '';
    }

    public function getMid(): string
    {
        return $this->getData()['body']['mid'] ?? '';
    }
}