<?php
spl_autoload_register(function($class) {
    if (substr($class,0,7)=='MongoDB'){
        $path=__DIR__.'/mongo/'.substr(strtr($class, '\\', DIRECTORY_SEPARATOR),8) . '.php';
        if (file_exists($path))
        require $path;
        return true;
    }
},true,true);
include_once __DIR__.'/mongo/functions.php';
class mongo{
    public $obj;
    private $select=[];
    private $data=[];
	function __construct($config) {
        $connection_string = "mongodb://";
        if (!empty($config['pass'])) $connection_string.="$config[user]:$config[pass]@";
        $connection_string .= "$config[host]:$config[port]";
        if (!empty($config['db'])) $connection_string.='/'.$config['db'];
        $this->obj=new MongoDB\Client($connection_string);
    }
    
    function page(int $count,int $page=0){
        $this->data['limit']=$count;
        $this->data['skip']=$page*$count;
        return $this;
    }

    function build($flush=true){
        $data=$this->data;
        if ($this->select){
            $data['projection']=$this->select;
        }
        if ($flush){
            $this->data=[];
            $this->select=[];
        }
        return $data;
    }

    function sort($key,$sort=-1){
        $this->data['sort'][$key]=$sort;
        return $this;
    }

    function select($key,$show=0){
        if (is_array($key)) $this->select=array_merge($this->select,$key);
        else $this->select[$key]=$show;
        return $this;
    }
}