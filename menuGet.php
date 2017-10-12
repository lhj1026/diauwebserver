<?php


header('Content-Type: text/html; charset=UTF-8');

$APPID="wx8e248cfb3d88e797";
$APPSECRET="57fc5ed91e4fbc944273825691f2ebad";

$TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;

$json=file_get_contents($TOKEN_URL);
$result=json_decode($json);

$ACC_TOKEN=$result->access_token;

$MENU_URL="https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$ACC_TOKEN;

$cu = curl_init();
curl_setopt($cu, CURLOPT_URL, $MENU_URL);
curl_setopt($cu, CURLOPT_RETURNTRANSFER, 1);
$menu_json = curl_exec($cu);
$menu = json_decode($menu_json);
curl_close($cu);

echo $menu_json;

?>
