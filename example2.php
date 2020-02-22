// <?php

// It may take a whils to crawl a site ...
set_time_limit(10000);

// Inculde the phpcrawl-mainclass
include("libs/PHPCrawler.class.php");

// Extend the class and override the handleDocumentInfo()-method 
class MyCrawler extends PHPCrawler 
{
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
    
    // echo $lb;

    // $pattern = preg_match(pattern, subject)

    // 这两个相等
    // preg_match("/http:\/\/top\.hengyan\.com\/\w+\//", $DocInfo->url);
    // preg_match("/http:\/\/top\.hengyan\.com\/\w+\/\w+\.aspx[?]cid=\d+/", $DocInfo->url);
        
    $br = "\n";

    // 没有分页
    $pat_1 = preg_match("/http:\/\/top\.hengyan\.com\/\w+\/$/", $DocInfo->url);

    // 有分类
    $pat_2 = preg_match("/http:\/\/top\.hengyan\.com\/\w+\/\w+\.aspx[?][ct]id=\d+$/", $DocInfo->url);

    // 有分页
    $pat_3 = preg_match("/http:\/\/top\.hengyan\.com\/\w+\/\w+\.aspx[?]p=\d+$/", $DocInfo->url);

    // if($pat_1 >= 0 || $pat_1 >= 0 || $pat_3 >= 0){

        // 网页内容 
        $content = $DocInfo->content;

        // 分类
        $top_name = "";

        $title = "";

        

        // 左侧菜单
        $matchs = array();

        // $pat = "/<span>名称：<\/span>([^(<br)]+)<br/";
        $result = preg_match_all("/<a href=\"\/\w+\/\">([^(<\/a>)]+)<\/a>/", $content,$matchs);

        // print_r($matchs);


        // 左侧菜单
        $menus = $matchs[1];


        // 当前页标题
        $h2 = "";

        // 两种格式
        // <div id="location">
        //     <h2>好看的言情小说排行榜</h2>
        // </div>
        // <div id="location"><h2><span><a href="default.aspx?tid=1">日</a><a href="/xuanhuan/" class="focus">周</a><a href="default.aspx?tid=3">月</a><a href="default.aspx?tid=4">总</a></span>玄幻小说排行榜</h2></div>

        // $result = preg_match_all("/<div id=\"location\"><h2>([^(<\/h2><\/div>)]+)<\/h2><\/div>/", $content,$matchs);

        // if(!$result){

        //     $result = preg_match_all("/<\/a><\/span>([^(<\/h2><\/div>)]+)<\/h2><\/div>/", $content,$matchs);

        // }
    
        // // $result = preg_match_all("", $content, $matches);


        // print_r($type_1);
        // print_r($matchs);

    //     <div class="list">
    // <ul class="title"><li class="num">排行</li><li class="bookname">书名</li><li class="author">作者</li><li class="length">字数</li><li class="click">点击</li><li class="update">更新时间</li></ul>
    // <ul><li class="num">1</li><li class="bookname"><a class="bn vip" target="_blank" href="http://www.hengyan.com/book/11540.aspx">神级强者在都市</a> <a href="http://www.hengyan.com/article/1110291.aspx" target="_blank">第5623章 关键时刻</a></li><li class="author">剑锋</li><li class="length">17129163</li><li class="click">80711203</li><li class="update">2020-02-07 13:12:12</li></ul><ul class="item2"><li class="num">2</li><li class="bookname"><a class="bn vip" target="_blank" href="http://www.hengyan.com/book/4042.aspx">八部传奇</a> <a href="http://www.hengyan.com/article/1110280.aspx" target="_blank">第一百三十三章 玄冥真神</a></li><li class="author">小虫世界</li><li class="length">548353</li><li class="click">151105</li><li class="update">2020-02-05 15:56:14</li></ul>
    // </div>

    // <p class="pager"></p>
    // 获取列表
        // /<div[^>]*id="PostContent"[^>]*>(.*?) </div>/si
    // $result = preg_match_all("/<ul[^>]*class=\"title\"[^>]*>(.*?)<\/ul>/si", $DocInfo->content, $matchs);

    // print_r($result);
    // print_r($matchs);


    // 提取<div class="list"></div>
    $result = preg_match_all("/<div[^>]*class=\"list\"[^>]*>(.*?)<\/div>/si", $DocInfo->content, $matchs);

    // print_r($result);
    // print_r($matchs);



    $content = $matchs[1];

    // print_r($content);

    // var_dump($content[0]);

    $content = $content[0];


    $matchs = array();
    // 提取<div class="list"></div>
    $result = preg_match_all("/<ul[^>]*>(.*?)<\/ul>/si", $content,$matchs);

    // print_r($matchs);


    $list_1 = $matchs[1];


    unset($list_1[0]);

    // print_r($list);
    $data = array();

    foreach ($list_1 as $key => $value) {
       
       // print_r($value);

       // 解释li
       $list_2 = preg_match_all("/<li[^>]*>(.*?)<\/li>/si", $value,$matchs);

       $message = $matchs[1];

       // Array
       //  (
       //      [0] => 50
       //      [1] => <a class="bn vip" target="_blank" href="http://www.hengyan.com/book/9528.aspx">武魂至尊</a> <a href="http://www.hengyan.com/article/1006320.aspx" target="_blank">第一百七十章 两个失忆的拖油瓶！（一）</a>
       //      [2] => 苏德
       //      [3] => 424065
       //      [4] => 232671
       //      [5] => 2015-04-10 21:31:11
       //  )


       // print_r($message);

       $desc = array(
            'name'=>'',
            'author'=>$message[2],
            'word'=>$message[3],
            'click'=>$message[4],
            // 'createtime'=>$message[5]
            'updatetime'=>strtotime($message[5])
            );

       $content = $message[1];

       $list_3 = preg_match_all("/<a[^>]*>(.*?)<\/a>/si", $content,$matchs);

       // print_r($content);
       // print_r($matchs);

       $matchs = $matchs[1];
       $desc['name'] = $matchs[0];

       // print_r($desc);
       // echo $br.$br;

       $data[] = $desc;

    }

    print_r($data);

    // }

    echo '====================================================================================='.$br;
    flush();
  } 
}

// Now, create a instance of your class, define the behaviour
// of the crawler (see class-reference for more options and details)
// and start the crawling-process.

$crawler = new MyCrawler();

// URL to crawl
$start_url = "http://top.hengyan.com/haokan/";
$crawler->setURL($start_url);

// Only receive content of files with content-type "text/html"
// $crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png)$# i");

// 设置url
// 分类
$crawler->addURLFollowRule("#http://top\.hengyan\.com/\w+/# i");
// 分类id 与 上相同
$crawler->addURLFollowRule("#http://top\.hengyan\.com/\w+/\w+\.aspx[?]tid=\d+# i");
$crawler->addURLFollowRule("#http://top\.hengyan\.com/\w+/\w+\.aspx[?]cid=\d+# i");
// 分页
$crawler->addURLFollowRule("#http://top\.hengyan\.com/\w/\w+\.aspx[?]p=\d+# i");

// Store and send cookie-data like a browser does
$crawler->enableCookieHandling(true);
// Set the traffic-limit to 1 MB(1000 * 1024) (in bytes,
// for testing we dont want to "suck" the whole site)
//爬取大小无限制
$crawler->setTrafficLimit(0);
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