<?php 

	// 没有分页
	$url_1 = "http://top.hengyan.com/dianji/";


    if( preg_match("/http:\/\/top\.hengyan\.com\/\w+\/$/", $url_1) > 0 ){

    	var_dump(preg_match("/http:\/\/top\.hengyan\.com\/\w+\/$/", $url_1));
    } 


	$url_2 = "http://top.hengyan.com/dianji/default.aspx?p=2";

    if( preg_match("/http:\/\/top\.hengyan\.com\/\w+\/\w+\.aspx[?]p=\d+$/", $url_2) > 0 ){

    	var_dump(preg_match("/http:\/\/top\.hengyan\.com\/\w+\/\w+\.aspx[?]p=\d+$/", $url_2));
    }


   
?>