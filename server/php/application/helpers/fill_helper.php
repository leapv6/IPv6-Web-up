<?php
//没有数据的日期补0
function fillDay($data,$begintime,$endtime) {
	$p=current($data);
	$p=$p?$p['date']:'';//不用判断p是否为false了
	$res=[];
	while ($begintime<$endtime) {
		$time=date('Y-m-d',$begintime);
		if ($p==$time){
			$res[]=current($data);
			$p=next($data);
			$p=$p?$p['date']:'';
		}else $res[]=['date'=>$time,'num'=>0];
		$begintime+=86400;
	}
	return $res;
}

//没有数据的周补0
function fillWeek($data,$begintime,$endtime) {
	$data=$data?:[['time'=>$endtime+1]];
	$p=current($data);
	$prebegin=$begintime;
	$res=[];
	while ($begintime<$endtime) {
		$begintime+=604800;
		$t=['time'=>date('m-d',$prebegin).'至'.date('m-d',$begintime)];
		$res[]=$p['time']<=$begintime?array_merge($p,$t):$t;//在区间内，设值，否则无
		if ($p['time']<=$begintime)
			$p=next($data);
		$p=$p?:['time'=>$endtime+1];
		$prebegin=$begintime;
	}
	return $res;
}

//没有数据的月份补0
function fillMonth($data,$begintime,$endtime) {
	$data=$data?:[['time'=>'']];//设为空，不会被匹配到
	$p=current($data);
	$res=[];
	while ($begintime<$endtime) {//endtime是下个月的，可以不要
		$date=date('Y/m',$begintime);
		if ($p['time']!=$date){
			$res[]=['time'=>$date];
		}else{
			$res[]=$p;
			$p=next($data)?:['time'=>''];
		}
		$begintime=strtotime('+1 Month',$begintime);
	}
	return $res;
}