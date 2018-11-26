<?php
class Uploadsdk{
	var $appid=0;
	var $key='';
	public $filename=null;
	public $subfolder=null;
	public $expire=3600;
	//其他参数和CI的upload类一致
	function __construct($config=[]) {
		foreach ($config as $key => $value) {
			$this->$key=$value;
		}
	}

	function token($param=[]) {
		if ($this->filename!=null){
			$param['file_name']=$this->filename;
			$param['overwrite']=true;
		}
		if ($this->subfolder!=null){
			$param['subfolder']=$this->subfolder;
		}
		$param['expire']=time()+$this->expire;
		$param['id']=$this->appid;
		$str=http_build_query($param);
		$param['sign']=md5(md5($str).$this->key);
		return json_encode($param);
	}
}
