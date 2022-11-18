<?php

declare(strict_types=1);

namespace Sande\Contract;

interface AccNotifyInterface extends BaseNotifyInterface
{

    public function getBizType():string;

    public function getBizUser():string;

    public function getUserInfo():array;

    public function getPwdInfo():array;

    public function getSignProtocolInfo():array;

    public function getCardInfo():array;

    public function getVerifyInfo():array;
}