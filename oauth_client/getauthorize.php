<?php
//发送post的方法
function _httpPost($url="" ,$requestData=array()){
                
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
   
    //普通数据
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($requestData));
    $res = curl_exec($curl);

    //$info = curl_getinfo($ch);
    curl_close($curl);
    return $res;
}
//解密用的方法
function decrypt($data, $key)  
{  
    $key = md5($key);  
    $x = 0;  
    $data = base64_decode($data);  
    $len = strlen($data);  
    $l = strlen($key);  
    for ($i = 0; $i < $len; $i++)  
    {  
        if ($x == $l)   
        {  
            $x = 0;  
        }  
        $char .= substr($key, $x, 1);  
        $x++;  
    }  
    for ($i = 0; $i < $len; $i++)  
    {  
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1)))  
        {  
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));  
        }  
        else  
        {  
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));  
        }  
    }  
    return $str;  
}


//获得服务端发送过来的授权码
$code=$_GET['code'];
//申请接口时填写的客户端账户
$client_id="testclient";
//申请接口时填写的客户端密码
$client_secret="123";
//授权方式为授权码授权，写死的，不可更改
$grant_type="authorization_code";
//将授权码提交给token控制器获取资源的访问令牌的url，不可更改
$url="https://*****/mobile/index.php?m=oauthServer&a=token";
//申请接口时填写的回调地址，可更改
$redirect_uri="https://******/oauth_client/getauthorize.php";
//将以上参数整理成数组
$data=array("code"=>$code,"client_id"=>$client_id,"client_secret"=>$client_secret,"grant_type"=>$grant_type,"redirect_uri"=>$redirect_uri);
//将参数发送到token控制器的url
$access_token_array=_httpPost($url,$data);
//json解码从token返回的数据
$access_token_array=json_decode($access_token_array);
//提取access_token值
$access_token=$access_token_array->access_token;


//提供资源的控制器，此地址不可更改
$url="https://****/mobile/index.php?m=oauthServer&a=resource";
//访问资源需要的access_token
$data=array("access_token"=>$access_token);
//将access_token参数传给资源访问的地址
$message=_httpPost($url,$data);
//base64解码返回的数据，然后再json解码
$user_info=json_decode(base64_decode($message));
//这是php解码用的key，不可更改
$key="";
//用自己的解密方法解密获取用户的名字
$nick_name=decrypt($user_info->nick_name,$key);
//获取用户的电话，有可能为空，有些客人没有填写
$mobile_phone=decrypt($user_info->mobile_phone,$key);
//获取用户的头像地址
$user_picture=decrypt($user_info->user_picture,$key);
//打印显示用户名字，用户的电话，用户的头像，这一段可以删除，可有可无。
var_dump($nick_name,$mobile_phone,$user_picture);