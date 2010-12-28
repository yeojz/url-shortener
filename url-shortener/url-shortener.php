<?php
/*
Plugin Name: URL Shortener
Plugin URI: http://www.fusedthought.com/downloads#url-shortener-wordpress-plugin
Description: This plugin provides integration of URL Shorteners  (e.g. Bit.ly, Su.pr, Ping.fm, Digg and many others). <strong>Please refer to <a href="http://wiki.fusedthought.com/docs/url-shortener-wordpress-plugin/upgrade-notes/">Upgrade Notes</a> if you're upgrading to 3.0 from previous versions.</strong>
Author: Gerald Yeo
Author URI: http://www.fusedthought.com
Version: 3.1.1
*/

define('FTS_URL_SHORTENER_VERSION', '3.1.1');
define('FTS_URL_SHORTENER_STATUS', 'Stable'); //Alpha->Beta->Stable
define('FTS_DEV_ENV', false);
require_once( dirname(__FILE__) . '/dependencies/class.FTShorten.php');

//main class
if ( !class_exists('FTS_URL_Shortener') ) :
	class FTS_URL_Shortener {
	
		private $db_option = 'fts_urlfx';
		private $plugin_url;
		private $plugin_page;
        
        private $supported = array(
            'tinyurl'=> 'TinyURL', 
            'supr'=> 'Su.pr (by StumbleUpon)', 
            'isgd'=> 'is.gd', 
            'bitly'=> 'bit.ly',
            'jmp' => 'j.mp',        
            'cligs'=> 'Cli.gs', 
            'shortie'=> 'Short.ie', 
            'chilpit'=> 'Chilp.it', 
            'pingfm'=> 'Ping.fm', 
            'smsh'=> 'sm00sh / smsh.me', 
            'unfakeit'=> 'unfake.it', 
            'awesm'=> 'awe.sm', 
            'snipurl'=> 'Snipurl', 
            'snurl'=> 'Snurl', 
            'snipr'=> 'Snipr',
            'snim'=> 'Sn.im',
            'cllk'=> 'Cl.lk',            
            'voizle'=> 'Voizle', 
            'urlinl' => 'urli.nl',
			'sosobz' => 'soso.bz',
			'tynie' => 'tynie.net',
			'interdose' => 'Interdose API',
			'cuthut' => 'cuthut.com',
            );
        private $authuser = array('supr', 'bitly', 'jmp', 'snipurl', 'snurl', 'snipr', 'snim', 'cllk', 'interdose');
        private $authkey = array('supr', 'bitly', 'jmp', 'snipurl', 'snurl', 'snipr', 'snim', 'cllk', 'awesm', 'pingfm', 'cligs', 'interdose');
        private $requser = array('snipurl', 'snurl', 'snipr', 'snim', 'cllk');
        private $reqkey = array('snipurl', 'snurl', 'snipr', 'snim', 'cllk', 'awesm', 'pingfm');
		private $generic = array('interdose');
        
		private function is_active($plugin) {return in_array($plugin, apply_filters('active_plugins', get_option('active_plugins') ) );}
		
//--------__construct()
		public function activate_shortener() {	
            $this->plugin_url = defined('WP_PLUGIN_URL') ? WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)) : trailingslashit(get_bloginfo('wpurl')) . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)); 		
			
            $options = $this->my_options();
            //Publishing
            if ($options['urlserviceenable'] == 'yes'){
                //FX registration mirrors WP stats plugin
                if (!function_exists('wp_get_shortlink')){    
                    //Register these only for WP < 3.0.
                    add_action('wp_head', array(&$this, 'fts_shortlink_wp_head'));
                    add_action('wp', array(&$this, 'fts_shortlink_header'));                    
                    //edit post-page button
                    if ($this->check_wp_version(2.9)){
                        add_filter('get_sample_permalink_html', array(&$this, 'fts_get_shortlink_html'), 10, 2 );
                    } else{}   
                    //compatibility - remove wp.me
                    remove_action('wp_head', 'wpme_shortlink_wp_head');
                    remove_action('wp', 'wpme_shortlink_header');
                    remove_filter('get_sample_permalink_html', 'wpme_get_shortlink_html', 10, 2 );       
                }else{
                    //Register a shortlink handler for WP >= 3.0.
                    add_filter('get_shortlink', array(&$this, 'pub_gateway'), 10, 4);
                }  
                //Future to Present
                add_action('future_to_publish', array(&$this, 'transition_get_shortlink'), 10, 1);           
            }
            
            //Options
            add_action('admin_menu', array(&$this, 'plugin_menu'));
            
            //Tables
            add_filter('post_row_actions',  array(&$this, 'table_hover_link'), 10, 2);
            add_filter('page_row_actions',  array(&$this, 'table_hover_link'), 10, 2);
			add_action('admin_head', array(&$this, 'fts_urlshortener_adminhead'));			
			add_action('load-edit.php', array(&$this, 'fts_urlshortener_edit_head'));
			add_action('load-edit-pages.php', array(&$this, 'fts_urlshortener_edit_head')); 

            //AJAX Calls
            add_action('wp_ajax_urlshortener_act', array(&$this, 'fts_urlshortener_ajaxcallback'));
			
            //Nice IDs
            if ($options['niceid']=='yes'){
                add_filter('template_redirect', array(&$this, 'template_redirect'), 10, 2);     
            }

			//Shortcode
			if ($options['url_shortcode']!='no'){
				add_shortcode('shortlink',  array(&$this, 'shortcode_support'));
			}     
            //Append Text
            add_filter('the_content', array(&$this, 'fts_filter_append_post'));  
  
        }
        
//-------- for use in activation	
		private function check_wp_version($version, $operator = ">="){
			global $wp_version;
			return version_compare($wp_version, $version, $operator);
		}      
        public function plugin_menu(){ 
            if( !is_admin() )
				return;
            $this->plugin_page = add_options_page(__('URL Shortener','url-shortener'), __('URL Shortener','url-shortener'), 'administrator', 'shorturl', array(&$this, 'options_page'));
            add_contextual_help($this->plugin_page, self::options_page_help());
            add_action('load-'.$this->plugin_page,  array(&$this, 'options_style_scripts'));
        }        
//--------publishing logic     
        //FTShorten adapter
        private function class_adapter($url, $service, $key='', $user=''){
           include( dirname(__FILE__) . '/lib/class_adapter.php');
           return $result;
        }
        //automatic shortener
        private function pub_get_shortlink($id=0, $context='post', $allow_slugs=true, $transition=false){
            include( dirname(__FILE__) . '/lib/pub_get_shortlink.php');  
            return $shortlink;
        }
        //ondemand shortener 
        public function od_get_shortlink($url='', $service='', $key='', $user=''){
             include( dirname(__FILE__) . '/lib/od_get_shortlink.php');
             return $shortlink;
        }     
        //future to publish status hook
        public function transition_get_shortlink($post){
            $shortlink = $this->pub_get_shortlink($post->ID, 'post', true, true);      
        }
//--------Function Calls, publishing page
        //WP >= 3.0
        public function pub_gateway($shortlink, $id, $context, $allow_slugs){
            $shortlink = $this->pub_get_shortlink($id, $context, $allow_slugs);
            return $shortlink;
        }
        //WP < 3.0 
        public function fts_shortlink_wp_head() {
            global $wp_query;
            $shortlink = $this->pub_get_shortlink(0, 'query');
			if ($shortlink){
				echo '<link rel="shortlink" href="' . $shortlink . '" />';
			}
        }
        public function fts_shortlink_header() {
            global $wp_query;
            if ( headers_sent() )
                return;
            $shortlink = $this->pub_get_shortlink(0, 'query');
            header('Link: <' . $shortlink . '>; rel=shortlink');
        }
        public function fts_get_shortlink_html($html, $post_id) {
            $shortlink = $this->pub_get_shortlink($post_id);
			if ($shortlink){$html .= '<input id="shortlink" type="hidden" value="' . $shortlink . '" /><a href="' . $shortlink . '" class="button" onclick="prompt(&#39;URL:&#39;, jQuery(\'#shortlink\').val()); return false;">' . __('Get Shortlink') . '</a>';}
			return $html;
        } 	
		//Template gateway
		public function fts_get_shortlink_display($post_id){
			$shortlink = $this->pub_get_shortlink($post_id);
			return $shortlink;
		}
        
        //The content filter
        public function fts_filter_append_post($content){   
            global $post;
            $options = $this->my_options();
            
            if(is_home() && $options['appendurl']['home']=='yes'
              || is_single() && $options['appendurl']['single']=='yes'
              || is_page() && $options['appendurl']['page']=='yes'){
                 
                $shortlink = $this->pub_get_shortlink($post->ID);
                $content .= '<div class="post-shortlink"><strong>'.$options['appendurl']['text'].'</strong> <a href="'.$shortlink.'">'.$shortlink.'</a></div>';
            }
            return $content;    
        }
//--------table page
        public function table_hover_link($actions, $post) {
            $shortlink = $this->pub_get_shortlink($post->ID);
			if ($shortlink){$actions['shortlink'] = '<a href="' . $shortlink . '" onclick="prompt(&#39;URL:&#39;, jQuery(this).attr(\'href\')); return false;">' . __('Get Shortlink') . '</a>';}
			return $actions;
        } 
		public function fts_urlshortener_adminhead(){?>
            <script type="text/javascript" >
                var aaurl = '<?php echo admin_url('admin-ajax.php'); ?>';
                var nonce = '<?php echo wp_create_nonce('urlshortener_ajax');?>';
            </script>
            <?php
		}
        public function fts_urlshortener_edit_head(){
			wp_enqueue_script('fts_surl_ajax', $this->plugin_url.'/lib/scripts/jquery.ajaxq.js',array('jquery'),1.0);
			wp_enqueue_script('fts_surl_edit', $this->plugin_url.'/lib/scripts/tablecol.js',array('jquery'),1.0);
        }        
//--------AJAX Callbacks
        public function fts_urlshortener_ajaxcallback(){
			check_ajax_referer('urlshortener_ajax');
			$post_id = $_POST['pid'];
			delete_post_meta($post_id, 'shorturl');
        }
//--------options page
        public function options_page(){
            if( !is_admin() )
				return;
            $action_url = $_SERVER['REQUEST_URI'];    
            include( dirname(__FILE__) . '/lib/options_page.php');
        }    
        public function options_page_help(){
            include( dirname(__FILE__) . '/lib/options_page_help.php');
            return $help_text;
        }
        public function options_style_scripts(){	
            wp_enqueue_style('url_shortener_options_css', $this->plugin_url . '/lib/styles/options_page.css');
        }     
//--------Nice ID Redirect Options
        public function template_redirect($requested_url=null, $do_redirect=true){
            $options = $this->my_options();
            global $wp;
            global $wp_query; 
            if (is_404()){
                $post_id='';
                $request = $wp->request;              
                if (preg_match('/(\\d+)/', $request, $matches)){
                    $post_id = $matches[0];
                }   
                if(!empty($post_id) && is_numeric($post_id)){
                    $redir_to = get_permalink($post_id);
                    if($redir_to){status_header(200); wp_redirect($redir_to, 301); exit();}
                }
            }
        }
//--------Shortcode Support
		public function shortcode_support($atts, $content = null){
			$options = $this->my_options();
			extract(shortcode_atts(array(
					'name' => '',
					'url' => '',
					'service' => $options['urlservice'],
					'key' => '',
					'user'=>'',
			), $atts));
			global $post;
			$post_id = $post->ID;
			
			if($content){$url = $content;}
			
			$url ? $shortlink = $this->od_get_shortlink($url, $service, $key, $user) : $shortlink = $this->pub_get_shortlink($post_id);

			$output = '<a href="'.$shortlink.'">';
			$name ? $output .= $name : $output .= $shortlink;
			$output .= '</a>';
			return $output;

		}
//--------Handle our options
        private function my_options(){
            return get_option($this->db_option);
        }
        private function save_options($options){
            update_option($this->db_option, $options);
        }
        public function del_options(){
			delete_option($this->db_option);
		}
		private function manage_options(){
			$options = array(
                'about_plugin' => array('version' => FTS_URL_SHORTENER_VERSION,
                                        'status' => FTS_URL_SHORTENER_STATUS,
                                        ),
				'urlserviceenable' => 'yes',
				'urlservice' => '',
                'useslug' => 'no',
				'niceid' => 'no',
                'niceid_prefix' => '/',
                'appendurl' => array('home '=> 'no',
                                     'single' => 'no',
                                     'page' => 'no',
                                     'text' => 'Short URL:',
                                    ),
			);
		    $saved = get_option($this->db_option);
		
		    if (!empty($saved)) {
				foreach ($saved as $key => $option){
					$options[$key] = $option;
				}
		    }
		    if ($saved != $options){update_option($this->db_option, $options);}
		    return $options;
		}        
//--------Set up everything
		public function install() {$this->manage_options();}       
    }
endif;

//register
if (class_exists('FTS_URL_Shortener')) :
	global $FTS_URL_Shortener;
	$FTS_URL_Shortener = new FTS_URL_Shortener();
    $FTS_URL_Shortener->activate_shortener();
	if (isset($FTS_URL_Shortener)) {
		register_activation_hook(__FILE__, array(&$FTS_URL_Shortener, 'install') );
	}
    //template fx WP < 3.0
	require_once( dirname(__FILE__) . '/lib/backward_fx.php');  
endif;    
?>
