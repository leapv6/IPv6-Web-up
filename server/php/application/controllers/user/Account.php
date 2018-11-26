<?php
class AccountController extends CI_Controller {
	function __construct() {
		parent::__construct();
		if (!$this->auth->isUser())
		throw new MyException('',MyException::AUTH);
		if ($this->auth->data['id']!=BELONG) throw new MyException('',MyException::NO_RIGHTS);
	}

	function user(){
		$page=(int)$this->input->get('page');
		$count=(int)$this->input->get('count',10);
		$total=$this->db->where('parent',BELONG,false)->count_all_results('user u',false);
		$data=$this->db->select('id,name,nickName,status,time')
		->order_by('id','desc')
		->get('',$count,$page*$count)->result_array();
		restful(200,['data'=>$data,'total'=>$total]);
	}
	
	function delUser(int $id=0){
		$flag=$this->db->where(['id'=>$id,'parent'=>BELONG])->delete('user');
		if ($flag){
			$this->clog->log("删除子用户 $id");
			restful();
		}else throw new MyException('',MyException::DATABASE);
	}
	
	function modUser(int $id=0){
		$data=$this->input->put(['pwd','status'],false,true);
		if (empty($data)) throw new MyException('',MyException::INPUT_MISS);
		$this->db->where('parent',BELONG,false);
		if (isset($data['pwd'])){
			$succ=$this->account->resetPwd(['id'=>$id,'pwd'=>$data],false);
		}else $succ=$this->db->where('id',$id)->update('user',$data);
		if ($succ){
			$this->clog->log("重置用户 $id 的密码");
			restful(201);
		}else throw new MyException('',MyException::DATABASE);
	}
	
	function addUser(){
		$data=$this->db->create('user');
		$data['parent']=$this->auth->data['id'];
		if ($this->account->add($data,false)){
			$this->clog->log("添加子用户 ".$this->db->insert_id());
			restful(201);
		}else throw new MyException('',MyException::DATABASE);
	}
}