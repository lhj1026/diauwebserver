<?php

require_once "wx_config.php";
include_once "wx_accessToken.php";



function updateRuleDatabase()
{
    //read from wechat platform for the keywords and answers

    $wx_session= new Wx_AccessToken();

    $AccessToken=$wx_session->getAccessToken();
    $api_url = sprintf("https://api.weixin.qq.com/cgi-bin/get_current_autoreply_info?access_token=%s",$AccessToken);
    //echo "URL:$api_url\n";
    $resp_contents = file_get_contents($api_url);
    //echo "resp:$resp_contents\n";
    //$json= json_encode($resp_contents, JSON_UNESCAPED_UNICODE);

    //echo "json:$json";
    $Rule_json=$resp_contents;
    $result_put= file_put_contents(Wx_Config::RULEFILE, $resp_contents);
    if($result_put===false)
    {

        return "Fail to write into file ";
    }
    else{
        return "Success!";
    }
}