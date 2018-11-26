<?php
/**
 * 查看v6地址和绑定信息
 */
class V6addrController extends CI_Controller {
	function __construct() {
		parent::__construct();
		if (!$this->auth->isAdmin())
		throw new MyException('',MyException::AUTH);
	}

	//查看v6地址
	function addr(int $id=0){
		if ($id==0){
			$page=(int)$this->input->get('page');
			$count=(int)$this->input->get('count',false,15);
			($t=$this->input->get('ipaddr'))&&$this->db->like('v6.ipaddr',$t);
			($t=$this->input->get('server'))&&$this->db->where('v6.server',$t);
			($t=$this->input->get('belong'))&&$this->db->where('v6.belong',$t);
			$total=$this->db->count_all_results('v6',false);
			$data=$this->db->select('v6.*,v6.belong belongId,addr.name addr,user.nickName belong,server.name server,(SELECT count(*) from v6site where vid=v6.id) bindNum')
			->join('server','server.id=v6.server')
			->join('addr','addr.id=server.addr')
			->join('user','user.id=v6.belong','left')
			->order_by('v6.id','desc')
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
		restful(200,$this->db->select('id,`desc`,ipaddr,belong')->get('v6')->result_array());
	}

	function delAddr(int $id=0){
		if ($this->db->where('id',$id)->delete('v6')){
			$this->clog->log('删除V6地址'.$id);
			restful();
		}else throw new MyException('',MyException::DATABASE);
	}

	function modAddr(int $id=0){
		$data=$this->input->put(['flow','site']);
		if (!$data) throw new MyException('',MyException::INPUT_MISS);
		if ($this->db->where('id',$id)->update('v6',$data)){
			$this->clog->log('修改V6地址'.$id);
			restful();
		}else throw new MyException('',MyException::DATABASE);
	}

	function addAddr(){
		$data=$this->db->create('v6');
		if (!$data) throw new MyException('',MyException::INPUT_MISS);
		if ($this->db->insert('v6',$data)){
			$this->clog->log('添加V6地址'.$data['ipaddr']);
			restful(201);
		}else throw new MyException('',MyException::DATABASE);
	}

	//取消分配v6地址
	function delAuth(int $id=0)
	{
		$user=$this->db->find('v6',$id,'id','belong');
		if (!$user) throw new MyException('',MyException::DONE);
		$succ=$this->db->where('id',$id)->update('v6',['belong'=>null]);
		if ($succ){
			$this->clog->log("取消V6地址 $id 对用户 $user[belong] 的授权");
			restful();
		}else throw new MyException('',MyException::DATABASE);
	}

	//分配v6地址
	function addAuth()
	{
		$input=$this->input->post(['vid','uid']);
		$v6=$this->db->find('v6',$input['vid'],'id','belong');
		if ($v6['belong']!=null) throw new MyException('',MyException::DONE);
		if ($this->db->where('id',$input['vid'])->update('v6',['belong'=>$input['uid']])){
			$this->clog->log("V6地址 $input[vid] 授权给用户 $input[uid]");
			restful(201);
		}else throw new MyException('',MyException::DATABASE);
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
		->join('site','site.id=v6site.sid')
		->count_all_results('v6site',false);
		$data=$this->db->select('user.nickName belong,site.name site,v4url,v6url,v6.ipaddr,flow,v6.desc,company.name company,v6site.time,v6site.vid,v6site.sid')
		->join('company','company.id=site.company','left')
		->join('user','user.id=site.belong')
		->order_by('site.id','desc')
		->get('',$count,$page*$count)->result_array();
		restful(200,['total'=>$total,'data'=>$data]);
	}
}
