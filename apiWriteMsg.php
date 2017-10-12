<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 26/09/17
 * Time: 10:10 PM
 */
require_once "wx_config.php";
include_once "KeywordsAnswer.php";


function writeIntoDatabase()
{
    //echo "writeMsg";
    $openId = $_POST['openid'];
    $time = $_POST['time'];
    $msg = $_POST['content'];
    $nickname = $_POST['nickname'];

    $conn = new mysqli(Wx_Config::SERVERNAME, Wx_Config::USERNAME,Wx_Config::PASSWORD ,Wx_Config::DATABASE);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    else {
        $sQuery = "insert into userMsg (from_user,openId,update_time,message) values ('$nickname','$openId','$time','$msg')";
        $stmt = $conn->prepare($sQuery);
        $stmt->execute();
        $stmt->store_result();
        $stmt->close();
    }

}
function isValidQuestion($msg)
{
    //$hiList=array("hi","hey","hello");
    //tell if the question is valid or not
    //1. if the question is not Chinese
    if (preg_match("/^[\x7f-\xff]+$/", $msg)) {
        return true;
    }
    else{
        return false;
    }
}
function writeMsg()
{

    $msg = $_POST['content'];
    //$nickname = $_POST['nickname'];

    if (isValidQuestion($msg)) {
        $QAItem = new KeywordsAnswer();
        $QAItem->readKeywordsAnswer();

        $answer = $QAItem->giveAnswer($msg);
        //echo "\nanswer:";

        if (($QAItem->MatchKeyword and $QAItem->writeMatchedQuestion) or (!$QAItem->MatchKeyword)) {
            writeIntoDatabase();
        };

        $model = "";
        $respMsg = "";
        switch ($answer->type) {
            case "news":
                $model = "news";
                $respMsg = $answer->news_info->list[0];
                break;
            case "text":
                $model = "text";
                $respMsg = $answer->content;
                break;
        }

        $data = array(
            'status' => 0,
            'msg' => $respMsg,
            'model' => $model,
            /*
            'model'=> "ok",
            'menunum'=> 4,
            'menu'=> array(
                    "补充资料",
                    "输入问题",
                    "查看状态",
                    "更多..."*/
        );

    } else {
        $data = array(
            'status' => 1,//not valid question
            'msg'   =>"",
            'model' =>"text"
        );
    }
    $response['data'] = $data;
    $response["error"] = false;
    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}
