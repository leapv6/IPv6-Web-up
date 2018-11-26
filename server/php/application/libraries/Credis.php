<?php
class Credis{
	var $obj=null;

	function __construct($config=[]) {
		foreach ($config as $key => $value) {
			$this->$key=$value;
		}
	}

	function init() {
		$this->obj = new redis();
		$succ=$this->obj->connect($this->host,$this->port);
		if (!$succ) throw new MyException('服务器挂了……',MyException::THIRD);
		empty($this->pass) || $this->obj->auth($this->pass);
		$this->obj->select($this->db);
	}
	
	function __call($name,$param) {
		if ($this->obj==null) $this->init();
		if (($name=='set'||$name=='lPush')&&is_array($param[1]))
			$param[1]=json_encode($param[1]);
		$res=call_user_func_array([$this->obj,$name],$param);
		return ($name=='get'&&count($param)==1)?json_decode($res,true):$res;
	}
}
