<?php

declare(strict_types=1);

namespace Sande\Contract;

interface BaseNotifyInterface
{
    public function isOk():bool;

    public function getMsg(): string;

    public function getOrderNo(): string;

    public function getMid():string;
}