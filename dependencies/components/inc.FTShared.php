<?php
/*
 * URL Shortner Shared Functions
 * @link: http://www.fusedthought.com/downloads#class-ftshorten/
 * @author Gerald Yeo <contact@fusedthought.com>
 * @version 2.0
 * @package: class.FTShorten
 * Requires: class.json
 * Also included in  URL Shortner Plugin for WordPress
 */

if (!class_exists('Services_JSON')){
	require_once(dirname(__FILE__).'/class.json.php');
}

if (!class_exists('FTShared')){
	class FTShared {
		public function openurl($url, $useragent = 'false', $posttype = 'GET', $postfield = '') {
			if (function_exists('curl_init')) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, false);	
				if ($useragent != 'false'){
					curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
				}
				if ($posttype == 'POST'){
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $postfield);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);	
				}
				$result = curl_exec($ch);
				curl_close($ch);
				return $result;				
			} else {
				return file_get_contents($url);
			}
		}//end fx open url	
		
		public function processjson($url){
			$json = new Services_JSON();
			$result = $json->decode($url);
			return $result;
		}//end json process
		
		public function processxml($url, $method='POST', $body=array()){
			$request = new WP_Http;
			$result = $request->request( $url, array( 'method' => $method, 'body' => $body) ); 
			if($result['body']){return $result['body'];}		
		} //end xml process
	}//end class
}//end check
?>
