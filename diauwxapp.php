<?php
//define your token
define("TOKEN", "diauWeb");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
$wechatObj->responseMsg();

//constant
define("HINT_MSG", "感谢关注DiaU戴友健康。试着输入您想问的问题关键词。例如:\n糖尿病\n确诊\n运动\n饮食\n糖尿病前期\n水果\n近期活动\n讲座\n戴友\n...");


//define("HINT_MSG","Thanks,tryagain.");

class wechatCallbackapiTest
{


	public function valid()
	{
       		$echoStr = $_GET["echostr"];

        	//valid signature , option
        	if($this->checkSignature()){
            		echo $echoStr;
            		exit;
       		 }
   	 }



   public function responseMsg()
    {
        //get post data, May be due to the different environments
	    $dataStr = file_get_contents('php://input');
        //extract post data

        if ($dataStr){

                $postObj = simplexml_load_string($dataStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            //var_dump($postObj);
                $RX_TYPE = trim($postObj->MsgType);

                switch($RX_TYPE)
                {
                    case "text":
                        /** @var TYPE_NAME $resultStr */
                        $resultStr = $this->handleText($postObj);
                        break;
                    case "event":
                        $resultStr = $this->handleEvent($postObj);
                        break;
                    default:
                        $resultStr = "Unknow msg type: ".$RX_TYPE;
                        break;
                }
                echo $resultStr;
        }else {
            echo "message is NULL";
            exit;
        }
    }

    /**
     * @param $postObj
     * @return string
     */
    public function handleText($postObj)
    {
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $keyword = trim($postObj->Content);
        $time = time();
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>0</FuncFlag>
                    </xml>";
        if(!empty( $keyword ))
        {
            $msgType = "text";
            //keyword answer
            if($keyword=="糖尿病")
            {
                $contentStr ="很多人问我，我空腹已经7了，是糖尿病么？还有人问我，我饭后达到11了，是糖尿病么？我的回答是，未必。根据美国糖尿病协会制定的标准，要同时满足空腹血糖为7，糖化血红蛋白大于6.5%，糖耐受测试两小时后大于11，随机血糖值大于11才能确诊为糖尿病。短时期的一个或者几个指标较高也未必是糖尿病，根据身体状况的不同，代谢效率也会发生变化。 问题的关键不在于认识自己是否已经是糖尿病了，而在于及时发现血糖的异常并且及时采取措施，糖尿病前期和早期的二型糖尿病是完全可以脱离药物的辅助达到恢复的效果的。";
            }elseif($keyword=="遗传") {
                $contentStr ="糖尿病跟遗传有很大关系。但并不代表家人有病史，自己就一定会得糖尿病。一型糖尿病完全由基因遗传决定，一般在10岁左右就发病，可是这种类型在国人中只占总发病率的5％还不到。绝大多数发病为二型，而二型虽然跟基因遗传有一定关系，但是主要取决于个人的生活方式，也就是说，自己的努力可以克服基因的缺陷。";
            }else{
                $contentStr = "感谢关注DiaU戴友健康。试着输入您想问的问题关键词。例如:\n糖尿病\n确诊\n运动\n饮食\n糖尿病前期\n水果\n近期活动\n讲座\n戴友\n...";
            }
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            echo $resultStr;
        }else{
            echo "Input something...";
        }
        return $resultStr;
    }

    public function handleEvent($object)
    {
        $contentStr = "";
        switch ($object->Event)
        {
            case "subscribe":
                $contentStr = HINT_MSG;
                break;
            default :
                $contentStr = "Unknow Event: ".$object->Event;
                break;
        }
        $resultStr = $this->responseText($object, $contentStr);
        return $resultStr;
    }

    public function responseText($object, $content, $flag=0)
    {
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>%d</FuncFlag>
                    </xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
        return $resultStr;
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}

?>
