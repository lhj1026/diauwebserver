<?php

header('Content-Type: text/html; charset=UTF-8');

//更换成自己的APPID和APPSECRET
$APPID="wx8e248cfb3d88e797";
$APPSECRET="57fc5ed91e4fbc944273825691f2ebad";

$TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;

$json=file_get_contents($TOKEN_URL);
$result=json_decode($json);

$ACC_TOKEN=$result->access_token;
//echo $ACC_TOKEN;
$data='{
		 "button":[
		 {
			   "name":"关于戴友",
			   "sub_button":[
				{
				   "type":"click",
				   "name":"戴友介绍",
				   "key":"aboutDiau"
				},
				{
				   "type":"click",
				   "name":" 近期活动",
				   "key":"RecentAcitvity"
				},
				]
		  },
		  {
			   "name":"知识问答",
			   "sub_button":[
				{
				   "type":"view",
				   "name":"最新消息",
				   "url":"http://mp.weixin.qq.com/mp/appmsg/show?__biz=MjM5NDM0NTEyMg==&appmsgid=10000005&itemidx=1&sign=136d2f76ede1b6661fd7dc08011889d7#wechat_redirect"
				},
				{
				   "type":"click",
				   "name":"历史消息",
				   "key":"historyInfo"
				}]
		   },
		   {
			   "type":"view",
			   "name":"关于你",
			   "url":"http://mp.weixin.qq.com/mp/appmsg/show?__biz=MjM5NDM0NTEyMg==&appmsgid=10000021&itemidx=1&sign=e25db2fe9750a1b08788d6a7c0498562#wechat_redirect"
		   }]
       }';

$MENU_URL="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$ACC_TOKEN;

$ch = curl_init($MENU_URL);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data)));
$info = curl_exec($ch);
$menu = json_decode($info);
print_r($info);		//创建成功返回：{"errcode":0,"errmsg":"ok"}

if($menu->errcode == "0"){
    echo "菜单创建成功";
}else{
    echo "菜单创建失败";
}

/*$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $MENU_URL);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$info = curl_exec($ch);

if (curl_errno($ch)) {
	echo 'Errno'.curl_error($ch);
}

curl_close($ch);

var_dump($info);*/

?>