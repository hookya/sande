<?php

namespace Sande;

class ProductCode
{
    //产品编码 产品编码: 云函数h5：02010006；支付宝H5：02020002；微信公众号H5：02010002；
    //一键快捷：05030001；H5快捷：06030001；支付宝扫码：02020005  ；快捷充值：  06030003;

    const ALIPAY_WEB_CODE = '02020002';

    const WXPAY_WEB_CODE = '02010002';

    const ONE_CLICK_FAST_CODE = '05030001';

    const WEB_FAST_CODE = '06030001';

    const ALIPAY_SCAN_CODE = '02020005';

    const FAST_RECHARGE_CODE = '06030003';

    // 电子钱包【云账户】：开通账户并支付product_code应为：04010001；消费（C2C）product_code 为：04010003 ; 我的账户页面 product_code 为：00000001

    const CLOUD_OPEN_ACC_AND_PAY = '04010001';

    const CLOUD_CONSUME_C2C = '04010003';

    const CLOUD_CONSUME_C2B = '04010001';
    // c2c转账担保消费
    const CLOUD_CONSUME_DC2C = '04010004';

    const CLOUD_ACCOUNT = '00000001';
}