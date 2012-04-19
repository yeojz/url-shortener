<?php
    $options = $this->my_options();  
	if($service){
		$service = strip_tags($service);
		$service = preg_replace("/[^a-zA-Z0-9]/", "", $service);
	}else{
		$service = $options['urlservice'];
	}
	if (!$key && in_array($service, $this->authkey)){
		$key = $options['apiuser_'.$service];
	}
    if (!$user && in_array($service, $this->authuser)){
		$user = $options['apikey_'.$services];
	}
	
	$shortlink = $this->class_adapter($url, $service, $key, $user);
    return $shortlink;
?>