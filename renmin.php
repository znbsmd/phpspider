<?php
require './vendor/autoload.php';
use phpspider\core\phpspider;
use phpspider\core\requests;    //请求类
use phpspider\core\selector;    //选择器类
use phpspider\core\db;    //选择器类

use \app\library\Curl;
/* Do NOT delete this comment */
/* 不要删除这段注释 */

$renMinTime = date('Y/md',time());

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
        'opinion.people.com.cn', // 人民网观点
        'society.people.com.cn', // 人民社会

    ),
    //定义爬虫的入口链接, 爬虫从这些链接开始爬取,同时这些链接也是监控爬虫所要监控的链接
    'scan_urls' => array(
        // 人民观点
        "http://opinion.people.com.cn/index.html",
        // 人民社会
        "http://society.people.com.cn/GB/136657/index.html",

    ),

    //定义内容页url的规则
    'content_url_regexes' => array(
        // 人民观点
        "http://opinion.people.com.cn/n1/$renMinTime/c1003-\d+\.html",
        // 人民社会
        "http://society.people.com.cn/n1/$renMinTime/c1008-\d+\.html"
    ),
    //爬虫爬取每个网页失败后尝试次数
    'max_try' => 5,
    //爬虫爬取数据导出

    'fields' => array(
        array(
            // 人民内容
            'name' => "title",
            'selector' => "//div[contains(@class,'text_title')]/h1/text()",
            'required' => true,
        ),
        array(
            // 人民内容
            'name' => "content",
            'selector' => "//div[contains(@id,'rwb_zw')]",
            'required' => true,
        ),

    ),

);

$spider = new phpspider($configs);


$spider->on_extract_page  = function($page, $data) use ($configs,$renMinTime)
{

    // 整理格式
    $data['title'] =  preg_replace('/&#13;/',"",$data['title']);
    // 筛选二级域名
//    preg_match("#http://(.*?)\.#i",$page['request']['url'],$match);


    //抓内容页 图片上传文件服务器 （人民网暂未找到图片文章）
    if($contentArray = selector::select($data['content'],"//p ")){

        $finalContent = '';
        foreach ($contentArray as $c) {

            $finalContent  .= $c."\n";
        }
    }


    // 整理格式
//    unset($data['content']);
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