<?php

namespace Sande;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Sande\Exception\DecryptException;
use Sande\Exception\EncryptException;
use Sande\Exception\RequestException;
use Sande\Exception\VerifyException;

class Elec
{
    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var string
     */
    private $privateKey;

    private $baseUrl = 'https://cap.sandpay.com.cn';

    /**
     * @var string
     */
    private $password;

    private $merNo;

    private $client;

    public function __construct(string $merNo,string $publicKey,string $privateKey,string $password)
    {
        $this->merNo = $merNo;
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->password = $password;
        $this->client = new Client();
    }

    /**
     * 对账单查询
     * @param string $orderNo
     * @param string $date
     * @param string $type
     * @return array
     * @throws DecryptException
     * @throws EncryptException
     * @throws GuzzleException
     * @throws RequestException
     * @throws VerifyException
     */
    public function billQuery(string $orderNo,string $date,string $type = '01'): array
    {
        $uri = '/v4/electrans/ceas.elec.trans.bill.query';
        return $this->request($this->getUrl($uri),compact('orderNo','date','type'));
    }


    /**
     * 企业向个人转账
     * @throws VerifyException
     * @throws EncryptException
     * @throws GuzzleException
     * @throws RequestException
     * @throws DecryptException
     */
    public function transfer(string $accountType, string $orderAmt, string $bizUserNo, string $name, array $options = []): array
    {
        $payee = compact('bizUserNo','name');
        $uri = '/v4/electrans/ceas.elec.trans.corp.transfer';
        return $this->request(
            $this->getUrl($uri),
            array_merge(compact('accountType','orderAmt','payee'),$options)
        );
    }

    /**
     * 用户开户信息查询
     * @param string $bizUserNo
     * @return array
     * @throws DecryptException
     * @throws EncryptException
     * @throws GuzzleException
     * @throws RequestException
     * @throws VerifyException
     */
    public function infoQuery(string $bizUserNo): array
    {
        $uri = '/v4/elecaccount/ceas.elec.member.info.query';
        return $this->request(
            $this->getUrl($uri),
            compact($bizUserNo)
        );
    }

    /**
     * @param string $bizUserNo
     * @param string $notifyUrl
     * @param string $frontUrl
     * @param string $bizType
     * @return array
     * @throws DecryptException
     * @throws EncryptException
     * @throws GuzzleException
     * @throws RequestException
     * @throws VerifyException
     */
    public function userModify(string $bizUserNo,string $notifyUrl,string $frontUrl,string $bizType = 'CLOSE'): array
    {
        $uri = '/v4/elecaccount/ceas.elec.account.member.status.modify';
        return $this->request(
            $this->getUrl($uri),
            compact('bizUserNo','bizType','notifyUrl','frontUrl')
        );
    }

    
    /**
     * @throws GuzzleException
     * @throws EncryptException
     * @throws RequestException
     * @throws DecryptException
     * @throws VerifyException
     */
    private function request(string $url, array $data):array
    {
        $public = loadX509Cert($this->publicKey);
        $private = loadPk12Cert($this->privateKey,$this->password);
        // step1: 拼接报文
        $data = array_merge($this->commonConfig(),$data);
        // step2: 生成AESKey并使用公钥加密 先生成16位随机字符串aesKey，并且转成aesKeyBytes字节数组
        $AESKey = aes_generate(16);
        // step3: 使用AESKey加密报文 通过aesKeyBytes对Json进行AES加密生成data
        $data['data'] = AESEncrypt($data, $AESKey);
        // step4: 把aesKeyBytes通过杉德公钥加密生成encryptKey，encryptType为"AES"
        $data['encryptKey'] = RSAEncryptByPub($AESKey, $public);
        // step5: 将加密后的data，通过商户私钥进行签名生成sign，signType为"SHA1WithRSA"
        $data['sign'] = sign($data['data'], $private);
        // step6: post请求
        $response = $this->client->request('POST',$url,[
            'body' => json_encode($data),
            'headers' => [
                'content-type' => 'application/json'
            ]
        ]);
        if ($response->getStatusCode() != 200) {
            throw new RequestException("http状态码不正确");
        }
        $content = $response->getBody()->getContents();
        $result =  json_decode($content, true);
        if (empty($result)) {
            throw new RequestException("解析返回数据失败" . $content);
        }
        // step8: 使用公钥验签报文$decryptPlainText
        $verify = verify($result['data'], $result['sign'], $public);
        if ($verify != 1) {
            throw new VerifyException("验签失败");
        }
        // step9: 使用私钥解密AESKey
        $decryptAESKey = RSADecryptByPri($result['encryptKey'], $private);
        // step10: 使用解密后的AESKey解密报文
        $decryptPlainText = AESDecrypt($result['data'], $decryptAESKey);
        return  json_decode($decryptPlainText,true);
    }

    private function getUrl($uri)
    {
        return $this->baseUrl . $uri;
    }

    private function commonConfig(): array
    {
        return [
            'version'           =>  '1.0',
            //  商户号
            'mid'               =>  $this->merNo,
            //  签名方式
            'signType'          =>  'SHA1WithRSA',
            // 	加密方式
            'encryptType'       =>  'AES',
            //  时间戳
            'timestamp'         =>  date('Y-m-d H:i:s'),
        ];
    }
}