<?
//post传递参数给url地址
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
  //发送get参数给url
	  function _httpGet($url=""){     
		  $curl = curl_init();
		  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		  curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		  // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
		  // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
		  // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		  // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		  curl_setopt($curl, CURLOPT_URL, $url);
		  $res = curl_exec($curl);
		  curl_close($curl);
		  return $res;
	  }
//可逆加密
  /*
*$data :需要加密解密的字符串
*$key :加密的钥匙(密匙);
*/
//加密
function encrypt($data, $key)  
{  
    $key    =   md5($key);  
    $x      =   0;  
    $len    =   strlen($data);  
    $l      =   strlen($key);  
    for ($i = 0; $i < $len; $i++)  
    {  
        if ($x == $l)   
        {  
            $x = 0;  
        }  
        $char .= $key{$x};  
        $x++;  
    }  
    for ($i = 0; $i < $len; $i++)  
    {  
        $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);  
    }  
    return base64_encode($str);  
}  
//解密
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