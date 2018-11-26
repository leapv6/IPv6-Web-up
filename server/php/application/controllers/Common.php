<?php
class CommonController extends CI_Controller {
	function __construct() {
		parent::__construct();
		if (!$this->auth->isLogin())
		throw new MyException('',MyException::AUTH);
	}

	function modMe() {
		$input=$this->input->put(['nickName','pwd','oldpwd']);
		if (!$input) throw new MyException('',MyException::INPUT_MISS);
		if ($input['oldpwd']!=''){
			$this->load->model('account');
			if (!$this->account->modPwd($input)) throw new MyException('',MyException::DATABASE);
		}
		$table=$this->auth->data['kind']==Auth::USER?'user':'admin';
		$id=$this->auth->data['id'];
		if ($this->db->where('id',$id)->update($table,['nickName'=>$input['nickName']]));
		else throw new MyException('',MyException::DATABASE);
	}

	function uploadToken()
	{
		$this->load->library('uploadsdk');
		restful(200,$this->uploadsdk->token());
	}

	function me()
	{
		$table=$this->auth->data['kind']==Auth::USER?'user':'admin';
		$info=$this->db->find($table,$this->auth->data['id'],'id','openid,oname');
		restful(200,$info);
	}

	function addBind()
	{
		$code=$this->input->post('code');
		if (!$code) throw new MyException('',MyException::INPUT_MISS);
		$isAdmin=$this->auth->data['kind']==Auth::ADMIN;
		$this->config->load('oauth',true);
		$config=$this->config->item('oauth');
		$this->load->library('oauth',$config[$isAdmin?'manage':'app']);
		$info=$this->oauth->userInfo($code);
		if (isset($info['openid'])){
			$table=$isAdmin?'admin':'user';
			$succ=$this->db->update($table,['openid'=>$info['openid'],'oname'=>$info['nickName']],['id'=>$this->auth->data['id']]);
			if ($succ){
				restful(201);
			}else throw new MyException('',MyException::DATABASE);
		}else {
			throw new MyException('认证失败！请重试',MyException::THIRD);
		}
	}
}
