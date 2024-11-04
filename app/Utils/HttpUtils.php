<?php
namespace App\Utils;

class HttpUtils
{

    /**
     * 发送GET请求
     *
     * @param  string  $url
     * @return bool|string
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/1 上午8:34
     */
    public static function get(string $url, $json=true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        if($json) {
            return json_decode($output, true);
        }
        return $output;
    }

    /**
     * 发送POST请求
     *
     * @param  string  $url
     * @param  array  $data
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/1 上午8:44
     */
    public static function post(string $url, array $data, array $headers = []){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        if(! empty($headers)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        //打印获得的数据
        return json_decode($output,true);;
    }

    /**
     * 发送json请求
     *
     * @param  string  $url
     * @param  array  $data
     * @return bool|string
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/1 上午8:34
     */
    public static function postJson(string $url, array $data=[])
    {
        $payload = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload))
        );
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result,true);
    }
}