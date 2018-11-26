<?php
class Auth extends CI_Model {
    const ADMIN=1;
    const USER=0;
	function __construct() {
		parent::__construct();
        $this->data=getToken();
    }
    
    function isAdmin(){
        if ($this->data['kind']!=self::ADMIN) return false;
        $succ=$this->db->where(['id'=>$this->data['id'],'token'=>$this->data['token']])->count_all_results('admin')==1;
        if ($succ){
            define('UID',$this->data['id']);
            return true;
        }return false;
    }

    function isUser(){
        if ($this->data['kind']!=self::USER) return false;
        $user=$this->db->select('parent')->where(['id'=>$this->data['id'],'token'=>$this->data['token']])->get('user')->row_array();
        if ($user){
            define('BELONG',(int)($user['parent']==0?$this->data['id']:$user['parent']));
            define('UID',$this->data['id']);
            return true;
        }else return false;
    }

    function isLogin(){
        return ($this->isUser()||$this->isAdmin());
    }
}
