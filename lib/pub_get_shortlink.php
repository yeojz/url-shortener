<?php
global $wp_query;
$options = $this->my_options();
$shortlink = '';

//options
$home_url = get_option('home');
$urlservice = $options['urlservice'];
if ($urlservice == ''){
    //$urlservice = 'tinyurl';
	return $shortlink;
}

//some switches
if ($context == 'query') {
    if ( is_singular() ) {
        $id = $wp_query->get_queried_object_id();
        $context = 'post';
    } elseif ( is_front_page() ) {
        $context = 'blog';
    } else {
        return $shortlink;
    }
} 

//Homepage shortlink
if ($context == 'blog') {
    if ( empty($id) )
        $url = $home_url;
    return $this->class_adapter($url, $urlservice);    
}

//get post
$post = get_post($id);
if ( empty($post) ){
    return $shortlink;
}  

//post details    
$post_id = $post->ID;
$post_type = $post->post_type;
$post_status = $post->post_status;
$url = '';
$saved_url = get_post_meta($post_id, 'shorturl', true);

//check prior generation and publish status
if (empty($saved_url) && ($post_status == 'publish' || $transition == true)){
    //Use permalinks
    if ($options['useslug']=='yes'){
        $url = get_permalink($post_id);
        if ($url){
            $shortlink = $this->class_adapter($url, $urlservice);
        }
    //Use IDs
    }else{
        switch ($post_type){
            case 'post' : $url = $home_url."/index.php?p=".$post_id; break;
            case 'page' : $url = $home_url."/index.php?page_id=".$post_id; break;
            default : break;
        }
        if ($url){
            $shortlink = $this->class_adapter($url, $urlservice);
        }
    }    
	//Allow other plugins to use generated shortlink (1st generation)
	if (!empty($shortlink)){
		do_action('fts_use_shortlink', $post_id, $shortlink); 
	}    
//assign saved URL if already generated    
} elseif (!empty($saved_url)) {
    $shortlink = $saved_url;
}

//Update Custom Field
if (empty($saved_url) && !empty($shortlink)){
    update_post_meta($post_id, 'shorturl', $shortlink);
}

if ($options['niceid'] == 'yes' && empty($shortlink)){
	$shortlink = $home_url.$options['niceid_prefix'].$post_id;
}

//Allow other plugins to filter output
if (!empty($shortlink)){
    apply_filters('fts_filter_shortlink', $post_id, $shortlink); 
}    

//return shortlink
return $shortlink;
?>