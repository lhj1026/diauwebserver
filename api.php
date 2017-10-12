<?php
//getting the database connection

include_once "wx_config.php";
include_once "wx_S_Session.php";
require_once 'apiSignUp.php';
require_once 'apiSaveOpenID.php';
require_once 'apiWriteMsg.php';
require_once 'apiUpdateUserInfo.php';
require_once 'apiUpdateRule.php';
require_once 'apiDecrypt.php';
require_once 'apiPushVoice.php';

$response['message'] = '0000';

//an array to display response
$response = array();
//if it is an api call
//that means a get parameter named api call is set in the URL
//and with this parameter we are concluding that it is an api call
//echo ($_GET['apicall']);
if (isset($_GET['apicall'])) {

    switch ($_GET['apicall']) {

        case 'saveOpenId':
//            echo "saveOpenId";
            saveOpenId();
            break;
        case 'signup':
            userSignUp();
            break;
        case 'writeMsg':
            writeMsg();
            break;
        case 'pushVoice':
            pushVoice();
            break;
        case 'decrypt':
            decrypt();
            break;
        case 'updateuserinfo':
            updateUserInfo();
            break;
        case 'updateRuleDatabase':
            updateRuleDatabase();
            break;

        default:
            $response['error'] = true;
            $response['message'] = 'Invalid Operation Called';
    }

} else {
    //if it is not api call
    //pushing appropriate values to response array
    $response['error'] = true;
    $response['message'] = 'Invalid API Call';
}
//displaying the response in json structure
echo json_encode($response);


?>
