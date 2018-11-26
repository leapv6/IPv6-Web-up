<?php
class SiteController extends CI_Controller {
	function __construct() {
		parent::__construct();
		if (!$this->auth->isAdmin())
		throw new MyException('',MyException::AUTH);
	}

	/**
	 * 域名列表
	 *
	 * @return void
	 */
	function site(int $id=0){
		if ($id==0){
			$page=(int)$this->input->get('page');
			$count=(int)$this->input->get('count',false,15);
			($t=$this->input->get('name'))&&$this->db->like('name',$t);
			($t=$this->input->get('url'))&&$this->db->like('url',$t);
			($t=$this->input->get('belong'))&&$this->db->where('belong',$t);
			$total=$this->db->count_all_results('site',false);
			$data=$this->db->select('site.*,site.belong belongId,user.nickName belong,(select count(*) from v6site where sid=site.id) v6num')
			->join('user','user.id=site.belong')->order_by('site.id','desc')
			->get('',$count,$page*$count)->result_array();
			restful(200,['total'=>$total,'data'=>$data]);
		}else{
			
		}
	}

	function siteBrief()
	{
		restful(200,$this->db->select('id,name,belong')->get('site')->result_array());
	}

	function addSite(){
		$data=$this->db->create('site');
		if (!$data) throw new MyException('',MyException::INPUT_MISS);
		$this->site->addSite($data);
		restful(201);
	}

	//绑定域名解析
	function addBind()
	{
		$input=$this->input->post(['vid','sid']);
		if (!$input) throw new MyException('',MyException::INPUT_MISS);
		$this->site->bindV6($input['sid'],$input['vid']);
	}

	//取消域名解析
	function delBind()
	{
		$input=$this->input->put(['vid','sid']);
		if (!$input) throw new MyException('',MyException::INPUT_MISS);
		$this->site->unbindV6($input['sid'],$input['vid']);
	}
}