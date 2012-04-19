<?php
//FTShorten Class Adapter
$error = false;
$result = '';
if (array_key_exists($service, $this->supported)){
    $options = $this->my_options();
        
    $ca = new FTShorten();
    $ca->url = $url;
    $ca->pingfmapi = 'f51e33510d3cbe2ff1e16a4a4897f099';
    $ca->service = $service;

	//Check for username and if required
    if (!$user && in_array($service, $this->authuser)){
        $ca->name = htmlentities($options['apiuser_'.$service], ENT_QUOTES); 
        if ($ca->name == '' && in_array($service, $this->requser)){
            $error = true;
        }
    }else{
		$ca->name = $user;
	}

	//Check for authentication key and if required
    if (!$key && in_array($service, $this->authkey)){
        $ca->apikey = htmlentities($options['apikey_'.$service], ENT_QUOTES);
        if ($ca->apikey == '' && in_array($service, $this->reqkey)){
            $error = true;
        }
    }else{
		$ca->apikey = $key;
	}  
	
	//Call supported custom services
	if (in_array($service, $this->generic)){
        $ca->generic = htmlentities($options['generic_'.$service], ENT_QUOTES);
        if ($ca->generic == ''){
            $error = true;
        }
    } 

	//Check for errors
    if ($error){
        $result = '';
    }else{
        $result = $ca->shorturl();
    }
}
return $result;
?>
