<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04/10/17
 * Time: 12:49 PM
 */
include_once "wx_config.php";

class Wx_AccessToken
{
    private $appid;
    private $secret;
    private $accessToken;
    private $Expires_in;


    /**
     * 构造函数
     * @param $code string 用于请求获取用户 openid 和 session_key 的 code
     * $pc = new Wx_S_Session($code);
     * $ret_msg = $pc->sSerssion();
     */
    public function Wx_AccessToken()
    {
        $this->appid = Wx_Config::PLATFORM_APPID;
        $this->secret = Wx_Config::PLATFORM_SECRET;
    }


    public function getAccessToken()
    {

        //get access_token;
        $api_url = sprintf("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s",
            $this->appid, $this->secret, $this->code);
        //echo "URL:$api_url\n";
        $resp_contents = file_get_contents($api_url);
        //echo "resp:$resp_contents\n";
        $resp_json = json_decode($resp_contents, TRUE);

        if (isset($resp_json["errcode"])) {
            $this->errcode = $resp_json["errcode"];
            $this->errmsg = $resp_json["errmsg"];
        } else {
            $this->accessToken = $resp_json["access_token"];

            $this->accessTokenExpires_in = $resp_json["expires_in"];
        }
        return $this->accessToken;
    }
}