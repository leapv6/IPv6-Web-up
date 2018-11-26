<?php
class SiteController extends CI_Controller {
	function __construct() {
		parent::__construct();
		if (!$this->auth->isUser())
		throw new MyException('',MyException::AUTH);
	}

	function site(int $id=0){
		if ($id==0){
			$page=(int)$this->input->get('page');
			$count=(int)$this->input->get('count',false,15);
			($t=$this->input->get('name'))&&$this->db->like('name',$t);
			($t=$this->input->get('url'))&&$this->db->like('url',$t);
			$total=$this->db->where('site.belong',BELONG)->count_all_results('site',false);
			$data=$this->db->select('site.*,(select count(*) from v6site where sid=site.id) v6num,company.name company')
			->join('company','company.id=company','left')
			->order_by('site.id','desc')
			->get('',$count,$page*$count)->result_array();
			restful(200,['total'=>$total,'data'=>$data]);
		}else{
			
		}
	}

	function siteBrief()
	{
		restful(200,$this->db->where('belong',BELONG)
		->select('id,name')->get('site')->result_array());
	}

	function addSite(){
		$data=$this->db->create('site');
		if (!$data) throw new MyException('',MyException::INPUT_MISS);
		$data['belong']=BELONG;
		if ($data['company']==0) $data['company']=null;
		$this->site->addSite($data);
		restful(201);
	}

	function delSite(int $id=0){
		$site=$this->db->find('site',$id);
		if (!$site) throw new MyException('',MyException::GONE);
		if ($site['belong']!=BELONG) throw new MyException('',MyException::NO_RIGHTS);
		if ($this->site->delSite($id)) restful();
		else throw new MyException('',MyException::DATABASE);
	}

	function modSite(int $id=0){
		$site=$this->db->find('site',$id,'id','belong');
		if (!$site) throw new MyException('',MyException::GONE);
		if ($site['belong']!=BELONG) throw new MyException('',MyException::NO_RIGHTS);
		$data=$this->input->put(['name','company','cert']);
		if (!$data) throw new MyException('',MyException::INPUT_MISS);
		if (empty($data['cert'])) unset($data['cert']);
		if ($data['company']==0) $data['company']=null;
		if ($this->db->where('id',$id)->update('site',$data)){
			if (isset($data['cert'])){
				$this->site->updateSite($id);
			}
			restful();
		}else throw new MyException('',MyException::DATABASE);
	}

	/**
	 * v6升级域名
	 *
	 * @return void
	 */
	function addBind()
	{
		$input=$this->input->post(['vid','sid']);
		if (!$input) throw new MyException('',MyException::INPUT_MISS);
		$this->site->bindV6($input['sid'],$input['vid']);
	}

	/**
	 * 取消v6升级域名
	 *
	 * @return void
	 */
	function delBind()
	{
		$input=$this->input->put(['vid','sid']);
		if (!$input) throw new MyException('',MyException::INPUT_MISS);
		$site=$this->db->find('site',$input['sid'],'id','belong');
		if (!$site||$site['belong']!=BELONG) throw new MyException('',MyException::NO_RIGHTS);
		$this->site->unbindV6($input['sid'],$input['vid']);
	}

	function companyBrief()
	{
		$data=$this->db->where('belong',BELONG)
		->order_by('id','desc')
		->get('company')->result_array();
		restful(200,$data);
	}

	//所属单位
	function company(){
		$page=(int)$this->input->get('page');
		$count=(int)$this->input->get('count',false,15);
		$total=$this->db->count_all_results('company',false);
		$data=$this->db->where('belong',BELONG)
		->order_by('id','desc')
		->get('',$count,$page*$count)->result_array();
		restful(200,['total'=>$total,'data'=>$data]);
	}

	function delCompany(int $id=0){
		if ($this->db->where(['id'=>$id,'belong'=>BELONG])->delete('company')) restful();
		else throw new MyException('',MyException::DATABASE);
	}

	function modCompany(int $id=0){
		$data=$this->db->field(['id','belong'],'company')->create('',FALSE);
		if (!$data) throw new MyException('',MyException::INPUT_MISS);
		if ($this->db->where(['id'=>$id,'belong'=>BELONG])->update('company',$data)) restful();
		else throw new MyException('',MyException::DATABASE);
	}

	function addCompany(){
		$data=$this->db->create('company');
		if (!$data) throw new MyException('',MyException::INPUT_MISS);
		$data['belong']=BELONG;
		if ($this->db->insert('company',$data)) restful(201);
		else throw new MyException('',MyException::DATABASE);
	}
}
