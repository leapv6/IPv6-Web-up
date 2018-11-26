<?php
class MainController extends CI_Controller {
	function addToken() {
		$data=$this->input->post(['name','pwd']);
		if (!$data) throw new MyException('',MyException::INPUT_MISS);
		restful(200,$this->account->login($data));
	}
	
	function addAdminToken() {
		$data=$this->input->post(['name','pwd']);
		if (!$data) throw new MyException('',MyException::INPUT_MISS);
		restful(200,$this->account->login($data,true));
	}

	function oauth(int $isAdmin=0){
		$url=$this->input->get('url');
		if (!$url) throw new MyException('',MyException::INPUT_MISS);
		$this->config->load('oauth',true);
		$config=$this->config->item('oauth');
		$this->load->library('oauth',$config[$isAdmin?'manage':'app']);
		$url=$this->oauth->authUrl($url);
		jump($url);
	}

	function oauthcallback(int $isAdmin=0){
		$code=$this->input->get('code');
		if (!$code) throw new MyException('',MyException::INPUT_MISS);
		$this->config->load('oauth',true);
		$config=$this->config->item('oauth');
		$this->load->library('oauth',$config[$isAdmin?'manage':'app']);
		$info=$this->oauth->userInfo($code);
		if (isset($info['openid'])){
			restful(200,$this->account->oauth($info['openid'],$isAdmin));
		}else {
			throw new MyException('认证失败！请重试',MyException::THIRD);
		}
	}
}