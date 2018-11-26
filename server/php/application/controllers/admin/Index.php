<?php
class IndexController extends CI_Controller {
	function __construct() {
		parent::__construct();
		if (!$this->auth->isAdmin())
		throw new MyException('',MyException::AUTH);
	}

	function index()
	{
		$data=$this->db->select('sum(flow) totalFlow,count(*) totalNum,'.
		'(SELECT count(*) FROM v6site) totalUp,'.
		'(SELECT count(*) FROM user WHERE parent=0) totalUser')
		->get('v6')->row_array();
		restful(200,$data);
	}
}
