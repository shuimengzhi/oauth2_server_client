<?php
//申请接口请找辉莱优品技术联系
//response_type=code写死的方法
//client_id=testclient 申请接口的时候获得的客户端id
//state=xyz 必填，随意填写值，防止CSRF攻击用的参数
//redirect_uri申请接口时填写的回调地址,必须是完整地址
//此处用来跳转到服务端授权页面，让用户点击授权
header("Location: https://www.18yang.com/mobile/index.php?m=oauthServer&a=authorize&response_type=code&client_id=testclient&state=xyz&redirect_uri=https://app.ngrok.hanwide.com/oauth_client/getauthorize.php");

?>
