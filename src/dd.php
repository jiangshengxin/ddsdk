<?php
namespace Woldy\ddsdk;
use Illuminate\Config\Repository;
use Woldy\ddsdk\Components\Token;
use Woldy\ddsdk\Components\Message;
use Woldy\ddsdk\Components\Contacts;
use Woldy\ddsdk\Components\Group;
use Illuminate\Support\Facades\Input;
class dd{
	static $config;
	static $token;
	static $ACCESS_TOKEN;
	public function __construct(Repository $config){
		self::$config = $config;
		self::$token = new Token($config);
		self::$ACCESS_TOKEN=self::$token->getAccessToken();
	}

	/**
	 * 取得JS SDK 配置
	 * @Author   Woldy
	 * @DateTime 2016-05-09T17:01:32+0800
	 */
	public static function getJsConfig(){
		return self::$token->getJsConfig();
	}


	/**
	 * 获取SSO配置
	 */
	public static function getSsoConfig($ssoid){
		$ssolist=self::$config->get('dd')['sso'];

		if(!array_key_exists($ssoid,$ssolist)){
			die('wrong id!');
		}else{
			return $ssolist[$ssoid];
		}
	}

	/**
	 * 根据免登CODE获取用户信息
	 * @Author   Woldy
	 * @DateTime 2016-05-09T17:03:07+0800
	 * @return   [type]                   [description]
	 */
	public static function getUserInfoByCode(){
		$code=Input::get('authcode');
		return Contacts::getUserInfoByCode(self::$ACCESS_TOKEN,$code);
	}

	/**
	 * 根据UserID获取用户信息
	 * @Author   Woldy
	 * @DateTime 2016-05-09T17:03:34+0800
	 * @return   [type]                   [description]
	 */
	public static function getUserInfoByUid($uid){
		return Contacts::getUserInfoByUid(self::$ACCESS_TOKEN,$uid);
	}

	/**
	 * 发送消息
	 * @Author   Woldy
	 * @DateTime 2016-05-09T19:57:22+0800
	 * @param    string                   $type    [description]
	 * @param    [type]                   $content [description]
	 * @return   [type]                            [description]
	 */
	public static function sendMessage($touser,$toparty,$type='text',$content){

	}

	/**
	 * 通过加密串发送信息
	 * @Author   Woldy
	 * @DateTime 2016-05-10T13:20:06+0800
	 * @param    [type]                   $code [description]
	 * @return   [type]                         [description]
	 */
	public static function sendMessageByCode(){
		$code=Input::get('code');
		//echo $code;
		return Message::sendMessageByCode(self::$ACCESS_TOKEN,self::$config,$code);
	}


	/**
	 * 扫码登录
	 * @Author   Woldy
	 * @DateTime 2016-08-23T11:17:36+0800
	 * @param    [type]                   $code [description]
	 * @return   [type]                         [description]
	 */
	public static function snsLogin($code){
		$accesstoken=self::$token->getSnsAccessToken();
		$persistent=self::$token->getPersistent($accesstoken,$code);
		$snscode=self::$token->getSnsToken($accesstoken,$persistent);
		$userinfo=Contacts::getUserInfoBySns($snscode);
		$userid=Contacts::getUserIdByUnionId(self::$ACCESS_TOKEN,$userinfo->unionid);
		$userinfo=Contacts::getUserInfoByUid(self::$ACCESS_TOKEN,$userid);
		return $userinfo;
	}

	/**
	 * 删除用户
	 * @Author   Woldy
	 * @DateTime 2016-08-23T11:17:48+0800
	 * @param    [type]                   $ids [description]
	 * @return   [type]                        [description]
	 */
	public static function delUser($ids){
		$accesstoken=self::$ACCESS_TOKEN;
		return Contacts::delUserByIds($accesstoken,$ids);
	}

	/**
	 * 增加用户
	 * @Author   Woldy
	 * @DateTime 2016-08-23T11:17:58+0800
	 * @param    [type]                   $user [description]
	 */
	public static function addUser($user){
		$accesstoken=self::$ACCESS_TOKEN;
		return Contacts::addUser($accesstoken,$user);
	}

	public static function createChat(){
		$accesstoken=self::$ACCESS_TOKEN;
		return Contacts::createChat($accesstoken);			
	}

	public static function getAllGroup(){
		$accesstoken=self::$ACCESS_TOKEN;
		return Group::getAllGroups($accesstoken);			
	}

	public static function getGroupById($groupid,$refresh=false){
		$accesstoken=self::$ACCESS_TOKEN;
		return Group::getGroupById($groupid,$accesstoken,$refresh);	
	}

	/**
	 * 人员去重
	 * @Author   Woldy
	 * @DateTime 2016-08-23T18:02:57+0800
	 * @return   [type]                   [description]
	 */
	public function cleanDouble($id1,$id2){
		$accesstoken=self::$ACCESS_TOKEN;
		$info1=Contacts::getUserInfoByUid(self::$ACCESS_TOKEN,$id1);
		$info2=Contacts::getUserInfoByUid(self::$ACCESS_TOKEN,$id2);

		$a1=$info1->active;
		$a2=$info2->active;

		if($a1===false){
			$delid=$info1->userid;
		}else if($a2===false){
			$delid=$info2->userid;
		}else{
			//return Contacts::createChat($accesstoken,"manager7108,$id1,$id2","号码去重确认");
		 	return false;
		};
		return Contacts::delUserByIds($accesstoken,$delid);	
	}
} 