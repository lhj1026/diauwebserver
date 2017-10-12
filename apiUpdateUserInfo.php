<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 01/10/17
 * Time: 8:19 PM
 */
include_once "wx_config.php";
function updateUserInfo(){

    $response['message'] = '0000';

    $openID = $_GET['openID'];
    $updateInfo=$_GET['datainfo'];

    //add the "" to the key
    if(preg_match('/\w:/', $updateInfo)){
        $updateInfo = preg_replace('/(\w+):/is', '"$1":', $updateInfo);
    }
    $updateInfo=json_decode($updateInfo,true);

    $strInfo ='';

    foreach ( $updateInfo as $singleinfo) {

        $strInfo=$strInfo.$singleinfo['name'].'='.$singleinfo['value'].',';
    }
    $strInfo=substr($strInfo,0,strlen($strInfo)-1);

    $conn = new mysqli(Wx_Config::SERVERNAME, Wx_Config::USERNAME,Wx_Config::PASSWORD ,Wx_Config::DATABASE);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sQuery_update="update users set $strInfo  WHERE openID = '$openID'";

    $stmt = $conn->prepare($sQuery_update);

    $stmt->execute();
    $stmt->store_result();
    $stmt->close();
}
?>