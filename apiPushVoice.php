<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 05/10/17
 * Time: 5:40 PM
 */
require_once "wx_config.php";
include_once "KeywordsAnswer.php";

function udate($format, $timestamp=null) {
    if (!isset($timestamp)) $timestamp = microtime();

    if (count($t = explode(" ", $timestamp)) == 1) {
        list($timestamp, $usec) = explode(".", $timestamp);
        $usec = "." . $usec;
    }else {
        $usec = $t[0];
        $timestamp = $t[1];
    }

    if($timestamp<0){
        $usec = 1-$usec;
        $timestamp = $timestamp-1;
    }

    $date = new DateTime(date('Y-m-d H:i:s' . substr(sprintf('%.7f', $usec), 1), $timestamp));
    $result = $date->format($format);
    return $result;

}
function checkAndSaveFile()
{

    $result= "";
    try {
        $openid=$_POST['openid'];

        // Undefined | Multiple Files | $_FILES Corruption Attack
        // If this request falls under any of them, treat it invalid.
        if (
            !isset($_FILES['upfile']['error']) ||
            is_array($_FILES['upfile']['error'])
        ) {
            throw new RuntimeException('Invalid parameters.');
        }

        // Check $_FILES['upfile']['error'] value.
        switch ($_FILES['upfile']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }

        // You should also check filesize here.
        if ($_FILES['upfile']['size'] > 1000000) {
            throw new RuntimeException('Exceeded filesize limit.');
        }

        // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
        // Check MIME Type by yourself.
        //check if the the upload folder is there
        $dir=Wx_Config::UPLOAD_FILE_DIR.'/'.$openid.'/';
        if (!is_dir($dir) && strlen($dir)>0)
            mkdir($dir, Wx_Config::UPLOAD_FILE_MOD);

        //$timeStr=(new DateTime())->format("YmdHisu");
        $timeStr=udate('Y-m-d_H-i-s-u');

        $tmpFile=sprintf('%s%s.silk', $dir, $timeStr);

        //echo $tmpFile.'  ';
        if (is_uploaded_file($_FILES['upfile']['tmp_name'])){
            if (!move_uploaded_file(
                $_FILES['upfile']['tmp_name'],$tmpFile)) {
                print_r(error_get_last());
                throw new RuntimeException('Failed to move uploaded file.');
            }
            //echo 'File is uploaded successfully.';
            $result= $tmpFile;
        }else{
            echo 'File is not uploaded.';
            $result= "";
        }

    } catch (RuntimeException $e)
    {
         echo $e->getMessage();
    }
    return $result;
}
function convert_to_wav($file)
{
    //convert the silk file to wav.
    //1. need to install ffmpeg in the php server
    $idWordWebm="data:audio/webm;base64";
    $idWordSilk="#!SILK_V3";
    //remove the extension from the file name
    $realFileName=$file;
    $fileNameNoExt=preg_replace('/\\.[^.\\s]{3,4}$/', '', $file);


    // the audio file uploaded by PC dev tools begin with 'data:audio/webm', the file uploaded by phone begin with "#!SILK_V3"
    $audioFile=file_get_contents($file);
    //echo $audioFile;
    if(strpos($audioFile,$idWordWebm)!== false) {

        //先将文件的头部未加密信息去除
        $base64 = str_replace('data:audio/webm;base64,', '', $audioFile);
        // 对文件进行 base64 解密
        $content = base64_decode($base64);
        // 修改文件后缀名
        $realFileName=$fileNameNoExt.'.webm';
        file_put_contents($realFileName, $content);

        //exec the batch file to convert
        $type='wav';
        $cmd="sh ./silk-v3-decoder-master/converter.sh $realFileName $type";
        exec($cmd,$out);
        if(strpos($out[0],'[ok]')!==false){
            echo "convert successfully";
        }
        //echo $realFileName;
    }elseif (strpos($audioFile,$idWordSilk)!== false)
    {
        //the wechat audio file from phone begins with "0x02#!SILK_V3", need to remove the chat 0x02
        $audioFile =str_replace(chr(2).$idWordSilk,$idWordSilk,$audioFile);
        file_put_contents($realFileName, $audioFile);


        $cmdToPcm="./silk-v3-decoder-master/silk/decoder $realFileName $fileNameNoExt.pcm";
        exec($cmdToPcm,$out);

        $cmdToWav = "ffmpeg -y -f s16le -ar 12000 -ac 2 -i $fileNameNoExt.pcm -f wav -ar 16000 -ac 1 $fileNameNoExt.wav";
        exec($cmdToWav, $out);
    };
    return $fileNameNoExt."wav";

}
function pushVoice()
{

    //1. put the uoloaded file into the subfolder in 'upload' folder. the subfolder name is openid
    $file=checkAndSaveFile();
    //2. convert the silk audio file to wav file.
    $wavFile=convert_to_wav($file);
    //3. use Xunfei SDK to recognize the voice


    $data = array(
        'status' => 0,
        'msg' => 'voice',
        'model'=>'voice'
        /*
        'model'=> "ok",
        'menunum'=> 4,
        'menu'=> array(
                "补充资料",
                "输入问题",
                "查看状态",
                "更多..."*/
    );


    $response['data'] = $data;
    $response["error"] = false;
    header('Content-Type: application/json');
    echo json_encode($response,JSON_UNESCAPED_UNICODE);
}
?>