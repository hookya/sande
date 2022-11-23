<?php

use Sande\Exception\DecryptException;
use Sande\Exception\EncryptException;

/**
 * 获取公钥
 * @param $path
 * @return mixed
 * @throws EncryptException
 */
function loadX509Cert($path)
{
    try {
        $file = file_get_contents($path);
        if (!$file) {
            throw new \Exception('loadx509Cert::file_get_contents ERROR');
        }
        $cert = chunk_split(base64_encode($file), 64, "\n");
        $cert = "-----BEGIN CERTIFICATE-----\n" . $cert . "-----END CERTIFICATE-----\n";
        $res = openssl_pkey_get_public($cert);
        $detail = openssl_pkey_get_details($res);
        openssl_free_key($res);

        if (!$detail) {
            throw new EncryptException('loadX509Cert::openssl_pkey_get_details ERROR');
        }

        return $detail['key'];
    } catch (\Exception $e) {
        throw new EncryptException($e->getMessage());
    }
}

/**
 * 获取私钥
 * @param $path
 * @param $pwd
 * @return mixed
 * @throws EncryptException
 */
function loadPk12Cert($path, $pwd)
{
    try {
        $file = file_get_contents($path);
        if (!$file) {
            throw new EncryptException('loadPk12Cert::file_get_contents');
        }

        if (!openssl_pkcs12_read($file, $cert, $pwd)) {
            throw new \Exception('loadPk12Cert::openssl_pkcs12_read ERROR');
        }
        return $cert['pkey'];
    } catch (\Exception $e) {
        throw new EncryptException($e->getMessage());
    }
}

/**
 * 私钥签名
 * @param $plainText
 * @param $path
 * @return string
 * @throws EncryptException
 */
function sign($plainText, $path):string
{
    try {
        $resource = openssl_pkey_get_private($path);
        $result = openssl_sign($plainText, $sign, $resource);
        openssl_free_key($resource);

        if (!$result) {
            throw new Exception('签名出错' . $plainText);
        }
        return base64_encode($sign);
    } catch (\Exception $e) {
        throw new EncryptException($e->getMessage());
    }
}

/**
 * 秘钥加密
 * Author: Tao.
 *
 * @param string $data 之前生成好的需加密内容
 * @param string $key 私钥证书位置(.pfx文件)
 * @param string $pwd 证书密码
 *
 * @return string
 * @throws EncryptException
 */
function SHA1withRSA($data, $key,$pwd)
{
    openssl_pkcs12_read(file_get_contents($key), $certs, $pwd);
    if (!$certs) {
        throw new EncryptException("获取文件失败");
    }
    $signature = '';
    openssl_sign($data, $signature, $certs['pkey']);
    return bin2hex($signature);
}

/**
 * 公钥验签
 * @param $plainText
 * @param $sign
 * @param $path
 * @return int
 * @throws EncryptException
 */
function verify($plainText, $sign, $path)
{
    $resource = openssl_pkey_get_public($path);
    $result = openssl_verify($plainText, base64_decode($sign), $resource);
    openssl_free_key($resource);

    if (!$result) {
        throw new EncryptException('签名验证未通过,plainText:' . $plainText . '。sign:' . $sign, '02002');
    }

    return $result;
}

/**
 * 公钥加密AESKey
 * @param $plainText
 * @param $puk
 * @return string
 * @throws EncryptException
 */
function RSAEncryptByPub($plainText, $puk): string
{
    if (!openssl_public_encrypt($plainText, $cipherText, $puk, OPENSSL_PKCS1_PADDING)) {
        throw new EncryptException('AESKey 加密错误');
    }

    return base64_encode($cipherText);
}

/**
 * 私钥解密AESKey
 * @param $cipherText
 * @param $prk
 * @return string
 * @throws DecryptException
 */
function RSADecryptByPri($cipherText, $prk): string
{
    if (!openssl_private_decrypt(base64_decode($cipherText), $plainText, $prk, OPENSSL_PKCS1_PADDING)) {
        throw new DecryptException('AESKey 解密错误');
    }

    return (string)$plainText;
}

/**
 * AES加密
 * @param $plainText
 * @param $key
 * @return string
 * @throws EncryptException
 */
function AESEncrypt($plainText, $key): string
{
    ksort($plainText);
    $plainText = json_encode($plainText);
    $len = openssl_cipher_iv_length("aes-128-cbc");
    $iv = openssl_random_pseudo_bytes($len);
    $result = openssl_encrypt($plainText, "aes-128-cbc", $key,OPENSSL_RAW_DATA,$iv);
    if (!$result) {
        throw new EncryptException('报文加密错误');
    }

    return base64_encode($result);
}

/**
 * AES解密
 * @param $cipherText
 * @param $key
 * @return string
 * @throws DecryptException
 */
function AESDecrypt($cipherText, $key): string
{
    $result = openssl_decrypt(base64_decode($cipherText), 'AES-128-ECB', $key, 1);

    if (!$result) {
        throw new DecryptException('报文解密错误', 2003);
    }

    return $result;
}

/**
 * 生成AESKey
 * @param $size
 * @return string
 */
function aes_generate($size)
{
    $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $arr = array();
    for ($i = 0; $i < $size; $i++) {
        $arr[] = $str[mt_rand(0, 61)];
    }

    return implode('', $arr);
}