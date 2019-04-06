<img src="https://ws3.sinaimg.cn/large/ecabade5ly1fv7pzn9u2pj211h0mpadq.jpg">
### TleQiTaoForTypecho简易乞讨插件
---
跳转同款（打赏）插件https://github.com/muzishanshi/TleDaShangForTypecho

TleQiTao是一个简易的乞讨Typecho插件

程序有可能会遇到bug不改版本号直接修改代码的时候，所以扫描以下二维码关注公众号“同乐儿”，可直接与作者二呆产生联系，不再为bug烦恼，随时随地解决问题。

#### 使用方法：
第一步：下载本插件，放在 `usr/plugins/` 目录中（插件文件夹名必须为TleQiTao）；

第二步：激活插件；

第三步：填写配置；

第四步：完成。

#### 注意问题：
如果出现没有回调的情况，用0.01多测试几次即可，payjs说有可能是DNS解析的原因，不明则厉，当时我也遇到过，不过觉得哪的也没改就又好了，看到payjs文档里写notify_url的长度为string(32)，但我的都是大于32的，都可以用，如果还有问题，可以进行向作者反馈，统计一下。

#### 版本推荐：
此插件使用php5.6+Typecho正式版开发。

#### 与我联系：
作者：二呆

1元入群：http://joke.tongleer.com/333.html

网站：http://www.tongleer.com/

Github：https://github.com/muzishanshi/TleQiTaoForTypecho

#### 更新记录：
2019-04-06

	V1.0.4 集成payjs微信支付，可通过自己的微信直接到账银行卡，并简化插件。
	
2019-03-22

	V1.0.3 修复了因cdn.bootcss.com中JS静态资源不可访问导致的js失效的问题。
	
2018-09-21

	新增有赞支付

2018-09-13
	
	第一版本实现