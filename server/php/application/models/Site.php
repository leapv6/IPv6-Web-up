<?php
class Site extends CI_Model {
	public function bindV6(int $sid,int $vid)
	{
		$site=$this->db->find('site',$sid,'id','v4url,v6url,isHttps,isBoth,cert,belong');
		if ($this->auth->data['kind']==Auth::USER && $site['belong']!=BELONG){
			throw new MyException('',MyException::NO_RIGHTS);
		}
		$this->db->trans_begin();
		$v6=$this->db->query("SELECT belong,site,server,ipaddr FROM v6 WHERE id=$vid for update")->row_array();
		if ($v6['belong']!=$site['belong']) throw new MyException('V6地址与域名不属于同一用户！',MyException::NO_RIGHTS);
		if ($v6['site']<=$this->db->where('vid',$vid)->count_all_results('v6site'))
		throw new MyException('V6地址绑定域名已达上限',MyException::NO_RIGHTS);
		$site['v6addr']=$v6['ipaddr'];
		$this->upsitepy->bind($site,$v6['server']);
		$this->db->insert('v6site',['vid'=>$vid,'sid'=>$sid]);
		if ($this->db->trans_complete()){
			$this->clog->log("绑定域名 $sid 到ip $vid");
			restful(201);
		}else throw new MyException('绑定失败，请检查是否已升级并重试。',MyException::DATABASE);
	}

	public function unbindV6(int $sid,int $vid)
	{
		$this->db->trans_begin();
		$has=$this->db->query("SELECT * FROM v6site WHERE sid=$sid and vid=$vid for update")->row_array();
		if (!$has) throw new MyException('',MyException::DONE);

		$v6=$this->db->find('v6',$vid,'id','server');
		$site=$this->db->find('site',$sid,'id','v6url');
		$this->upsitepy->unbind($site['v6url'],$v6['server']);
		$this->db->delete('v6site',['vid'=>$vid,'sid'=>$sid]);
		if ($this->db->trans_complete()){
			$this->clog->log("解绑域名 $sid 到ip $vid");
			restful();
		}else throw new MyException('',MyException::DATABASE);
	}

	function addSite($data)
	{
		if (empty($data['v4url'])||empty($data['v6url']))
		throw new MyException('',MyException::INPUT_MISS);
		$succ=preg_match('/^[a-z0-9_.-:]+$/i',$data['v6url']);
		if (!$succ) throw new MyException('v6域名错误',MyException::INPUT_ERR);

		$data['isHttps']=$data['isHttps']==0?0:1;
		if ($data['isHttps']==1){
			$cert=json_decode($data['cert']??'none',true);
			if (!$cert||empty($cert['cert'])||empty($cert['key']))
			throw new MyException('请上传证书文件',MyException::INPUT_MISS);
		}else{
			$data['cert']=null;
		}
		if (!$this->db->insert('site',$data)) throw new MyException('',MyException::DATABASE);
		$this->clog->log("添加域名$data[v6url]");
		return true;
	}

	public function delSite(int $id=0):bool
	{
		$site=$this->db->find('site',$id,'id','v6url');
		$bind=$this->db->where('sid',$id)->get('v6site')->result_array();
		foreach ($bind as $value) {
			$this->unbindV6($id,$value['vid']);
		}
		if ($this->db->where('id',$id)->delete('site')){
			$this->clog->log("删除id为 ${id} 的域名$site[v6url]");
			return true;
		}else return false;
	}
	
	//更新https域名的证书
	function updateSite(int $id=0)
	{
		$site=$this->db->find('site',$id,'id','v4url,v6url,isHttps,isBoth,cert');
		$vids=$this->db->where('sid',$id)->select('vid')
		->get('v6site')->result_array();
		foreach ($vids as $vid) {
			$v6=$this->db->query("SELECT server,ipaddr FROM v6 WHERE id=$vid")->row_array();
			$site['v6addr']=$v6['ipaddr'];
			$this->upsitepy->bind($site,$v6['server']);
		}
	}
}
