<?php

namespace Redrain\Express;

class LaravelExpress
{

    protected $hostExpressName; //获取快递服务商
    protected $hostExpressNo;   //获取快递运单流转信息

    public function __construct()
    {
        $this->hostExpressName = 'http://www.kuaidi100.com/autonumber/autoComNum?text=';
        $this->hostExpressNo = 'http://www.kuaidi100.com/query?type=';  //zhongtong&postid=447974544163
    }

    /**
     * 获取快递商家
     * @param string $no
     * @return string
     */
    public function getExpressNameByNo($no)
    {
        $url = $this->hostExpressName . $no;
        $content = self::curlHttpGet($url);
        $content = json_decode($content, true);
        return $content['auto'][0]['comCode'];
    }

    /**
     * 获取快递信息
     * @param $no
     * @return string
     */
    public function getExpressInfoByNo($no)
    {

        $expressName = $this->getExpressNameByNo($no);
        $url = $this->hostExpressNo . $expressName . '&postid='. $no;
        $content = self::curlHttpGet($url);
        return $content;

    }

    /**
     * curl GET 请求
     * @param $url
     * @param $timeOut
     * @param bool $ssl
     * @param array $header
     * @return mixed
     */
    public static function curlHttpGet($url, $timeOut = 5, $ssl = false, $header = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url); //设置链接
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeOut);
        if ($ssl) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            app('log')->info('HttpUtil_curlHttpGet', ['url' => $url, 'error' => $err]);
        } else {
            app('log')->info('HttpUtil_curlHttpGet', ['url' => $url, 'response' => $response]);

            return $response;
        }

    }
}