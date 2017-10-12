<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 25/09/17
 * Time: 9:49 PM
 */
//公众号自动回复规则
$Rule_json="";
$sessionKey='';
class Wx_Config
{
    /**
     * TODO: 修改这里配置小程序信息
     */
    # 小程序唯一标识
    const APPID = "wx8e248cfb3d88e797";
    # 小程序的 app secret
    const SECRET = "7538bd703999c388b33d9cff5e683815";
    # 认证方式
    const GRANT_TYPE = "authorization_code";
    #database connection settings
    const SERVERNAME= "localhost";
    const USERNAME = "diauAdmin";
    const PASSWORD = "Ljm@0628";
    const DATABASE= "diauDB";

    const RULEFILE='ruleJson.txt';
    const UPLOAD_FILE_DIR='./upload';
    const UPLOAD_FILE_MOD=0777;


    #conversation settings
    //公众号开发信息

    const PLATFORM_APPID ="wx34f7ed46667ff1af";
    const PLATFORM_SECRET ="6f681aa0be872adbacdf33462cb3c7d8";
}