<?php

declare(strict_types=1);

namespace Sande;

class Config
{
    /**
     * @return array
     */
    public static function getCloudC2COptions(): array
    {
        return [
            'remark' => '',
            'userFeeAmt' => '0',
            'postscript' => "",
        ];
    }
}