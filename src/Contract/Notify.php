<?php

namespace Sande\Contract;

interface Notify
{
    /**
     * 获取订单号
     * @return string
     */
    public function getOrderNo():string;

    /**
     * 是否成功
     * @return bool
     */
    public function isOk():bool;

    public function getAmount():string;

    public function getPayTime():string;

    public function getData():array;
}