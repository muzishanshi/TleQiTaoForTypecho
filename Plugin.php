<?php
/**
 * TleQiTao是一个全自动24小时在线乞讨Typecho插件。<div class="TleQiTaoUpdateSet"><br /><a href="javascript:;" title="插件因兴趣于闲暇时间所写，故会有代码不规范、不专业和bug的情况，但完美主义促使代码还说得过去，如有bug或使用问题进行反馈即可。">鼠标轻触查看备注</a>&nbsp;<a href="http://club.tongleer.com" target="_blank">论坛</a>&nbsp;<a href="https://www.tongleer.com/api/web/pay.png" target="_blank">打赏</a>&nbsp;<a href="http://mail.qq.com/cgi-bin/qm_share?t=qm_mailme&email=diamond0422@qq.com" target="_blank">反馈</a></div><style>.TleQiTaoUpdateSet a{background: #4DABFF;padding: 5px;color: #fff;}</style>
 * @package TleQiTao For Typecho
 * @author 二呆
 * @version 1.0.5<br /><span id="TleQiTaoUpdateInfo"></span><script>TleQiTaoXmlHttp=new XMLHttpRequest();TleQiTaoXmlHttp.open("GET","https://www.tongleer.com/api/interface/TleQiTao.php?action=update&version=5",true);TleQiTaoXmlHttp.send(null);TleQiTaoXmlHttp.onreadystatechange=function () {if (TleQiTaoXmlHttp.readyState ==4 && TleQiTaoXmlHttp.status ==200){document.getElementById("TleQiTaoUpdateInfo").innerHTML=TleQiTaoXmlHttp.responseText;}}</script>
 * @link http://www.tongleer.com/
 * @date 2020-04-15
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
		$tleqitaopaytype = new Typecho_Widget_Helper_Form_Element_Radio('tleqitaopaytype', array(
            'payjs'=>_t('payjs')
        ), 'payjs', _t('支付渠道'), _t("支付渠道"));
        $form->addInput($tleqitaopaytype->addRule('enum', _t(''), array('payjs')));
		
        $tleqitao_mchid = new Typecho_Widget_Helper_Form_Element_Text('tleqitao_mchid', null, '', _t('payjs商户号'), _t('在<a href="https://payjs.cn/" target="_blank">payjs官网</a>注册的商户号'));
        $form->addInput($tleqitao_mchid);
		$tleqitao_key = new Typecho_Widget_Helper_Form_Element_Password('tleqitao_key', null, '', _t('payjs通信密钥'), _t('在<a href="https://payjs.cn/" target="_blank">payjs官网</a>注册的通信密钥'));
        $form->addInput($tleqitao_key);
		$tleqitao_notify_url = new Typecho_Widget_Helper_Form_Element_Text('tleqitao_notify_url', array("value"), $plug_url.'/TleQiTao/notify_url.php', _t('payjs异步回调'), _t('payjs支付的异步回调地址'));
        $form->addInput($tleqitao_notify_url);
		$tleqitao_return_url = new Typecho_Widget_Helper_Form_Element_Text('tleqitao_return_url', array("value"), $plug_url.'/TleQiTao/return_url.php', _t('payjs同步回调'), _t('payjs支付的同步回调地址'));
        $form->addInput($tleqitao_return_url);
		
		$tleqitaoqq = new Typecho_Widget_Helper_Form_Element_Text('tleqitaoqq', array("value"), '2293338477', _t('QQ号'), _t('通过QQ号自动获取头像地址和联系QQ链接'));
        $form->addInput($tleqitaoqq);
		$tleqitaotalk = new Typecho_Widget_Helper_Form_Element_Text('tleqitaotalk', array("value"), '', _t('想说的话'), _t('如果填写用户可看到你乞讨的缘由'));
        $form->addInput($tleqitaotalk);
		$tleqitaoisaudio = new Typecho_Widget_Helper_Form_Element_Radio('tleqitaoisaudio', array(
            'y'=>_t('启用'),
            'n'=>_t('禁用')
        ), 'y', _t('是否开启背景乞讨歌'), _t("启用后乞讨页面会出现乞讨歌"));
        $form->addInput($tleqitaoisaudio->addRule('enum', _t(''), array('y', 'n')));
		$tleqitaoaudiovolume = new Typecho_Widget_Helper_Form_Element_Text('tleqitaoaudiovolume', array("value"), '0.05', _t('乞讨歌音量大小'), _t('音量大小在0-1之间'));
        $form->addInput($tleqitaoaudiovolume);
		$tleqitaoaudiourl = new Typecho_Widget_Helper_Form_Element_Text('tleqitaoaudiourl', array("value"), 'http://sf.sycdn.kuwo.cn/2560baadac5c000fc060b9ec0eab18f5/5e97233d/resource/n1/68/69/11468396.mp3', _t('乞讨歌Url'), _t('输入乞讨歌的Url地址'));
        $form->addInput($tleqitaoaudiourl);
		
		$tleqitao_ad_return = new Typecho_Widget_Helper_Form_Element_Textarea('tleqitao_ad_return', array("value"), '广告位', _t('手机端同步回调页广告位'), _t('手机端同步回调页广告位广告代码'));
        $form->addInput($tleqitao_ad_return);
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