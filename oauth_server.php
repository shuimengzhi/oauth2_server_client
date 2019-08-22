<?php
// 官方文档：https://bshaffer.github.io/oauth2-server-php-docs/
use OAuth2;
class IndexController extends \App\Modules\Base\Controllers\FrontendController
{

    private function server(){
         // error reporting (this is a demo, after all!)
         ini_set('display_errors',1);error_reporting(E_ALL);
         // Autoloading (composer is preferred, but for this example let's just do this)
         require_once('src/OAuth2/Autoloader.php');
         //loading datebase config
         //数据库配置！！！！！！！！！！！！！！！！！！！！！！！！！！！！一定不要忘记！！！！！！！！！！
         require_once('oauth2_dbconfig.php');
         OAuth2\Autoloader::register();
        // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
        //创建存储的方式
        $storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));
        // Pass a storage object or array of storage objects to the OAuth2 server class
        //创建server
        $server = new OAuth2\Server($storage);
        // Add the "Authorization Code" grant type (this is where the oauth magic happens)
        // 添加 Authorization Code 授予类型
        $server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
        return $server;
    }
    // 授权页面和授权,用户同意授权
    public function actionAuthorize(){
        //用户的请求链接      
        //https://******.com/mobile/index.php?m=oauthserver&a=authorize&response_type=code&client_id=testclient&state=xyz&redirect_uri=https://app.ngrok.hanwide.com/oauth_client/getauthorize.php
        //response_type=code写死的，表示授权码方式授权
        //client_id=testclient 请求用户信息的客户端id在数据表dsc_oauth_clients里填写
        //state=xyz  防止CSRF攻击用的参数，这个值随便定义
        //redirect_uri  回调用的url，必须现在数据表dsc_oauth_clients里填写，用来返回授权码的地址
        //加载basehelper里面的方法
        require_once("basehelp.php");

        $server = $this->server();
        $request = OAuth2\Request::createFromGlobals();
        $response = new OAuth2\Response();
        // validate the authorize request
        // 验证请求不通过则die
        if (!$server->validateAuthorizeRequest($request, $response)) {
                $response->send();
                die;
        }
        // display an authorization form
        
            if (strtolower(ACTION_NAME) == 'addcomment') {
                $_SERVER['HTTP_USER_AGENT'] = 'AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1';
            }
    
            //获得用户的id
            $this->user_id = $_SESSION['user_id'];
            //检查用户有没有登录
            $this->actionchecklogin();
            //如果用户没有到授权页面则进入页面
            if(empty($_POST['authorized'])){
                //根据用户id获得用户的头像和名字
                $sql = "SELECT user_name,nick_name,mobile_phone,user_picture FROM ".$GLOBALS['ecs']->table('users')."WHERE user_id =".$this->user_id;
                $user_info =$GLOBALS['db']->GetAll($sql);
                //用户名字
                $nick_name=$user_info[0]["nick_name"];
                //用户头像
                $user_picture=$user_info[0]["user_picture"];
                //如果没有头像就用默认的头像
                if($user_picture==null){
                    $user_picture ="../data/images_user/default_picture.jpeg";
                }
                //获得优品的头像
                $youpin_picture="/mobile/public/img/oauth2-server/youpin.jpg";
                //获得请求客户端id
                $client_id=$_GET['client_id'];
                //数据库获得请求客户头像
                $sql = "SELECT client_picture,client_name FROM ".$GLOBALS['ecs']->table('oauth_clients')."WHERE client_id ='".$client_id."'";
                $client_info = $GLOBALS['db']->getAll($sql);
                //客户端的头像
                $client_picture = $client_info[0]['client_picture'];
                //客户端的名字
                $client_name = $client_info[0]['client_name'];
                //箭头图标
                $arrows = "/mobile/public/img/oauth2-server/arrows.jpg";
                $this->assign('nick_name', $nick_name);
                $this->assign('user_picture', $user_picture);
                $this->assign('youpin_picture', $youpin_picture);
                $this->assign('client_picture', $client_picture);
                $this->assign('arrows', $arrows);
                $this->assign('client_name',$client_name);
                $this->display();die;
            }
       
        
                
                    $is_authorized = ($_POST['authorized'] === 'yes');
                    
                    $server->handleAuthorizeRequest($request, $response, $is_authorized,$this->user_id);
                
        $response->send();
    
    }
    // 生成并获取token
    public function actionToken()
    {
            $server = $this->server();
            $server->handleTokenRequest(\OAuth2\Request::createFromGlobals())->send();
            exit();
    }
    //获取资源
    public function actionResource()
    {
        //加载basehelper里面的方法
        $this->load_helper('base');
        $server = $this->server();
        // Handle a request to a resource and authenticate the access token
        if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $server->getResponse()->send();
            die;
        }
        $token = $server->getAccessTokenData(\OAuth2\Request::createFromGlobals());
        $sql = "SELECT user_name,nick_name,mobile_phone,user_picture FROM ".$GLOBALS['ecs']->table('users')."WHERE user_id =".$token["user_id"];
                $user_info =$GLOBALS['db']->GetAll($sql);
                //用户的名字
                $nick_name=$user_info[0]["nick_name"];
                //用户的头像
                $user_picture=$user_info[0]["user_picture"];
                //用户的电话
                $mobile_phone=$user_info[0]["mobile_phone"];
                //如果没有头像则用默认的头像
                if($user_picture==null){
                    $user_picture =$_SERVER['HTTP_HOST']."/data/images_user/default_picture.jpeg";
                }else{
                    $user_picture =$user_picture;
                }
        //加密方法加密，$key与客户端的包用的$key要一致
        $key="";
        //加密用户名字
        $nick_name=encrypt($nick_name,$key);
        //加密用户照片
        $user_picture=encrypt($user_picture,$key);
        //加密用户电话
        $mobile_phone=encrypt($mobile_phone,$key);

        $data=array(
        'success' => true, 
        'message' => 'You accessed my APIs!',
        'nick_name'=>$nick_name,
        'user_picture'=>$user_picture,
        'mobile_phone'=>$mobile_phone,
        );
        exit(base64_encode(json_encode($data)));
    }
    //检测是否登录
    public function actionchecklogin()
	{
		if ($_SESSION['user_id']) {
			$users = dao('users')->where(array('user_id' => $_SESSION['user_id']))->find();

			if (empty($users)) {
				$_SESSION['user_id'] = 0;
				$_SESSION['user_name'] = '';
				$_SESSION['email'] = '';
				$_SESSION['user_rank'] = 0;
				$_SESSION['discount'] = 1;
				$_SESSION['openid'] = '';
				$_SESSION['unionid'] = '';
			}
		}

		if (!$_SESSION['user_id']) {
			$back_act = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : __HOST__ . $_SERVER['REQUEST_URI'];
			$this->redirect('user/login/index', array('back_act' => urlencode($back_act)));
		}
    }
   

}