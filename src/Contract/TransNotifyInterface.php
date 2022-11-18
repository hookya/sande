<?php

declare(strict_types=1);

namespace Sande\Contract;

interface TransNotifyInterface extends BaseNotifyInterface
{
    public function getTransType():string;

    public function getStatus():string;

    public function getStatusOk():bool;

    public function getPayerInfo():array;

    public function getPayerAcc():string;

    public function getPayerMemId():string;

    public function getPayerName():string;

    public function getPayeeInfo():array;

    public function getAmount():string;

    public function getPayeeList():array;

    public function getRemark():string;

    public function getPostscript():string;

}