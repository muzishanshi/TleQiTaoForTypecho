<?php
/*
 * 异步回调通知页面
 */
include '../../../config.inc.php';
require_once 'libs/ispay/lib/Ispay.class.php';

$db = Typecho_Db::get();

$Ispay = new ispayService($option->tleqitaoispayid, $option->tleqitaoispaykey);
date_default_timezone_set('Asia/Shanghai');
$Array['payChannel'] = @$_POST['payChannel'];
$Array['Money'] = @$_POST['Money'];
$Array['orderNumber'] = @$_POST['orderNumber'];
$Array['attachData'] = @$_POST['attachData'];
$Array['callbackSign'] = @$_POST['callbackSign'];
if($Ispay->callbackSignCheck($Array)){
	//回调请求校验  (有效预防商户泄露payKey导致回调签名遭到破解的另一种校验方式,弊端会影响回调的成功率,要求安全性建议开启。) 开启请将下方注释//去掉
	//if(!$Ispay->callbackRequestCheck($Array)){echo "fail!";exit;}
	//<--------------------------商户业务代码写在下方-------------------------->
	$update = $db->update('table.tleqitao_item')->rows(array('status'=>'y'))->where('orderNumber=?',$Array['orderNumber']);
	$updateRows= $db->query($update);
	//<--------------------------商户业务代码写在上方-------------------------->
	//下方输出是告知ISPAY服务器业务受理成功,请不要修改下方输出内容,否则会导致重复通知,ISPAY服务器会在24小时内通知8次,输出SUCCESS则不再进行通知
	echo "SUCCESS";
}else{
	echo "callbackSign fail!";
	exit;
}
?>