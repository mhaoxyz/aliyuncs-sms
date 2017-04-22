<?php

namespace Mhaoxyz\Aliyuncs\SMS;


use anlutro\cURL\cURL;

class SMS extends Api
{
    protected $url = 'https://sms.aliyuncs.com';

    protected $action = 'SingleSendSms';

    protected $sign_name, $template_code, $rec_num = [], $param_strings = [];

    /**
     * 短信签名(系统中已验证通过)
     * @param $sign_name
     */
    public function setSignName($sign_name)
    {
        $this->sign_name = $sign_name;
    }

    public function getSignName()
    {
        return $this->sign_name;
    }

    /**
     * 设置使用的短信模版编号
     * @param $template_code
     */
    public function setTemplateCode($template_code)
    {
        $this->template_code = $template_code;
    }

    public function getTemplateCode()
    {
        return $this->template_code;
    }

    public function setRecNum($rec_num)
    {
        if (is_array($rec_num)) {
            $this->rec_num = $rec_num;
        } else {
            $this->rec_num = [$rec_num];
        }
    }

    public function getRecNum()
    {
        return $this->rec_num;
    }

    public function pushRecNum($rec_num)
    {
        array_push($this->rec_num, $rec_num);
    }

    public function setParamStrings(array $param_strings)
    {
        $this->param_strings = $param_strings;
    }

    public function getParamStrings()
    {
        return $this->param_strings;
    }

    public function setParamString($key, $param)
    {
        $this->param_strings[$key] = $param;
    }

    public function getParamString($key)
    {
        return $this->param_strings[$key];
    }

    protected function arrToStr(array $arr)
    {
        $str = '';
        foreach ($arr as $item) {
            if (!empty($str)) {
                $str .= ',';
            }
            $str .= $item;
        }
        return $str;
    }

    public function getParams($others = [])
    {
        $params = parent::getParams([
            'Action' => $this->action,
            'SignName' => $this->getSignName(),
            'TemplateCode' => $this->getTemplateCode(),
            'RecNum' => strval($this->arrToStr($this->getRecNum())),
            'ParamString' => json_encode($this->getParamStrings())
        ]);
        return $params;
    }

    /**
     * @return bool
     */
    public function send()
    {
        $curl = new cURL();
        $params = $this->getParams();
        $request = $curl->newRequest('post', $this->url, $params)->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $response = $request->send();
        $body = json_decode($response->body, true);
        if (!empty($body['Model']) && !empty($body['RequestId']) && !isset($body['HostId']) && !isset($body['Message']) && !isset($body['Code'])) {
            return true;
        } else {
            return false;
        }
    }
}