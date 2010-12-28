<?php
/*
 * URL Shortner Services Class
 * @link: http://www.fusedthought.com/downloads#class-ftshorten
 * @author Gerald Yeo <contact@fusedthought.com>
 * @version 2.3.1
 * @package: class.FTShorten
 * Requires: inc.FTShared 
 * Also included in  URL Shortner Plugin for WordPress
 
 * Copyright Information and Usage
 * This class is packaged in the URL Shortener plugin and is also available as a separate download from http://www.fusedthought.com/downloads#class-ftshorten.
 */
define('CLASS_FTSHORTEN_VERSION', '2.3.1'); 
if (!defined('FTS_DEV_ENV')) {
    define('FTS_DEV_ENV', false);
}
require_once(dirname(__FILE__).'/components/inc.FTShared.php');

if (!class_exists('FTShorten')){
	class FTShorten extends FTShared {
		public $service, $url, $name, $apikey, $generic;	
		public $pingfmapi;
	
		protected function get_service(){
			$service = $this->service; 
			$url = $this->url;
			$name =  $this->name; 
			$userkey = $this->apikey;
			$eurl = urlencode($url);	

			$apiarray = array(
				'tinyurl' => array(
					'api'=>'http://tinyurl.com/api-create.php?url=[url]'
					),
				'supr' => array(
					'api' => 'http://su.pr/api/shorten?longUrl=[url]',
					'auth' => 'http://su.pr/api/shorten?longUrl=[url]&login=[user]&apiKey=[key]',
					'type' => 'json'
					),
				'isgd' => array(
					'api'=>'http://is.gd/api.php?longurl=[url]'
					),
				'bitly' => array(
					'api' => 'http://bit.ly/api?url=[url]',
					'auth' => 'http://api.bit.ly/v3/shorten?login=[user]&apiKey=[key]&longUrl=[url]&format=json',
					'type' => 'json'
					),
				'jmp' => array(
					'api' => 'http://j.mp/api?url=[url]',
					'auth' => 'http://api.j.mp/v3/shorten?format=json&longUrl=[url]&login=[user]&apiKey=[key]',
					'type' => 'json'
					),                    
				'cligs' => array(
					'api' => 'http://cli.gs/api/v1/cligs/create?url=[url]',
					'auth' => 'http://cli.gs/api/v1/cligs/create?url=[url]&key=[key]&appid=ftsplugin'
					),
				'shortie'=> array(
					'api' => 'http://short.ie/api?url=[url]',
					'auth' => 'http://short.ie/api?url=&format=plain&private=true&email=[user]&secretKey=[key]'
					),
				'chilpit' => array(
					'api'=>'http://chilp.it/api.php?url=[url]'
					),
				'pingfm' => array(
					'auth'=>'http://api.ping.fm/v1/url.create',
					'type'=>'xml',
					'body' => array(
							'api_key' => $this->pingfmapi,
							'user_app_key' => $userkey,
							'long_url' => $this->url
							)
					),
				'smsh' => array(
					'api'=>'http://smsh.me/?api=json&url=[url]',
					'type' => 'json'
					),
				'unfakeit' => array(
					'api'=>'http://unfake.it/?a=api&url=[url]'
					),
				'awesm' => array(
					'auth'=>'http://create.awe.sm/url.txt',
					'body' => 'version=1&target=[url]&share_type=other&create_type=api&api_key=[key]',
					'type' => 'txt-post'
					),	
				'snipurl' => array(
					'auth'=>'http://snipurl.com/site/getsnip',
					'body' => 'sniplink=[url]&snipuser=[user]&snipapi=[key]&snipformat=simple',
					'type' => 'txt-post'
					),	 
				'snurl' => array(
					'auth'=>'http://snurl.com/site/getsnip',
					'body' => 'sniplink=[url]&snipuser=[user]&snipapi=[key]&snipformat=simple',
					'type' => 'txt-post'
					),
				'snipr' => array(
					'auth'=>'http://snipr.com/site/getsnip',
					'body' => 'sniplink=[url]&snipuser=[user]&snipapi=[key]&snipformat=simple',
					'type' => 'txt-post'
					),
				'snim' => array(
					'auth'=>'http://sn.im/site/getsnip',
					'body' => 'sniplink=[url]&snipuser=[user]&snipapi=[key]&snipformat=simple',
					'type' => 'txt-post'
					),
				'cllk' => array(
					'auth'=>'http://cl.lk/site/getsnip',
					'body' => 'sniplink=[url]&snipuser=[user]&snipapi=[key]&snipformat=simple',
					'type' => 'txt-post'
					),                    
				'voizle' => array(
					'api'=>'http://api.voizle.com/?crawl=no&type=all&property=voizleurl&u=[url]'
					),
				'urlinl' => array(
					'api'=>'http://urli.nl/api.php?format=simple&action=shorturl&url=[url]'
					),
				'digg' => array(
					'auth'=>'http://services.digg.com/1.0/endpoint/?method=shorturl.create&type=json&url=[url]',
					'type' => 'json',
                    'useragent'=> '1'
					),
				'sosobz' => array(
					'api'=>'http://soso.bz/api.php?url=[url]'
					),	
				'tynie' => array(
					'api'=>'http://tynie.net/maketynie.php?api=[url]'
					),	
				'yourls' => array(
					'auth'=>$this->generic.'/yourls-api.php?action=shorturl&format=json&signature=[key]&url=[url]',
					'type'=>'json'
					),
				'interdose' =>  array(
					'api'=>'http://api.interdose.com/api/shorturl/v1/shorten?service='.$this->generic.'&url=[url]',
					'auth'=>'http://api.interdose.com/api/shorturl/v1/shorten?service='.$this->generic.'&url=[url]&user=[user]&pass=[key]',
					),
				'cuthut' => array(
					'api'=>'http://cuthut.com/api/?url=[nurl]',
					),			
			);
			$selected = $apiarray[$service];
			$useragent = $selected['useragent'];	
			if ( $name != ''|| $userkey != '' ){
				$api  = $selected['auth'];
				$api = str_replace('[user]',$name, $api);
				$api  = str_replace('[key]',$userkey, $api);
			} else {
				$api  = $selected['api'];
			}
			$api = str_replace('[url]',$eurl, $api);
			$api = str_replace('[nurl]',$url, $api);

			if ($useragent == '1'){
				$ua = 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5';
				ini_set('user_agent', "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5");
			} else {
				$ua = 'false';
			};		
			switch($selected['type']){
				case json:
					$result = $this->openurl($api, $ua);
					$processed = $this->processjson($result);
					if ($processed){
						$data = $this->outpro($service, $processed);
					} else {
						$data = $result;
					}
					break;
				case xml:
					$body = $selected['body'];
					$result = $this->processxml($api, 'POST', $body);
					if ($result) {
						$processed = new SimpleXMLElement($result);	
						$data = $this->outpro($service, $processed);
					}
					break;	
				case txt-post:
					$body = $selected['body'];
					$body = str_replace('[url]',$eurl, $body);
					$body = str_replace('[user]',$name, $body);
					$body = str_replace('[key]',$userkey, $body);
					$data = $this->openurl($api, $ua, 'POST', $body);
					break;	
				default :
					$data = $this->openurl($api, $ua);
					break;
			}
			if ($useragent == '1'){ini_restore('user_agent');};	
			return $data;
		}
		protected function outpro($service, $result) {
			$nurl = $this->url;
			switch ($service){
				case 'supr': $data = $result->results->$nurl->shortUrl; break;
				case 'pingfm': $data = $result->short_url; break;
				case 'bitly': $data = $result->data->url; break;
                case 'jmp': $data = $result->data->url; break;
				case 'smsh': $data = $result->body; break;	
                case 'digg': $data = $result->shorturls[0]->short_url; break;
				case 'yourls': break;
				default : break;
			}
			return $data;
		}
		public function shorturl(){	
            if (FTS_DEV_ENV){
                $data = 'http://'.$this->service .'----'. $this->url;       
			}else{
                $data = $this->get_service();	
            }
			return $data;
		}//end fx shorten
		
	} //end class
} //end check cleass
?>
