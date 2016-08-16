<?php
namespace Woldy\ddsdk\Components;
use Cache;
use Httpful\Request;
class contacts{
    /**
     * 根据免登码获取用户信息
     * @Author   Woldy
     * @DateTime 2016-05-12T09:13:43+0800
     * @param    [type]                   $ACCESS_TOKEN [description]
     * @param    [type]                   $CODE         [description]
     * @return   [type]                                 [description]
     */
 	public static function getUserInfoByCode($ACCESS_TOKEN,$CODE){
        	$param=http_build_query(
        		array(
        			'code' =>$CODE, 
        			'access_token'=>$ACCESS_TOKEN
        		)
        	);
 
            $response = Request::get('https://oapi.dingtalk.com/user/getuserinfo?'.$param)->send();
            if ($response->hasErrors()){
            	var_dump($response);
            	exit;
        	}
        	if ($response->body->errcode != 0){
            	var_dump($response->body);
            	exit;
        	}
            $userid = $response->body->userid;
 
        return self::getUserInfoByUid($ACCESS_TOKEN,$userid);
	}

    /**
     * 根据uid获取用户信息
     * @Author   Woldy
     * @DateTime 2016-05-12T09:14:17+0800
     * @param    [type]                   $ACCESS_TOKEN [description]
     * @param    [type]                   $uid          [description]
     * @return   [type]                                 [description]
     */
	public static function getUserInfoByUid($ACCESS_TOKEN,$uid){
        	$param=http_build_query(
        		array(
        			'userid' =>$uid, 
        			'access_token'=>$ACCESS_TOKEN
        		)
        	);
 
            $response = Request::get('https://oapi.dingtalk.com/user/get?'.$param)->send();
            if ($response->hasErrors()){
            	var_dump($response);
            	exit;
        	}
        	if ($response->body->errcode != 0){
            	// var_dump($response->body);
            	// exit;
        	}
          
 
        return $response->body;
	}



    /**
     * 创建用户
     * @Author   Woldy
     * @DateTime 2016-08-16T13:56:23+0800
     * @return   [type]                   [description]
     * // {
//     "userid": "zhangsan",
//     "name": "张三",
//     "orderInDepts" : "{1:10, 2:20}",
//     "department": [1, 2],
//     "position": "产品经理",
//     "mobile": "15913215421",
//     "tel" : "010-123333",
//     "workPlace" :"",
//     "remark" : "",
//     "email": "zhangsan@gzdev.com",
//     "jobnumber": "111111",
//     "isHide": false,
//     "isSenior": false,
//     "extattr": {
//                 "爱好":"旅游",
//                 "年龄":"24"
//                 }
// }
     */
    public static function addUser($ACCESS_TOKEN,$user){
        $response = Request::post('https://oapi.dingtalk.com/user/create?access_token='.$ACCESS_TOKEN)
            ->body(json_encode($user))
            ->sendsJson()
            ->send();
        if ($response->hasErrors()){
            var_dump($response);
            exit;
        }
        if ($response->body->errcode != 0){
            //  var_dump($response->body);
            // exit;
        }
        return $response->body;
    }


    /**
     * [getUserInfoBySns description]
     * @Author   Woldy
     * @DateTime 2016-08-10T13:53:11+0800
     * @param    [type]                   $SNS_TOKEN [description]
     * @return   [type]                              [description]
     */
    public static function getUserInfoBySns($SNS_TOKEN){
            $param=http_build_query(
                array(
                    'sns_token'=>$SNS_TOKEN
                )
            );
 
            $response = Request::get('https://oapi.dingtalk.com/sns/getuserinfo?'.$param)->send();
            if ($response->hasErrors()){
                var_dump($response);
                exit;
            }
            if ($response->body->errcode != 0){
               return $response->body;
            }

            $userinfo = $response->body->user_info;
 
        return $userinfo;
    }

    /**
     * 根据unionId获取员工信息
     * @Author   Woldy
     * @DateTime 2016-08-12T19:16:16+0800
     * @param    [type]                   $ACCESS_TOKEN [description]
     * @param    [type]                   $unionid      [description]
     * @return   [type]                                 [description]
     */
    public static function  getUserIdByUnionId($ACCESS_TOKEN,$unionid){
            $param=http_build_query(
                array(
                    'access_token'=>$ACCESS_TOKEN,
                    'unionid'=>$unionid
                )
            );
 
            $response = Request::get('https://oapi.dingtalk.com/user/getUseridByUnionid?'.$param)->send();
            if ($response->hasErrors()){
                var_dump($response);
                exit;
            }
            if ($response->body->errcode != 0){
                return $response->body;
            }
            $userid = $response->body->userid;
 
        return $userid;       
    }

    public static function delUserByIds($ACCESS_TOKEN,$ids){
        if(!is_array($ids)){
            $ids=explode(',', $ids);
        }
        foreach ($ids as $id) {
            $param=http_build_query(
                array(
                    'access_token'=>$ACCESS_TOKEN,
                    'userid'=>$id
                )
            );
            //die('https://oapi.dingtalk.com/user/delete?'.$param);
            $response = Request::get('https://oapi.dingtalk.com/user/delete?'.$param)->send();
            if ($response->hasErrors()){
               // var_dump($response);
               // exit;
            }
            if ($response->body->errcode != 0){
                //return $response->body;
            }
        }

        return $response->body;
    }
}