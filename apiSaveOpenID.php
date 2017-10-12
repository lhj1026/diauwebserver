<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04/10/17
 * Time: 1:42 PM
 */
function saveOpenId()
{

    //echo "saveOpenId";
    $response['message'] = '0000';
    $code=$_GET['code'];
    $wx_session= new Wx_S_Session($code);
    //echo "code:$code\n";
    $res=$wx_session->sSerssion();

    if($res['code']==="0"){
        $response['error'] = false;
        $response['code'] = $res['code'];
        $response['data'] = $res['data'];
        $response['message'] = 'success';
        //echo "response:  ";
        //var_dump($response);
    }else{
        $response['error'] = true;
        $response['message'] = 'cannot get openid';
    }
    header('Content-Type: application/json');
    echo json_encode($response);

}