<?php

namespace Sande\Contract;

interface NotifyInterface extends BaseNotifyInterface
{
    public function getAmount():string;

    public function getPayTime():string;

    public function getData():array;
}