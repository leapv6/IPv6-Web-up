<?php
class ServerController extends CI_Controller {
	function __construct() {
		parent::__construct();
		if (!$this->auth->isAdmin())
		throw new MyException('',MyException::AUTH);
	}
	function server(int $id=0){
		if ($id==0){
			$page=(int)$this->input->get('page');
			$count=(int)$this->input->get('count',false,15);
			($t=$this->input->get('name'))&&$this->db->like('server.name',$t);
			($t=$this->input->get('ipaddr'))&&$this->db->like('ipaddr',$t);
			($t=$this->input->get('mask'))&&$this->db->like('mask',$t);
			($t=$this->input->get('addr'))&&$this->db->where('addr',$t);
			$total=$this->db->count_all_results('server',false);
			$data=$this->db->select('server.*,addr.name addr,(SELECT count(*) FROM v6site WHERE sid=server.id) num')
			->join('addr','addr.id=addr')
			->order_by('id','desc')
			->get('',$count,$page*$count)->result_array();
			restful(200,['total'=>$total,'data'=>$data]);
		}else{
			if ($data=$this->db->find('server', $id)){
				$data['pics']=json_decode($data['pics'],TRUE);
				restful(200,$data);
			}else throw new MyException('',MyException::GONE);
		}
	}

	function serverBrief()
	{
		$data=$this->db->select('id,name')
		->order_by('id','desc')
		->get('server')->result_array();
		restful(200,$data);
	}

	function delServer(int $id=0){
		$item=$this->db->find('server',$id);
		if ($this->db->where('id',$id)->delete('server')){
			$this->clog->log("删除服务器 $id");
			$this->load->library('credis');
			$this->credis->del($data['ipaddr']);
			restful();
		}else throw new MyException('',MyException::DATABASE);
	}

	function modServer(int $id=0){
		$data=$this->input->put(['name','version']);
		if (!$data) throw new MyException('',MyException::INPUT_MISS);
		if ($this->db->where('id',$id)->update('server',$data)) restful();
		else throw new MyException('',MyException::DATABASE);
	}

	function addServer(){
		$data=$this->db->create('server');
		if (!$data) throw new MyException('',MyException::INPUT_MISS);
		if ($this->db->insert('server',$data)){
			$this->clog->log("添加服务器 $data[ipaddr]");
			$this->load->library('credis');
			$this->credis->hset($data['ipaddr'],'time',0);
			restful(201);
		}else throw new MyException('',MyException::DATABASE);
	}

	/**
	 * 区域列表
	 *
	 * @return void
	 */
	function addr(int $id=0){
		$total=$this->db->count_all_results('addr',false);
		$data=$this->db->select('addr.*,(SELECT count(*) FROM server WHERE addr=addr.id) num')
		->order_by('id','desc')
		->get('')->result_array();
		restful(200,['total'=>$total,'data'=>$data]);
	}

	function addrBrief()
	{
		$data=$this->db->select('id,name')
		->order_by('id','desc')
		->get('addr')->result_array();
		restful(200,$data);
	}

	function delAddr(int $id=0){
		if ($this->db->where('id',$id)->delete('addr')) restful();
		else throw new MyException('请检查此区域是否有服务',MyException::DATABASE);
	}

	function modAddr(int $id=0){
		$data=$this->db->create('addr',FALSE);
		if (!$data) throw new MyException('',MyException::INPUT_MISS);
		if ($this->db->where('id',$id)->update('addr',$data)) restful();
		else throw new MyException('',MyException::DATABASE);
	}

	function addAddr(){
		$data=$this->db->create('addr');
		if (!$data) throw new MyException('',MyException::INPUT_MISS);
		if ($this->db->insert('addr',$data)) restful(201);
		else throw new MyException('',MyException::DATABASE);
	}
}
