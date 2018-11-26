<?php
class IndexController extends CI_Controller {
	function __construct() {
		parent::__construct();
		if (!$this->auth->isUser())
		throw new MyException('',MyException::AUTH);
	}

	function index()
	{
		$data=$this->db->select('sum(flow) totalFlow,count(*) totalNum,'.
		'(SELECT count(*) FROM v6site WHERE vid in (SELECT id FROM v6 WHERE belong='.BELONG.') ) totalUp,'.
		'(SELECT count(*) FROM site WHERE belong='.BELONG.') totalSite')
		->where('belong',BELONG)
		->get('v6')->row_array();
		restful(200,$data);
	}
}
