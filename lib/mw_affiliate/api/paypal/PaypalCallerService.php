<?php 
// Use this class to construct the NVP calls to the paypal service
 
class PayPal_CallerService {
 
    public $API_UserName;
    public $API_Password;
    public $API_Signature;
    public $API_Endpoint;
    public $last_request;
    public $last_response;
    public $version;
    public $subject;
     
    public function __construct($credentials)
    {
        if(is_array($credentials)) {
             
            $this->API_UserName  = (isset($credentials['API_USERNAME'])  && !empty($credentials['API_USERNAME']))  ? $credentials['API_USERNAME'] : '';
            $this->API_Password  = (isset($credentials['API_PASSWORD'])  && !empty($credentials['API_PASSWORD']))  ? $credentials['API_PASSWORD'] : '';
            $this->API_Signature = (isset($credentials['API_SIGNATURE']) && !empty($credentials['API_SIGNATURE'])) ? $credentials['API_SIGNATURE'] : '';
            $this->API_Endpoint  = (isset($credentials['API_ENDPOINT'])  && !empty($credentials['API_ENDPOINT']))  ? $credentials['API_ENDPOINT'] : 'https://api-3t.sandbox.paypal.com/nvp';
            $this->version       = (isset($credentials['VERSION'])       && !empty($credentials['VERSION']))       ? $credentials['VERSION'] : '60.0';
            $this->subject       = (isset($credentials['SUBJECT'])       && !empty($credentials['SUBJECT']))       ? $credentials['SUBJECT'] : '';
         
        } else {
            throw new Exception('You must pass in an array of credentials to instantiate this class.');
        }
    }
     
    public function callPayPal($methodName,$base_call = NULL)
    {
        $header_call = 	"&PWD=" . urlencode($this->API_Password).
                        "&USER=" . urlencode($this->API_UserName).
						"&SIGNATURE=" . urlencode($this->API_Signature).
						"&SUBJECT=" . urlencode($this->subject).
                        "&VERSION=" . urlencode($this->version);
             
        $call = $header_call.$base_call;
         
        //setting the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->API_Endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
         
        //Turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
         
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);
         
        //If USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
        //Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php
        /*if(USE_PROXY)
            curl_setopt ($ch, CURLOPT_PROXY, PROXY_HOST.":".PROXY_PORT);*/
         
        //Check if version is included in $nvpStr else include the version.
        if(strlen(str_replace('VERSION=', '', strtoupper($call))) == strlen($call)) {
             
            $nvpStr = "&VERSION=" . urlencode($this->version) . $call;  
        }
         
        $nvpreq="METHOD=".urlencode($methodName).$call;
         
        $this->last_request = $nvpreq;
         
        //Setting the nvpreq as POST FIELD to curl
        curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);
         
        //Getting response from server
        $response = curl_exec($ch);
         
        //Converting NVPResponse to an Associative Array
        $nvpResArray = $this->deformatNVP($response);
        $nvpReqArray = $this->deformatNVP($nvpreq);
        $_SESSION['nvpReqArray'] = $nvpReqArray;
         
        if (curl_errno($ch)) {
             
            throw new Exception('Curl error: '.curl_errno($ch).' - '.curl_error($ch));
               
         } else {
            //Closing the curl
            curl_close($ch);
         }
         
         $this->last_response = $nvpResArray;
          
        return $nvpResArray;
    }
     
    /**
     * This function will take NVPString and convert it to an Associative Array and it will decode the response.
      * It is usefull to search for a particular key and displaying arrays.
      *
      * @nvpstr is NVPString.
      * @nvpArray is Associative Array.
      */
     
    public static function deformatNVP($nvpstr)
    {
        $intial=0;
        $nvpArray = array();
     
        while(strlen($nvpstr)){
            //postion of Key
            $keypos= strpos($nvpstr,'=');
            //position of value
            $valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);
     
            /*getting the Key and Value values and storing in a Associative Array*/
            $keyval=substr($nvpstr,$intial,$keypos);
            $valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
			
            //decoding the response
            $nvpArray[urldecode($keyval)] = urldecode( $valval);
            $nvpstr = substr($nvpstr,$valuepos+1,strlen($nvpstr));
         }
          
        return $nvpArray;
    }
}