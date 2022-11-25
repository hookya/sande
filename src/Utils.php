<?php

declare(strict_types=1);

namespace Sande;

class Utils {

    /**
     * @param array $template
     * @param array $config
     * @return array
     */
    public static function filterConfig(array $template,array $config):array
    {

        $keys = array_keys(array_diff_key($config,$template));
        foreach ($keys as $key) {
            unset($config[$key]);
        }
        return array_merge($template,$config);
    }
}
//
//$template = [
//    'a' => 1,
//    'b' => 2,
//    'c' => 3,
//    'd' => 4,
//    'e' => 5,
//];
//
//
//$config = [
//    'a' => 6,
//    'g' => 7,
//    'gg'=> 8,
//];
//
//$result = Utils::filterConfig($template,$config);
//
//print_r($result);

//$template = [
//    'remark' => '',
//    'userFeeAmt' => '0',
//    'postscript' => "",
//];
//
//$conf = [
//    'userFeeAmt' => '1'
//];
//
//$result = Utils::filterConfig($template,$conf);
//
//print_r($result);
