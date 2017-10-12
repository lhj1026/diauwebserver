<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 26/09/17
 * Time: 9:55 PM
 */
include_once "wx_config.php";
function isTheseParametersAvailable($params)
{

    //traversing through all the parameters
    foreach ($params as $param) {
        //if the paramter is not available
        $sParam = $_POST[$param];/** @var TYPE_NAME $conn */
        //echo "$param:$sParam\n";
        if (!isset($sParam)){
            //return false
            return false;
        }
    }
    //return true if every param is available
    return true;
}

function userSignUp()
{
    //checking the parameters required are available or not
    $response['message'] = '0000';
    //var_dump( $_POST);
    $resParam = isTheseParametersAvailable(array('nickname', 'gender', 'headimg', 'country', 'province', 'city', 'language', 'openID'));
    //echo "param result: $resParam\n";
    if ($resParam) {
        //getting the values
        $openID = $_POST['openID'];
        $nickname = $_POST['nickname'];
        //$password = md5($_POST['password']);
        $gender = $_POST['gender'];
        $headimg = $_POST['headimg'];
        $country = $_POST['country'];
        $province = $_POST['province'];
        $city = $_POST['city'];
        $language = $_POST['language'];
        $id ="";
        //checking if the user is already exist with this username or email
        //as the email and username should be unique for every user
        //echo "before connect to DB";

        //var_dump( $conn);
        //die("Connection failed: " . $conn->connect_error);
        $conn = new mysqli(Wx_Config::SERVERNAME, Wx_Config::USERNAME,Wx_Config::PASSWORD ,Wx_Config::DATABASE);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }


        $sQuery_select="SELECT id, 
                                openID, 
                                nickname,
                                gender,
                                age,
                                weight,
                                height,
                                glucose1,
                                glucose2,
                                glucose3,
                                glucose4, 
                                bloodhigh,
                                bloodlow,
                                email
                                FROM users WHERE openID = '$openID'";

        $stmt = $conn->prepare($sQuery_select);
        //$stmt->bind_param("s", );
        $stmt->execute();
        $stmt->store_result();

        //echo "numbe of row: $stmt->num_rows";
        //if the user already exist in the database
        if ($stmt->num_rows > 0) {
            $response['error'] = true;
            $response['message'] = 'User already registered';
            $stmt->bind_result( $id,
                $openID,
                $nickname,
                $gender,
                $age,
                $weight,
                $height,
                $glucose1,
                $glucose2,
                $glucose3,
                $glucose4,
                $bloodhigh,
                $bloodlow,
                $email);
            $stmt->fetch();

            $user = array(
                'id' => $id,
                'openID' => $openID,
                'nickname'=>$nickname,
                'gender'=>$gender,
                'age'=>$age,
                'weight'=>$weight,
                'height'=>$height,
                'glucose1'=>$glucose1,
                'glucose2'=>$glucose2,
                'glucose3'=>$glucose3,
                'glucose4'=>$glucose4,
                'bloodhigh'=>$bloodhigh,
                'bloodlow'=>$bloodlow,
                'email'=>$email
            );
            $response['user'] = $user;
            $stmt->close();

            //var_dump( $response);
        } else {

            //if user is new creating an insert query
            $sQuery_insert="INSERT INTO users (nickname, gender, headImageUrl, country, province, city, language,openID ) VALUES ('$nickname', '$gender', '$headimg', '$country','$province','$city','$language','$openID')";
            //echo $sQuery;
            $stmt = $conn->prepare($sQuery_insert);
            //$stmt->bind_param("sssssss", $nickname, $gender, $headimg, $country, $province, $city, $language, $openID);
            //if the user is successfully added to the database

            $sResult = $stmt->execute();
            //echo mysql_error();

            if ($sResult) {

                //fetching the user back

                $stmt = $conn->prepare($sQuery_select);
                //$stmt->bind_param("s", $openID);
                $stmt->execute();
                $stmt->bind_result( $id,
                                    $openID,
                                    $nickname,
                                    $gender,
                                    $age,
                                    $weight,
                                    $height,
                                    $glucose1,
                                    $glucose2,
                                    $glucose3,
                                    $glucose4,
                                    $bloodhigh,
                                    $bloodlow,
                                    $email);
                $stmt->fetch();

                $user = array(
                    'id' => $id,
                    'openID' => $openID,
                    'nickname'=>$nickname,
                    'gender'=>$gender,
                    'age'=>$age,
                    'weight'=>$weight,
                    'height'=>$height,
                    'glucose1'=>$glucose1,
                    'glucose2'=>$glucose2,
                    'glucose3'=>$glucose3,
                    'glucose4'=>$glucose4,
                    'bloodhigh'=>$bloodhigh,
                    'bloodlow'=>$bloodlow,
                    'email'=>$email
                );
                $stmt->close();

                //adding the user data in response
                $response['error'] = false;
                $response['message'] = 'User registered successfully';
                $response['user'] = $user;
            } else {
                $stmt->close();
                $response['error'] = true;
                $response['message'] = 'user signup database failed!';
            }
        }
    } else {
        $response['error'] = true;
        $response['message'] = 'no required parameter!';
    }
    echo json_encode($response,JSON_UNESCAPED_UNICODE);
}