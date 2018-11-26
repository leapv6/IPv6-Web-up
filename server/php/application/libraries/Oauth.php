<?php
class Oauth{
	const HOST='http://oauth.v6plus.com/index.php/';
	const SIGN_FAIL=1001;//签名验证失败
	const APP_NONE=1002;//app不存在
	const APP_FROZEN=1003;//app被冻结
	const TIME_OUT=1004;//签名超时
	const SIGN_FA=1005;//签名验证失败
	const CODE_NONE=1006;//无效code
	const UNKNOWN=0;//未知错误

	var $appid;
	var $appkey;

	function __construct($config=[]) {
		foreach ($config as $key => $value) {
			$this->$key=$value;
		}
	}

	function buildParam($isHeader=true){
		$time=time();
		$sign=md5(md5($this->appid.$this->appkey).$time);
		if ($isHeader){
			return ['time:'.$time,
            'appid:'.$this->appid,
            'sign:'.$sign];
		}else{
			return ['time'=>$time,'sign'=>$sign,'appid'=>$this->appid];
		}
	}

	/**
	 * authUrl
	 *
	 * @param string $url
	 * @param string $state=''
	 * @return string
	 */
	function authUrl($url,$state=''){
		$param=http_build_query(['url'=>$url,'state'=>$state,'appid'=>$this->appid]);
		return self::HOST.'main/index?'.$param;
	}

	/**
	 * userInfo
	 *
	 * @param string $code
	 * @return array user info
	 */
	function userInfo($code){
		$url=self::HOST.'api/userinfo?code='.$code;
		return $this->_send($url);
	}

	private function _send($url,$data=null,$method='GET'){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST,strtoupper($method));
		$data&&curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER,$this->buildParam());
		$response = curl_exec($ch);
		$response=json_decode($response,true);
		$httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($httpCode>=200&&$httpCode<300){
			return $response;
		}else{
			return $response?:['errcode'=>self::UNKNOWN];
		}
	}
}
