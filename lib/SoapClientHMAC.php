<?php
class SoapClientHMAC extends SoapClient {
  public function __doRequest($request, $location, $action, $version, $one_way = NULL) {
	global $context;
	$hmackey = "2BgyFGa7s1WQ0jMfG3vCff3m5ImmZCpr"; 
	$keyid = "225800"; 
	$hashtime = date("c");
	$hashstr = "POST\ntext/xml; charset=utf-8\n" . sha1($request) . "\n" . $hashtime . "\n" . parse_url($location,PHP_URL_PATH);
	$authstr = base64_encode(hash_hmac("sha1",$hashstr,$hmackey,TRUE));
	if (version_compare(PHP_VERSION, '5.3.11') == -1) {
		ini_set("user_agent", "PHP-SOAP/" . PHP_VERSION . "\r\nAuthorization: GGE4_API " . $keyid . ":" . $authstr . "\r\nx-gge4-date: " . $hashtime . "\r\nx-gge4-content-sha1: " . sha1($request));
	} else {
		stream_context_set_option($context,array("http" => array("header" => "authorization: GGE4_API " . $keyid . ":" . $authstr . "\r\nx-gge4-date: " . $hashtime . "\r\nx-gge4-content-sha1: " . sha1($request))));
	}
    return parent::__doRequest($request, $location, $action, $version, $one_way);
  }
  
  public function SoapClientHMAC($wsdl, $options = NULL) {
	global $context;
	$context = stream_context_create();
	$options['stream_context'] = $context;
	return parent::SoapClient($wsdl, $options);
  }
}

?>