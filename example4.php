<?php

// It may take a whils to crawl a site ...
set_time_limit(10000);

// Inculde the phpcrawl-mainclass
include("libs/PHPCrawler.class.php");

// Extend the class and override the handleDocumentInfo()-method 
class MyCrawler extends PHPCrawler 
{
  // 处理涵数
  function handleDocumentInfo($DocInfo) 
  {
    // Just detect linebreak for output ("\n" in CLI-mode, otherwise "<br>").
    if (PHP_SAPI == "cli") $lb = "\n";
    else $lb = "<br />";

    // Print the URL and the HTTP-status-Code
    echo "Page requested: ".$DocInfo->url." (".$DocInfo->http_status_code.")".$lb;
    
    // Print the refering URL
    echo "Referer-page: ".$DocInfo->referer_url.$lb;
    
    // Print if the content of the document was be recieved or not
    if ($DocInfo->received == true)
      echo "Content received: ".$DocInfo->bytes_received." bytes".$lb;
    else
      echo "Content not received".$lb; 
    
    // Now you should do something with the content of the actual
    // received page or file ($DocInfo->source), we skip it in this example 
      
    // 正则接收返回值
    $matches = array();

    // 没有分页
    $pattern_1 = preg_match("/http:\/\/top\.hengyan\.com\/\w+\/$/", $DocInfo->url);

    // 有分类
    $pattern_2 = preg_match("/http:\/\/top\.hengyan\.com\/\w+\/\w+\.aspx[?][ct]id=\d+$/", $DocInfo->url);

    // 有分页
    $pattern_3 = preg_match("/http:\/\/top\.hengyan\.com\/\w+\/\w+\.aspx[?]p=\d+$/", $DocInfo->url);

    // 解释当前分类
    $result = preg_match("/http:\/\/top\.hengyan\.com\/([^(\/)]+)\//", $DocInfo->url, $matches);

    // print_r($result);
    // print_r($matches[1]);
    // echo $br;

    $current_type = $matches[1];

    // 网页
    $html = $DocInfo->content;
    // print_r($html);

    // 开启session
    @session_start();

    // 右侧菜单栏
    $full_menu = array();

    // print_r($_SESSION);
    // 菜单栏
    if(empty($_SESSION['full_menu'])){
    // if(true == false){

      // 左侧菜单栏
      // <div id="left">
      // <h1><a href="/">小说排行榜</a></h1>
      // <ul>
      // <li><a href="/haokan/">好看的小说排行榜</a></li>
      // <li><a href="/quanben/">完本小说排行榜</a></li>
      // <li><a href="/lz/">连载小说排行榜</a></li>
      // <li><a href="/xinshu/">新书风云榜</a></li>
      // <li><a href="/gengxin/">最强更新榜</a></li>
      // <li><a href="/mianfei/">免费小说排行榜</a></li>
      // <li><a href="/vip/">VIP小说排行榜</a></li>
      // <li class="menu">小说分类排行榜</li>
      // <li><a href="/xuanhuan/">玄幻</a></li>
      // <li><a href="/qihuan/">奇幻</a></li>
      // <li><a href="/wuxia/">武侠</a></li>
      // <li><a href="/xianxia/">仙侠</a></li>
      // <li><a href="/dushi/">都市</a></li>
      // <li><a href="/mm/">言情</a></li>
      // <li><a href="/lishi/">历史</a></li>
      // <li><a href="/junshi/">军事</a></li>
      // <li><a href="/youxi/">游戏</a></li>
      // <li><a href="/jingji/">竞技</a></li>
      // <li><a href="/kehuan/">科幻</a></li>
      // <li><a href="/kongbu/">恐怖</a></li>
      // <li class="menu">统计分类排行榜</li>
      // <li><a href="/dianji/">点击</a></li>
      // <li><a href="/dashang/">打赏</a></li>
      // <li><a href="/honghua/">火票</a></li>
      // <li><a href="/shoucang/">收藏</a></li>
      // <li><a href="/wangzuan/">金笔</a></li>
      // <li><a href="/xiazai/">下载</a></li>
      // </ul>
      // </div>

      // 截取菜单栏部分
      $result = preg_match_all("/<div[^>]*id=\"left\"[^>]*>(.*?)<\/div>/si", $html,$matches);


      // print_r($matches);

      // <li><a href="/haokan/">好看的小说排行榜</a></li>
      // 解释链接名
      $links = $matches[1][0];

      // print_r($links);
      // $result = preg_match_all("/<a href=\"\/([^(<\/\">)]+)\/\">/si", $links, $matches);

      // print_r($matches);

      // 解释菜单栏
      $result = preg_match_all("/<a href=\"\/\w+\/\">([^(<\/a>)]+)<\/a>/si", $links,$matches);

      // $menus = $matches[1];

      // print_r($matches);

      $menu_link = array();
      $menu_name = array();
      $full_link = array();

      foreach ($matches[0] as $key => $value) {
        # code...

        $result = preg_match_all("/<a href=\"\/([^(<\/\">)]+)\/\">/si", $value, $matches);

        // 键名
        $key = $matches[1][0];

        $result = preg_match_all("/<a href=\"\/\w+\/\">([^(<\/a>)]+)<\/a>/si", $value,$matches);

        // 值
        $name = $matches[1][0];


        if(!in_array($key, $menu_link)){

          $menu_link[] = $key;
          $menu_name[] = $name;
          if(!empty($key)){
            
            $full_menu[$key] = $name;
          }
        }

        
        // print_r($full_menu);
        // echo $lb;
      }

      $_SESSION['full_menu'] = $full_menu;
      // print_r($matches);

    }else{

      $full_menu = $_SESSION['full_menu'];
    }

    // print_r($full_menu);
    // print_r(array_keys($full_menu));



    // 列表

    //     <div class="list">
    // <ul class="title"><li class="num">排行</li><li class="bookname">书名</li><li class="author">作者</li><li class="length">字数</li><li class="click">点击</li><li class="update">更新时间</li></ul>
    // <ul><li class="num">1</li><li class="bookname"><a class="bn vip" target="_blank" href="http://www.hengyan.com/book/11540.aspx">神级强者在都市</a> <a href="http://www.hengyan.com/article/1110291.aspx" target="_blank">第5623章 关键时刻</a></li><li class="author">剑锋</li><li class="length">17129163</li><li class="click">80711203</li><li class="update">2020-02-07 13:12:12</li></ul><ul class="item2"><li class="num">2</li><li class="bookname"><a class="bn vip" target="_blank" href="http://www.hengyan.com/book/4042.aspx">八部传奇</a> <a href="http://www.hengyan.com/article/1110280.aspx" target="_blank">第一百三十三章 玄冥真神</a></li><li class="author">小虫世界</li><li class="length">548353</li><li class="click">151105</li><li class="update">2020-02-05 15:56:14</li></ul>
    // </div>

    // 提取<div class="list"></div>
    $result = preg_match_all("/<div[^>]*class=\"list\"[^>]*>(.*?)<\/div>/si", $html, $matches);

    // 提取列表
    $result = preg_match_all("/<ul[^>]*>(.*?)<\/ul>/si", $matches[1][0], $matches);

    $list = $matches[1];

    // 用户表头
    unset($list[0]);

    $data = array();

    // 解释小说列表
    foreach ($list as $key => $value) {

      // print_r($value);
      
      // 提取每条信息
      $result = preg_match_all("/<li[^>]*>(.*?)<\/li>/si", $value, $matches);

      // [1] => Array
      // (
      //   [0] => 49
      //   [1] => <a class="bn" target="_blank" href="http://mm.hengyan.com/book/4650.aspx">穿越之王妃很倾城</a> <a href="http://mm.hengyan.com/article/191748.aspx" target="_blank">第六十章·奇怪的少年（三）</a>
      //   [2] => 墨尔本未晴
      //   [3] => 104269
      //   [4] => 235290
      //   [5] => 2013-11-20 08:46:53
      // )

      $each = $matches[1];

      $data = array(
            "name"=>'',
            "author"=>$each[2],
            "word"=>$each[3],
            "click"=>$each[4],
            "updatetime"=>$each[5]
          );

      // 解释书名
      $result = preg_match_all("/<a[^>]*>(.*?)<\/a>/si", $each[1], $matches);

      $name = $matches[1][0];

      $data['name'] = $name;

      print_r($data);

      echo $lb,$lb;
    }

    // print_r($matches);

    echo $lb;
    
    flush();
  } 
}

// Now, create a instance of your class, define the behaviour
// of the crawler (see class-reference for more options and details)
// and start the crawling-process.

$crawler = new MyCrawler();

// 开始地址
// URL to crawl
$start_url = "http://top.hengyan.com/haokan/";
$crawler->setURL($start_url);

// 下载文档类型
// Only receive content of files with content-type "text/html"
$crawler->addContentTypeReceiveRule("#text/html#");

// 忽略的链接
// Ignore links to pictures, dont even request pictures
$crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png)$# i");

// 设置扩展url
// 分类
$crawler->addURLFollowRule("#http://top\.hengyan\.com/\w+/# i");
// 分类id 与 上相同
$crawler->addURLFollowRule("#http://top\.hengyan\.com/\w+/\w+\.aspx[?]tid=\d+# i");
$crawler->addURLFollowRule("#http://top\.hengyan\.com/\w+/\w+\.aspx[?]cid=\d+# i");
// 分页
$crawler->addURLFollowRule("#http://top\.hengyan\.com/\w/\w+\.aspx[?]p=\d+# i");

// Store and send cookie-data like a browser does
$crawler->enableCookieHandling(true);

// 最大下载量
// Set the traffic-limit to 1 MB (in bytes,
// for testing we dont want to "suck" the whole site)
// $crawler->setTrafficLimit(1000 * 1024);
$crawler->setTrafficLimit(0);// 不设置

// 执行
// Thats enough, now here we go
$crawler->go();

// At the end, after the process is finished, we print a short
// report (see method getProcessReport() for more information)
$report = $crawler->getProcessReport();

// if (PHP_SAPI == "cli") $lb = "\n";
// else $lb = "<br />";
    
// echo "Summary:".$lb;
// echo "Links followed: ".$report->links_followed.$lb;
// echo "Documents received: ".$report->files_received.$lb;
// echo "Bytes received: ".$report->bytes_received." bytes".$lb;
// echo "Process runtime: ".$report->process_runtime." sec".$lb; 
?>