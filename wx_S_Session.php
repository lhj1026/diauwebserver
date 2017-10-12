<?php

/**
 * User: Caesar
 * Date: 16-12-01
 * 功能：
 * 微信程序-获取用于与用户会话的安全session
 * 请求：wx_S_Session.php
 * {
 * "code":"",
 * }
 * 返回：
 * {
 * "code":"" ,                 //0：正确；40029:invalid code;
 * "msg":
 * //正常返回的JSON数据包
 * {
 * "openid":"",
 * "session_key":""
 * "s_session":""
 * "expires_in":2592000
 * }
 * //错误时返回JSON数据包(示例为Code无效)
 * {
 * "errmsg": "invalid code"
 * }
 * }
 */

include_once "wx_config.php";
global $sessionKey;
class Wx_S_Session
{
    private $appid;
    private $secret;
    private $grant_type;
    private $code;

    //从微信返回的信息
    private $openid;
    private $session_key;
    private $expires_in;
    private $errcode;
    private $errmsg;





    /**
     * 构造函数
     * @param $code string 用于请求获取用户 openid 和 session_key 的 code
     * $pc = new Wx_S_Session($code);
     * $ret_msg = $pc->sSerssion();
     */
    public function Wx_S_Session($code)
    {
        $this->appid = Wx_Config::APPID;
        $this->secret = Wx_Config::SECRET;
        $this->grant_type = Wx_Config::GRANT_TYPE;
        $this->code = $code;
    }



    /**
     * 通过code换取用户的 openid 和 session_key
     */
    private function codeSessionKey()
    {

        //get openID
        $api_url = sprintf("https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=%s",
            $this->appid, $this->secret, $this->code, $this->grant_type);
        //echo "URL:$api_url\n";
        $resp_contents = file_get_contents($api_url);
        //echo "resp:$resp_contents\n";
        $resp_json = json_decode($resp_contents, TRUE);
        //var_dump($resp_json);
        //$respErrcode=$resp_json["errcode"];


        if (isset($resp_json["errcode"])) {
            $this->errcode = $resp_json["errcode"];
            $this->errmsg = $resp_json["errmsg"];
        } else {
            $this->openid = $resp_json["openid"];
            $this->session_key = $resp_json["session_key"];
            $this->expires_in = $resp_json["expires_in"];
            $sessionKey=$this->session_key;
        }
        //echo "return:";
        //var_dump($this);
    }


    /**
     * 读取 linux服务器/dev/urandom生成随机数,若发生错误使用php的uniqid()函数
     * @param int $min
     * @param int $max
     * @return int
     */
    private function GetURandom($min = 0, $max = 0x7FFFFFFF)
    {
        $diff = $max - $min;
        if ($diff > PHP_INT_MAX) {
            // throw new RuntimeException('Bad Range');
            return uniqid();
        }

        $fh = fopen('/dev/urandom', 'r');
        stream_set_read_buffer($fh, PHP_INT_SIZE);
        $bytes = fread($fh, PHP_INT_SIZE);
        if ($bytes === false || strlen($bytes) != PHP_INT_SIZE) {
            // throw new RuntimeException("nable to get". PHP_INT_SIZE . "bytes");
            return uniqid();
        }
        fclose($fh);

        if (PHP_INT_SIZE == 8) {
            // 64-bit versions
            list($higher, $lower) = array_values(unpack('N2', $bytes));
            $value = $higher << 32 | $lower;
        } else {
            // 32-bit versions
            list($value) = array_values(unpack('Nint', $bytes));
        }

        $val = $value & PHP_INT_MAX;
        // convert to [0,1]
        $fp = (float)$val / PHP_INT_MAX;

        return (int)(round($fp * $diff) + $min);
    }


    /**
     * 返回用于与用户会话的安全session
     */
    public function sSerssion()
    {
        $ret_msg = array();

        $this->codeSessionKey();

        if (isset($this->errcode)) {
            $ret_code = $this->errcode;
            $ret_msg["errmsg"] = $this->errmsg;
        } else {
            $ret_code = "0";
            $ret_msg["openid"] = $this->openid;
            $ret_msg["session_key"] = $this->session_key;
            $ret_msg["expires_in"] = $this->expires_in;
            // PATH_SEPARATOR 是一个常量，在linux系统中是一个" : "号,Windows上是一个";"号。
            if (PATH_SEPARATOR == ';') {
                $ret_msg["session"] = sha1($this->openid . $this->session_key . (string)uniqid());
            } else {
                $ret_msg["session"] = sha1($this->openid . $this->session_key . (string)$this->GetURandom());
            }
        }

        $res = array("code" => $ret_code, "data" => $ret_msg);
        //var_dump($res);
        return $res;
    }
}

?>
