<?php
/*
 * 异步回调通知页面
 */
include '../../../config.inc.php';
require_once 'libs/ispay/lib/Ispay.class.php';
date_default_timezone_set('Asia/Shanghai');

$db = Typecho_Db::get();
$options = Typecho_Widget::widget('Widget_Options');
$option=$options->plugin('TleQiTao');
switch($option->tleqitaopaytype){
	case "youzan":
		/*有赞回调*/
		$json = file_get_contents('php://input'); 
		$data = json_decode($json, true);
		/**
		 * 判断消息是否合法，若合法则返回成功标识
		 */
		$msg = $data['msg'];
		$sign_string = $option->tleqitaoyz_client_id."".$msg."".$option->tleqitaoyz_client_secret;
		$sign = md5($sign_string);
		if($sign != $data['sign']){
			exit();
		}else{
			$result = array("code"=>0,"msg"=>"success") ;
		}
		/**
		 * msg内容经过 urlencode 编码，需进行解码
		 */
		$msg = json_decode(urldecode($msg),true);
		/**
		 * 根据 type 来识别消息事件类型，具体的 type 值以文档为准，此处仅是示例
		 */
		if($data['type'] == "trade_TradePaid"){
			$qrNameArr=explode("|",$msg["qr_info"]["qr_name"]);
			$data = array(
				"orderNumber"=>$data['id'],
				"payChannel"=>$msg["full_order_info"]["order_info"]["pay_type_str"],
				"Money"=>$qrNameArr[1],
				"attachData"=>$qrNameArr[2],
				"status"=>'y',
				"instime"=>date('Y-m-d H:i:s',time())
			);
			$insert = $db->insert('table.tleqitao_item')->rows($data);
			$insertId = $db->query($insert);
		}
		break;
	case "ispay":
		$Ispay = new ispayService($option->tleqitaoispayid, $option->tleqitaoispaykey);
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
		break;
}
?>