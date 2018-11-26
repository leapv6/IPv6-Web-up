<?php
/**
 * 查看v6地址和绑定信息
 */
class V6addrController extends CI_Controller {
	function __construct() {
		parent::__construct();
		if (!$this->auth->isUser())
		throw new MyException('',MyException::AUTH);
	}

	function addr(int $id=0){
		if ($id==0){
			$page=(int)$this->input->get('page');
			$count=(int)$this->input->get('count',false,15);
			$total=$this->db->where('belong',BELONG)->count_all_results('v6',false);
			$data=$this->db->select('v6.*,(SELECT count(*) from v6site where vid=v6.id) bindNum')
			->get('',$count,$page*$count)->result_array();
			restful(200,['total'=>$total,'data'=>$data]);
		}else{
			if ($data=$this->db->find('addr', $id)){
				restful(200,$data);
			}else throw new MyException('',MyException::GONE);
		}
	}

	function addrBrief()
	{
		restful(200,$this->db->where('belong',BELONG)
		->select('id,`desc`,ipaddr')->get('v6')->result_array());
	}

	function modAddr(int $id=0){
		$data=$this->input->put(['desc']);
		if (!$data) throw new MyException('',MyException::INPUT_MISS);
		if ($this->db->where(['id'=>$id,'belong'=>BELONG])->update('v6',$data)) restful();
		else throw new MyException('',MyException::DATABASE);
	}

	/**
	 * 网站升级列表
	 *
	 * @return void
	 */
	function v6addr()
	{
		$page=(int)$this->input->get('page');
		$count=(int)$this->input->get('count',false,15);
		($t=$this->input->get('sid'))&&$this->db->where('sid',$t);
		($t=$this->input->get('vid'))&&$this->db->where('vid',$t);
		$total=$this->db->join('v6','v6.id=v6site.vid')
		->join('site','site.id=v6site.sid and site.belong='.UID)
		->count_all_results('v6site',false);
		$data=$this->db->select('site.name site,v4url,v6url,v6.ipaddr,flow,v6.desc,company.name company,v6site.time,v6site.vid,v6site.sid')
		->join('company','company.id=site.company','left')
		->order_by('site.id','desc')
		->get('',$count,$page*$count)->result_array();
		restful(200,['total'=>$total,'data'=>$data]);
	}
}
