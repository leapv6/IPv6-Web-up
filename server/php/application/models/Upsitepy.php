<?php
class Upsitepy extends CI_Model {
	function bind($site,int $server)
	{
		if ($site['isHttps']==0){
			unset($site['isBoth'],$site['cert']);
		}else{
			$site['cert']=json_decode($site['cert'],true);
		}
		$s=$this->_socket();
		$addr=$this->db->find('server',$server,'id','ipaddr')['ipaddr'];
		$this->db->insert('downLog',['server'=>$server,'opt'=>"绑定$site[v6url]到服务器$server"]);
		$param=['cmd'=>'add','reqid'=>$this->db->insert_id(),'server'=>$addr,'site'=>$site];
		socket_write($s,json_encode($param));
    	socket_close($s);
	}

	function unbind($v6url,int $server)
	{
		$s=$this->_socket();
		$addr=$this->db->find('server',$server,'id','ipaddr')['ipaddr'];
		$this->db->insert('downLog',['server'=>$server,'opt'=>"解绑$v6url 到服务器$server"]);
		$param=['cmd'=>'del','reqid'=>$this->db->insert_id(),'server'=>$addr,'site'=>['v6url'=>$v6url]];
		socket_write($s,json_encode($param));
    	socket_close($s);
	}

	function _socket()
	{
		$s=socket_create(AF_INET6,SOCK_STREAM,SOL_TCP);
		if (!socket_connect($s,'::1',6446)){
			log_message('error',socket_strerror(socket_last_error()));
			throw new MyException('后端服务挂了！请联系系统管理员。',MyException::THIRD);
		}
		return $s;
	}
}
