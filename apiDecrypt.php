<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 05/10/17
 * Time: 8:10 PM
 */
header("Content-Type:text/html; charset=utf-8");
include_once "wx_config.php";
include_once "WXBizDataCrypt.php";
//global $sessionKey;
function decrypt()
{
    $session_Key = (string)$_GET['session'];
    $encryptedData = (string) $_GET['encryptedData'];
    
    $encryptedData = str_replace(' ','+',$encryptedData);
    
    $iv = (string)$_GET['iv'];
    $data="";
    $pc = new WXBizDataCrypt(Wx_Config::APPID, $session_Key);

    $errCode = $pc->decryptData( $encryptedData, $iv, $data);

    //
    //var_dump($data);
    //header('Content-Type: application/json');
    if ($errCode == 0) {
        //echo(json_encode($data,JSON_UNESCAPED_UNICODE) . "\n");
        echo($data. "\n");
    } else {
        echo($errCode . "\n");
    }
    
}
