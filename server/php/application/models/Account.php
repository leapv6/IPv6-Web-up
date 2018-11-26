<?php
class Account extends CI_Model {
	const KEY='upsite';
	//添加账号
	function add($input,$isAdmin=false) {
		if (isset($input['id'])) throw new MyException('',MyException::INPUT_ERR);
		$input['pwd']=md5(md5($input['pwd']).self::KEY);
		return $this->db->insert($isAdmin?'admin':'user',$input);
	}
	
	function modPwd($input) {
		$id=$this->auth->data['id'];
		$table=$this->auth->data['kind']==0?'user':'admin';
		$old=$this->db->find($table,$id,'id','pwd')['pwd'];
		if ($old==md5(md5($input['oldpwd']).self::KEY)){
			return $this->db->where('id',$id)
			->update($table,['pwd'=>md5(md5($input['pwd']).self::KEY)]);
		}else throw new MyException('密码错误!',MyException::INPUT_ERR);
	}
	
	//重置密码
	function resetPwd($input,$isAdmin=false) {
		return $this->db->where('id',$input['id'])
			->update($isAdmin?'admin':'user',['pwd'=>md5(md5($input['pwd']).self::KEY)]);
	}
	
	function login($data,$isAdmin=false) {
		$table=$isAdmin?'admin':'user';
		$user=$this->db->where('name',$data['name'])->get($table,1)->row_array();
		$log=['ip'=>$this->input->ip_address(),'user'=>$data['name'],'pwd'=>$data['pwd']];
		if (!$user){
			$this->db->insert('loginLog',$log);
			throw new MyException('用户不存在！',MyException::INPUT_ERR);
		}
		if ($user['pwd']==md5(md5($data['pwd']).self::KEY)){
			return $this->_login($user,$table);
		}else{
			$this->db->insert('loginLog',$log);
			throw new MyException('密码错误!',MyException::INPUT_ERR);
		}
	}

	function oauth($openid,$isAdmin=false){
		$table=$isAdmin?'admin':'user';
		$user=$this->db->find($table,$openid,'openid');
		if (!$user) throw new MyException('请确认帐号绑定正确！',MyException::INPUT_ERR);
		return $this->_login($user,$table);
	}

	function _login($user,$table){
		$token=md5(uniqid());
		$update=['token'=>$token];
		if ($table=='user'){
			if ($user['status']!=0){
				throw new MyException('帐号被冻结，请联系管理员',MyException::NO_RIGHTS);
			}
			$res['isAdmin']=$user['parent']==0;
		}
		$this->db->where('id',$user['id'])->update($table,$update);
		$res['token']=$token;
		$res['id']=$user['id'];
		$res['name']=$user['nickName'];
		return $res;
	}
}