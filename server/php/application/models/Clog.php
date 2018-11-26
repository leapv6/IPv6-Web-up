<?php
class Clog extends CI_Model {
	function log(string $message)
	{
		$data=['uid'=>UID,'type'=>$this->auth->data['kind'],'msg'=>$message];
		return $this->db->insert('opraLog',$data);
	}
}
