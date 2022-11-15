<?php

declare(strict_types=1);

namespace Sande;

use Exception;
use Sande\Contract\Notify;
use Sande\Exception\InvalidArgumentException;
use Sande\Exception\VerifyException;

class Sande
{

    protected $baseUrl = 'https://sandcash.mixienet.com.cn/pay/h5/fastpayment';

    protected $merNo = '';

    protected $merKey = '';

    protected $md5Key = '';

    // 公钥地址
    protected $publicKey = '';

    protected $privateKey = '';

    /**
     * @param array $configs
     */
    public function __construct(array $configs = [])
    {
        $this->merNo = $configs['merNo'] ?? '';
        $this->merKey = $configs['merKey'] ?? '';
        $this->md5Key = $configs['md5Key'] ?? '';
    }

    /**
     * 进入云账户页面.
     * @param string $userId
     * @param string $nickname
     * @param string $orderNo
     * @param string $notify
     * @param string $returnUrl
     * @return string
     */
    public function cloudPage(string $userId,string $nickname,string $orderNo,string $notify,string $returnUrl): string
    {
        if ($userId == '') {
            throw new InvalidArgumentException("用户id不能为空");
        }

        if ($nickname == '') {
            throw new InvalidArgumentException("用户昵称不能为空");
        }

        $payExtra = compact('userId','nickname');


        return $this->buildPageUrl(
            $this->baseUrl,
            ProductCode::CLOUD_ACCOUNT,
            $orderNo,
            '0.01',
            '',
            $notify,
            $returnUrl,
            json_encode($payExtra)
        );
    }

    /**
     * 一键快捷支付
     * @param string $userId
     * @param string $orderNo
     * @param string $amount
     * @param string $goodsName
     * @param string $notifyUrl
     * @param string $returnUrl
     * @return string
     */
    function oneClickFast(string $userId,string $orderNo,string $amount, string $goodsName, string $notifyUrl, string $returnUrl):string {
        if ((float)$amount <= 0.0) {
            throw new InvalidArgumentException("订单号金额不能小于0");
        }

        if ($goodsName == '') {
            throw new InvalidArgumentException("商品名称不能为空");
        }
        if ($userId == '') {
            throw new InvalidArgumentException("用户id不能为空");
        }
        $payExtra = [
            'userId' => $userId,
        ];
        return $this->buildPageUrl($this->baseUrl,
            ProductCode::ONE_CLICK_FAST_CODE,
                        $orderNo,
                        $amount,
                        $goodsName,
                        $notifyUrl,
                        $returnUrl,
                        json_encode($payExtra));
    }


    /**
     * @param array $data
     * @param string|null $publicKey
     * @return Notify
     * @throws VerifyException
     */
    public function notify(array $data,?string $publicKey = ""): Notify
    {
        $sign = $data['sign'] ?? '';
        if ($this->verify($data['data'], $sign,$publicKey)) {
            return new PaymentNotify(json_decode($data['data'],true));
        }
        throw new VerifyException("验签失败");
    }

    /**
     * @param string $publicKey
     * @return Sande
     */
    public function setPublicKey(string $publicKey):self
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    /**
     * 创建请求页面的url地址
     * @param string $baseUrl 基础url
     * @param string $productCode 产品标号
     * @param string $orderNo 订单号
     * @param string $amount 订单金额
     * @param string $goodsName 商品名称
     * @param string $notifyUrl 通知地址
     * @param string $returnUrl 返回地址
     * @param string $payExtra 扩展数据
     */
    private function buildPageUrl(string $baseUrl, string $productCode, string $orderNo,
                                  string $amount, string $goodsName, string $notifyUrl,
                                  string $returnUrl, string $payExtra): string
    {

        $this->checkBaseParam();

        if ($productCode == '') {
            throw new InvalidArgumentException("产品编号不能为空");
        }

        if ($orderNo == '') {
            throw new InvalidArgumentException("订单号不能为空");
        }

        if ($notifyUrl == '') {
            throw new InvalidArgumentException("回调地址不能为空");
        }

        $data = [
            'version' => '10',
            'mer_no' => $this->merNo, // 商户号
            'mer_key' => $this->merKey, // 商户私钥通过安卓APK工具解析出来的KEY1
            'mer_order_no' => $orderNo,
            'create_time' => date('YmdHis'),
            'order_amt' => $amount,
            'expire_time' => date('YmdHis', time() + 9 * 60),
            'notify_url' => $notifyUrl, // 订单支付异步通知
            'return_url' => $returnUrl, // 订单前端页面跳转地址
            'pay_extra' => $payExtra,
            'create_ip' => '127_0_0_1',
            'goods_name' => $goodsName,
            'store_id' => '000000',
            'product_code' => $productCode, // 产品编码
            'clear_cycle' => '3',
            'accsplit_flag' => 'NO',
            'jump_scheme' => 'sandcash://scpay',
            'meta_option' => json_encode([['s' => 'Android', 'n' => 'wxDemo', 'id' => 'com.pay.paytypetest', 'sc' => 'com.pay.paytypetest']]),
            'sign_type' => 'MD5',
        ];
        $temp = $data;
        unset($temp['goods_name'], $temp['jump_scheme'], $temp['expire_time'], $temp['product_code'], $temp['clear_cycle'], $temp['meta_option']);
        $sign = strtoupper(md5($this->getSignContent($temp) . '&key=' . $this->md5Key));  // key对应商户私钥通过安卓APK工具解析出来的MD5KEY
        $data['sign'] = $sign;
        $query = http_build_query($data);
        // 返回url
        return $baseUrl . '?' . $query;
    }

    /**
     * 检查基础的配置参数
     * @throws InvalidArgumentException
     */
    private function checkBaseParam()
    {
        if ($this->merNo == '') {
            throw new InvalidArgumentException("商户号为空");
        }

        if ($this->merKey == '') {
            throw new InvalidArgumentException("商户key为空");
        }

        if ($this->md5Key == '') {
            throw new InvalidArgumentException("加密秘钥为空");
        }
    }


    /**
     * 检查参数是否为空.
     * @param $value
     * @return bool
     */
    private function checkEmpty($value): bool
    {
//        echo $value;
        if (! isset($value)) {
            return true;
        }
        if (trim($value) === '') {
            return true;
        }

        return false;
    }

    /**
     * 获取签名内容.
     * @param $params
     * @return string
     */
    private function getSignContent($params): string
    {
        ksort($params);
        $tmp = [];
        foreach ($params as $k => $v) {
            if (! $this->checkEmpty($v)) {
                $tmp[] = $k . '=' . $v;
            }
        }
        return implode('&', $tmp);
    }

    /**
     * 验签.
     */
    public function verify(string $plainText, string $sign,?string $publicKey = ''): bool
    {
        if ($publicKey == '') {
            $publicKey = $this->publicKey;
        }
        try {
            $resource = openssl_pkey_get_public($this->publicKey($publicKey));
            $result = openssl_verify($plainText, base64_decode($sign), $resource);
            openssl_free_key($resource);
            if (! $result) {
                return false;
            }
            if ($result == -1) {
//                Log::StdLogger()->error(date('Y-m-d H:i:is', time()) . '验签错误');
                return false;
            }
            if ($result == 0) {
//                Log::StdLogger()->error(date('Y-m-d H:i:is', time()) . '验签错误');
                return false;
//                throw new Exception('openssl 内部错误');
            }
            return true;
        } catch (Exception $e) {
//            Log::StdLogger()->error(date('Y-m-d H:i:s', time()) . '验签错误' . $e->getMessage());
            return false;
        }
    }

    /**
     * @throws VerifyException
     * @throws InvalidArgumentException
     * @return mixed
     */
    private function publicKey(string $path)
    {
        if ($path == '') {
            throw new InvalidArgumentException("公钥地址不正确");
        }
        $file = file_get_contents($path);
        if (! $file) {
            throw new VerifyException('getPublicKey::file_get_contents ERROR');
        }
        $cert = chunk_split(base64_encode($file), 64, "\n");
        $cert = "-----BEGIN CERTIFICATE-----\n" . $cert . "-----END CERTIFICATE-----\n";
        $res = openssl_pkey_get_public($cert);
        $detail = openssl_pkey_get_details($res);
        openssl_free_key($res);
        if (! $detail) {
            throw new VerifyException('getPublicKey::openssl_pkey_get_details ERROR');
        }
        return $detail['key'];
    }

}