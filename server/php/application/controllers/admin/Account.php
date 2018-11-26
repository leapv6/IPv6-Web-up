<?php
class AccountController extends CI_Controller {
	function __construct() {
		parent::__construct();
		if (!$this->auth->isAdmin())
		throw new MyException('',MyException::AUTH);
	}

	function admin(){
		$page=(int)$this->input->get('page');
		$count=(int)$this->input->get('count',false,10);
		$total=$this->db->where('id >',1)->count_all_results('admin',false);
		$data=$this->db->select('id,name')->get('',$count,$page*$count)->result_array();
		restful(200,['data'=>$data,'total'=>$total]);
	}
	
	function delAdmin(int $id=0){
		if ($id==1) throw new MyException('',MyException::INPUT_ERR);
		if ($this->db->where('id',$id)->delete('admin')){
			$this->clog->log("删除管理员 $id");
			restful(201);
		}else throw new MyException('',MyException::DATABASE);
	}
	
	function modAdmin(int $id=0){
		if ($id==1) throw new MyException('',MyException::INPUT_ERR);
		$data=$this->input->put('pwd');
		if (empty($data)) throw new MyException('',MyException::INPUT_MISS);
		if ($this->account->resetPwd(['pwd'=>$data,'id'=>$id],true)){
			$this->clog->log("重置管理员 $id 的密码");
			restful();
		}else throw new MyException('',MyException::DATABASE);
	}
	
	function addAdmin(){
		$data=$this->db->create('admin');
		if ($this->account->add($data,true)){
			$this->clog->log("添加管理员 ".$this->db->insert_id());
			restful(201);
		}else throw new MyException('',MyException::DATABASE);
	}

	function user(){
		$page=(int)$this->input->get('page');
		$count=(int)$this->input->get('count',false,10);
		$total=$this->db->where('parent',0)->count_all_results('user',false);
		$data=$this->db->select('id,name,nickName,status,tel,contact,time')
		->get('',$count,$page*$count)->result_array();
		restful(200,['data'=>$data,'total'=>$total]);
	}

	function userBrief()
	{
		$data=$this->db->select('id,nickName')
		->where('parent',0)
		->order_by('id','desc')
		->get('user')->result_array();
		restful(200,$data);
	}
	
	function modUser(int $id=0){
		$data=$this->input->put(['pwd','status'],false,true);
		if (empty($data)) throw new MyException('',MyException::INPUT_MISS);
		if (isset($data['pwd'])){
			$succ=$this->account->resetPwd(['id'=>$id,'pwd'=>$data['pwd']],false);
		}else $succ=$this->db->where('id',$id)->update('user',$data);
		if ($succ){
			$this->clog->log("重置用户 $id 的密码");
			restful();
		}else throw new MyException('',MyException::DATABASE);
	}

	function addUser(){
		$data=$this->db->create('user');
		$data['token']=md5(uniqid());
		if ($this->account->add($data,false)){
			$this->clog->log("添加用户 ".$this->db->insert_id());
			restful(201);
		}else throw new MyException('',MyException::DATABASE);
	}
}