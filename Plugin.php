<?php
/**
 * TleQiTao是一个全自动24小时在线乞讨Typecho插件，进入插件设置可检测版本更新。<a href="https://github.com/muzishanshi/TleQiTaoForTypecho" target="_blank">Github地址</a>
 * @package TleQiTao For Typecho
 * @author 二呆
 * @version 1.0.3
 * @link http://www.tongleer.com/
 * @date 2019-03-22
 */
date_default_timezone_set('Asia/Shanghai');
class TleQiTao_Plugin implements Typecho_Plugin_Interface{
    // 激活插件
    public static function activate(){
		$db = Typecho_Db::get();
		$prefix = $db->getPrefix();
		self::createTableQiTaoItem($db);
		//判断目录权限，并将插件文件写入主题目录
		self::funWriteThemePage($db,'page_tleqitao.php');
		//如果数据表没有添加页面就插入
		self::funWriteDataPage($db,'乞讨','tleqitao','page_tleqitao.php','publish');
        return _t('插件已经激活，需先配置乞讨信息！');
    }

    // 禁用插件
    public static function deactivate(){
		//删除页面模板
		$db = Typecho_Db::get();
		$queryTheme= $db->select('value')->from('table.options')->where('name = ?', 'theme'); 
		$rowTheme = $db->fetchRow($queryTheme);
		@unlink(dirname(__FILE__).'/../../themes/'.$rowTheme['value'].'/page_tleqitao.php');
        return _t('插件已被禁用');
    }

    // 插件配置面板
    public static function config(Typecho_Widget_Helper_Form $form){
		$options = Typecho_Widget::widget('Widget_Options');
		$plug_url = $options->pluginUrl;
		//版本检查
		$version=file_get_contents('https://www.tongleer.com/api/interface/TleQiTao.php?action=update&version=3');
		$headDiv=new Typecho_Widget_Helper_Layout();
		$headDiv->html('版本检查：'.$version);
		$headDiv->render();
		
		$tleqitaopaytype = new Typecho_Widget_Helper_Form_Element_Radio('tleqitaopaytype', array(
            'ispay'=>_t('ispay'),
            'youzan'=>_t('有赞')
        ), 'youzan', _t('有赞'), _t("支付渠道"));
        $form->addInput($tleqitaopaytype->addRule('enum', _t(''), array('ispay', 'youzan')));
		
        $tleqitaoispayid = new Typecho_Widget_Helper_Form_Element_Text('tleqitaoispayid', null, '', _t('ispayid'), _t('在<a href="https://www.ispay.cn/" target="_blank">ispay官网</a>注册的payId'));
        $form->addInput($tleqitaoispayid);
		$tleqitaoispaykey = new Typecho_Widget_Helper_Form_Element_Text('tleqitaoispaykey', null, '', _t('ispaykey'), _t('在<a href="https://www.ispay.cn/" target="_blank">ispay官网</a>注册的payKey'));
        $form->addInput($tleqitaoispaykey);
		
		$tleqitaoyz_client_id = new Typecho_Widget_Helper_Form_Element_Text('tleqitaoyz_client_id', null, '', _t('有赞client_id'), _t('在<a href="https://www.youzanyun.com/app/sdk" target="_blank">有赞App开店</a>授权绑定有赞微小店APP的店铺后注册的client_id'));
        $form->addInput($tleqitaoyz_client_id);
		$tleqitaoyz_client_secret = new Typecho_Widget_Helper_Form_Element_Text('tleqitaoyz_client_secret', null, '', _t('有赞client_secret'), _t('在<a href="https://www.youzanyun.com/app/sdk" target="_blank">有赞App开店</a>授权绑定有赞微小店APP的店铺后注册的client_secret'));
        $form->addInput($tleqitaoyz_client_secret);
		$tleqitaoyz_shop_id = new Typecho_Widget_Helper_Form_Element_Text('tleqitaoyz_shop_id', null, '', _t('有赞授权店铺id'), _t('在<a href="https://www.youzanyun.com/app/sdk" target="_blank">有赞App开店</a>授权绑定有赞微小店APP的店铺后注册的授权店铺id'));
        $form->addInput($tleqitaoyz_shop_id);
		$tleqitaoyz_redirect_url = new Typecho_Widget_Helper_Form_Element_Text('tleqitaoyz_redirect_url', array("value"), $plug_url.'/TleQiTao/notify_url.php', _t('有赞消息推送网址'), _t('在<a href="https://www.youzanyun.com/app/sdk" target="_blank">有赞App开店</a>授权绑定有赞微小店APP的店铺后注册的消息推送网址'));
        $form->addInput($tleqitaoyz_redirect_url);
		
		$tleqitaoqrcodetype = new Typecho_Widget_Helper_Form_Element_Radio('tleqitaoqrcodetype', array(
            'QR_TYPE_FIXED_BY_PERSON'=>_t('无金额'),
            'QR_TYPE_NOLIMIT'=>_t('固定金额且可以重复支付'),
			'QR_TYPE_DYNAMIC'=>_t('固定金额且只可支付一次')
        ), 'QR_TYPE_DYNAMIC', _t('固定金额且只可支付一次'), _t("支付二维码种类"));
        $form->addInput($tleqitaoqrcodetype->addRule('enum', _t(''), array('QR_TYPE_FIXED_BY_PERSON', 'QR_TYPE_NOLIMIT', 'QR_TYPE_DYNAMIC')));
		
		$tleqitaoshoptype = new Typecho_Widget_Helper_Form_Element_Radio('tleqitaoshoptype', array(
            'oauth'=>_t('工具型'),
            'self'=>_t('自用型')
        ), 'self', _t('自用型'), _t("店铺应用种类"));
        $form->addInput($tleqitaoshoptype->addRule('enum', _t(''), array('oauth', 'self')));
		
		$tleqitaoqq = new Typecho_Widget_Helper_Form_Element_Text('tleqitaoqq', array("value"), '2293338477', _t('QQ号'), _t('通过QQ号自动获取头像地址和联系QQ链接'));
        $form->addInput($tleqitaoqq);
		$tleqitaotalk = new Typecho_Widget_Helper_Form_Element_Text('tleqitaotalk', array("value"), '', _t('想说的话'), _t('如果填写用户可看到你乞讨的缘由'));
        $form->addInput($tleqitaotalk);
		$tleqitaoisaudio = new Typecho_Widget_Helper_Form_Element_Radio('tleqitaoisaudio', array(
            'y'=>_t('启用'),
            'n'=>_t('禁用')
        ), 'y', _t('是否开启背景乞讨歌'), _t("启用后乞讨页面会出现乞讨歌"));
        $form->addInput($tleqitaoisaudio->addRule('enum', _t(''), array('y', 'n')));
    }
	
    // 个人用户配置面板
    public static function personalConfig(Typecho_Widget_Helper_Form $form){
    }

    // 获得插件配置信息
    public static function getConfig(){
        return Typecho_Widget::widget('Widget_Options')->plugin('TleQiTao');
    }
	
	/*创建乞讨数据表*/
	public static function createTableQiTaoItem($db){
		$prefix = $db->getPrefix();
		//$db->query('DROP TABLE IF EXISTS '.$prefix.'weibofile_videoupload');
		$db->query('CREATE TABLE IF NOT EXISTS `'.$prefix.'tleqitao_item` (
		  `orderNumber` varchar(125) COLLATE utf8_general_ci NOT NULL,
		  `payChannel` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
		  `Money` double(10,2) DEFAULT NULL,
		  `attachData` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
		  `status` enum("y","n") COLLATE utf8_general_ci DEFAULT "n",
		  `instime` datetime DEFAULT NULL,
		  PRIMARY KEY (`orderNumber`)
		) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;');
	}
	
	/*公共方法：将页面写入数据库*/
	public static function funWriteDataPage($db,$title,$slug,$template,$status="hidden"){
		$query= $db->select('slug')->from('table.contents')->where('template = ?', $template); 
		$row = $db->fetchRow($query);
		if(count($row)==0){
			$contents = array(
				'title'      =>  $title,
				'slug'      =>  $slug,
				'created'   =>  date('Y-m-d H:i:s',time()),
				'text'=>  '<!--markdown-->',
				'password'  =>  '',
				'authorId'     =>  Typecho_Cookie::get('__typecho_uid'),
				'template'     =>  $template,
				'type'     =>  'page',
				'status'     =>  $status,
			);
			$insert = $db->insert('table.contents')->rows($contents);
			$insertId = $db->query($insert);
			$slug=$contents['slug'];
		}else{
			$slug=$row['slug'];
		}
	}
	
	/*公共方法：将页面写入主题目录*/
	public static function funWriteThemePage($db,$filename){
		$queryTheme= $db->select('value')->from('table.options')->where('name = ?', 'theme'); 
		$rowTheme = $db->fetchRow($queryTheme);
		$path=dirname(__FILE__).'/../../themes/'.$rowTheme['value'];
		if(!is_dir($path."/templates/")){
			mkdir ($path."/templates/", 0777, true );
		}
		if(!is_writable($path)){
			Typecho_Widget::widget('Widget_Notice')->set(_t('主题目录不可写，请更改目录权限。'.__TYPECHO_THEME_DIR__.'/'.$rowTheme['value']), 'success');
		}
		if(!file_exists($path."/".$filename)){
			$regfile = @fopen(dirname(__FILE__)."/page/".$filename, "r") or die("不能读取".$filename."文件");
			$regtext=fread($regfile,filesize(dirname(__FILE__)."/page/".$filename));
			fclose($regfile);
			$regpage = fopen($path."/".$filename, "w") or die("不能写入".$filename."文件");
			fwrite($regpage, $regtext);
			fclose($regpage);
		}
	}

}