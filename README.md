##基于composer 下 phpspider 爬取新华网 人民网部分栏目

1.此代码 适用于 每天多次爬虫 爬取当天新闻 故不做分页

2.详细文档 见 https://doc.phpspider.org/

3.如果 满足不了业务 建议自己修改依赖 主要修改 来自选择器 log日志相关 可以根据业务调整

4. selector.php 140 行选择器修改成按业务需求获取带标签选择

5. requests.php 933 997 html encoding 方法 修改为更优的 mb_convert_encoding 函数做处理 兼容大部分执行环境