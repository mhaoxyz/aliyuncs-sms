<?php
/**
 * Created by PhpStorm.
 * User: menghao
 * Date: 2017/3/25
 * Time: 下午3:53
 */

namespace Mhaoxyz\Aliyuncs\SMS;


class Api
{

    // Important
    protected $access_key_id = '';
    protected $access_key_secret = '';
    protected $format = 'JSON';
    protected $version = '2016-09-27';
    protected $signature_method = 'HMAC-SHA1';
    protected $timestamp;
    protected $signature_version = '1.0';
    protected $signature_nonce;

    // Not important

    protected $region_id ;

    public function __construct($access_key_id, $access_key_secret)
    {
        $this->timestamp = date('Y-m-d\TH:i:s\Z');
        $this->signature_nonce = md5(time() . rand(0, 9999));
//        $this->signature_nonce = '9e030f6b-03a2-40f0-a6ba-157d44532fd0';

        $this->access_key_id = $access_key_id;
        $this->access_key_secret = $access_key_secret;
    }

    public function setRegionId($region_id)
    {
        $this->region_id = $region_id;
    }

    public function getRegionId()
    {
        return $this->region_id;
    }

    public function getParams($others = [])
    {
        $params = [
            "Format" => $this->format,
            "Version" => $this->version,
            "AccessKeyId" => $this->access_key_id,
            "SignatureMethod" => $this->signature_method,
            "Timestamp" => $this->timestamp,
            "SignatureVersion" => $this->signature_version,
            "SignatureNonce" => $this->signature_nonce,
        ];

        if (isset($this->region_id)) {
            $params["RegionId"] = $this->getRegionId();
        }

        $params = array_merge($params, $others);

        $params['Signature'] = $this->sign($params);

        return $params;
    }

    public function sign(array $params)
    {
        ksort($params);

        $sign_str = '';

        foreach ($params as $index => $param) {
            if (!empty($sign_str)) {
                $sign_str .= $this->urlEncode('&');
            }
            $sign_str .= $this->urlEncode("{$this->urlEncode($index)}={$this->urlEncode($param)}");
        }

        $sign_str = 'POST&' . $this->urlEncode('/') . '&' . $sign_str;

        return (base64_encode(hash_hmac('sha1', $sign_str, $this->access_key_secret . '&', true)));
    }

    public function urlEncode($url)
    {
        $url = str_replace([
            '+', '*', '%7E'
        ], [
            '%20', '%2A', '~'
        ], urlencode($url));
        return $url;
    }

    public function composeUrl($url, $params)
    {
        foreach ($params as $index => $param) {
            if (strpos($url, '?')) {
                $url .= '&';
            } else {
                $url .= '?';
            }
            $url .= "{$index}={$param}";
        }
        return $url;
    }
}