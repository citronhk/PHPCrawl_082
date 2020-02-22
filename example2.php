<?php

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

    // 这两人相等
    // preg_match("/http:\/\/top\.hengyan\.com\/\w+\//", $DocInfo->url);
    // preg_match("/http:\/\/top\.hengyan\.com\/\w+\/\w+\.aspx[?]cid=\d+/", $DocInfo->url);
        
    // 没有分页
    $pat_1 = preg_match("/http:\/\/top\.hengyan\.com\/\w+\/$/", $DocInfo->url);
    $pat_2 = preg_match("/http:\/\/top\.hengyan\.com\/\w+\/\w+\.aspx[?]p=\d+$/", $DocInfo->url);


    if($pat_1 > 0 && $pat_2 < 0){

        echo '没有分页';
    }

    // 有分页

    if($pat_1 > 0 && $pat_2 > 0){

        echo '有分页';
    }

    // echo $DocInfo->content;
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

// 设置链接
$crawler->addURLFollowRule("#http://top\.hengyan\.com/\w+/# i");
$crawler->addURLFollowRule("#http://top\.hengyan\.com/\w+/\w+\.aspx?tid=\d+# i");
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