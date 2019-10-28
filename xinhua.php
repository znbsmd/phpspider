<?php

/**
 * 此代码 适用于 每天多次爬虫 爬取当天新闻 故不做分页
 *
 * 详细文档 见 https://doc.phpspider.org/
 *
 * 如果 满足不了业务 建议自己修改依赖 主要修改 来自选择器 log日志相关 可以根据业务调整
 */

require './vendor/autoload.php';
use phpspider\core\phpspider;
use phpspider\core\requests;    //请求类
use phpspider\core\selector;    //选择器类
use phpspider\core\db;    //选择器类

/* Do NOT delete this comment */
/* 不要删除这段注释 */

//测试网址选择代码
//$url      = "http://society.people.com.cn/n1/2019/1025/c1008-31419275.html";
//$html = requests::get($url);
//
//// 选择器规则
//$selector = "//div[contains(@id,'rwb_zw')]";
//$result   = selector::select($html, $selector);
//var_dump($result);
//// 提取结果
//exit;


$xinHuaTime = date('Y-m/d',time());

//$xinHuaTime = '2019-10/18';
$configs = array(
    'name' => '爬取新闻',
    'tasknum' => 1,
    'log_show' => true,
    'log_type' => 'error',
    'db_config' => array(
        'host'  => '10.20.1.146',
        'port'  => 4450,
        'user'  => 'djyv4_rw',
        'pass'  => 'tfkj_secret',
        'name'  => 'djyv4_cms',
    ),
    'export' => array(
        'type' => 'db',
        'table' => 'test',  // 如果数据表没有数据新增请检查表结构和字段名是否匹配
    ),

    //定义爬虫爬取哪些域名下的网页, 非域名下的url会被忽略以提高爬取速度
    'domains' => array(
        'www.xinhuanet.com',    //新华网
        'qc.wa.news.cn',    //新华网接口

    ),
    //定义爬虫的入口链接, 爬虫从这些链接开始爬取,同时这些链接也是监控爬虫所要监控的链接
    'scan_urls' => array(
        // 新华时政
        "http://qc.wa.news.cn/nodeart/list?nid=113351&pgnum=1&cnt=35&tp=1&orderby=0?callback=jQuery17109652718611317335_1571626648599&_=1571626648745",
//        // 新华国际
        "http://www.xinhuanet.com/world/qqbb.htm",
//        // 新华财经
        "http://qc.wa.news.cn/nodeart/list?nid=115033&pgnum=1&cnt=10&tp=1&orderby=1?callback=jQuery17108952321026778156_1571639539397&_=1571639539498",
//        // 新华社会
        "http://qc.wa.news.cn/nodeart/list?nid=113321&pgnum=1&cnt=10&tp=1&orderby=1?callback=jQuery112409205436671573288_1571642305357&_=1571642305358",
        // 新华法制
        "http://qc.wa.news.cn/nodeart/list?nid=113205&pgnum=1&cnt=35&tp=1&orderby=1?callback=jQuery17106217753547327338_1571643786449&_=1571643786581",

    ),
    // 自定义添加 前缀
    'prefix_host' => 'http://www.xinhuanet.com',

    //定义内容页url的规则
    'content_url_regexes' => array(
        // 新华时政
        "http://www.xinhuanet.com/politics/$xinHuaTime/c_\d+\.htm",
//        // 新华国际
        "http://www.xinhuanet.com/world/$xinHuaTime/c_\d+\.htm",
//        // 新华财经
        "http://www.xinhuanet.com/fortune/$xinHuaTime/c_\d+\.htm",
//        // 新华社会
        "http://www.xinhuanet.com/local/$xinHuaTime/c_\d+\.htm",
        // 新华法制
        "http://www.xinhuanet.com/legal/$xinHuaTime/c_\d+\.htm",

    ),
    //爬虫爬取每个网页失败后尝试次数
    'max_try' => 5,
    //爬虫爬取数据导出

    'fields' => array(
        array(
            // 新华
            'name' => "title",
            'selector' => "//div[@class='h-title']/text()",
            'required' => true
        ),
        array(
            // 新华
            'name' => "content",
            'selector' => "//div[contains(@id,'p-detail')]",
            'required' => true,
        ),


    ),

);

$spider = new phpspider($configs);


$spider->on_extract_page  = function($page, $data) use ($configs,$xinHuaTime)
{

    // 整理格式
    $data['title'] =  preg_replace('/&#13;/',"",$data['title']);

    //抓内容页 图片上传文件服务器 （人民网暂未找到图片文章）
    if($contentArray = selector::select($data['content'],"//div/img | //p ")){

        $finalContent = '';
        foreach ($contentArray as $c) {

            $finalContent  .= $c."\n";
        }
    }

    // 整理格式
    unset($data['content']);
    $data['content'] = preg_replace('/\n|\s|\r|&#13;/',"",strip_tags($finalContent));
    $data['content_html'] = preg_replace('/&#13;/',"",$finalContent);

    var_dump($data);
    //标题重复就不入库
    $sql  = "select count(*) as `count` from `test` where `title` ='".$data['title']."'";
    $row = db::get_one($sql);
    if($row['count'] != 0){
        return false;
    }
    // 如果需要自定义sql 在这里写 然后返回false   例： return false
    //事务
//    db::begin_tran();

//    if(!($content['article_id'] = db::insert('article', $data))) {
////        db::rollback();
//        return false;
//    }

//    if(!db::insert('article_content', $content)) {
//        db::rollback();
//        return false;
//    }
//    db::commit();

//    return false;

    // 如果 使用依赖里的 入库操作 最后返回 数据 即可
    return $data;
};


$spider->start();