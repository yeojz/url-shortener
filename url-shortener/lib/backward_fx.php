<?php
//-----Show URL
	function fts_show_shorturl($post, $output=true){
		global $FTS_URL_Shortener;
		$post_id = $post->ID;
		$shorturl = $FTS_URL_Shortener->fts_get_shortlink_display($post_id);
		if ($output){
			echo $shorturl;
		}else{
			return $shorturl;
		}
	}
//-----On-demand URL Shortener
	function fts_shorturl($url, $service, $output = true, $key='', $user=''){
		global $FTS_URL_Shortener;
		$shorturl = $FTS_URL_Shortener->od_get_shortlink($url, $service, $key, $user);
		if ($output){
			echo $shorturl;
		}else{
			return $shorturl;
		}
	}
?>