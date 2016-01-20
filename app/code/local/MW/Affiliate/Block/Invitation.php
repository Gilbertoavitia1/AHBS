<?php
include_once 'lib/mw_affiliate/api/gmail/GmailOath.php';
class MW_Affiliate_Block_Invitation extends Mage_Core_Block_Template
{
	protected $_sClientId;
	protected $_sClientSecret;
	protected $_sCallback;
	protected $_iMaxResults;
	
	protected $_oAuth;
	protected $_oGetContacts;
	
	public function __construct() {
		parent::__construct();
		
		$this->_init();
	}
	
	protected function _init() {
		$this->_sClientId 		= '164869874838.apps.googleusercontent.com';
		$this->_sClientSecret 	= 'QFPpQSprjz7DB3K4PNQ746EA';
		$this->_sCallback 		= Mage::getUrl('affiliate/invitation/gmail');
		$this->_iMaxResults 	= 2000; // max results
		
		$this->_oAuth 			= new GmailOath($this->_sClientId, $this->_sClientSecret, array(), false, $this->_sCallback);
		$this->_oGetContacts 	= new GmailGetContacts();
	}
	
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
    public function getRequestToken() {
    	$oAuth = $this->_oAuth;
    	$oGetContacts = $this->_oGetContacts;
    
    	// prepare access token and set it into session
    	$oRequestToken = $oGetContacts->get_request_token($oAuth, false, true, true);
    
    	Mage::getSingleton('core/session')->setOauthToken($oRequestToken['oauth_token']);
    	Mage::getSingleton('core/session')->setOauthTokenSecret($oRequestToken['oauth_token_secret']);
    
    	return $oAuth->rfc3986_decode($oRequestToken['oauth_token']);
    }
    
    public function getContact() {
    	$oAuth = $this->_oAuth;
    	$oGetContacts = $this->_oGetContacts;
    	
    	$request = $this->getRequest();
    	if($request && $request->getParam('oauth_token'))
    	{
    		// decode request token and secret
    		$sDecodedToken = $oAuth->rfc3986_decode($request->getParam('oauth_token'));
    		$sDecodedTokenSecret = $oAuth->rfc3986_decode(Mage::getSingleton('core/session')->getOauthTokenSecret());
    
    		// get 'oauth_verifier'
    		$oAuthVerifier = $oAuth->rfc3986_decode($request->getParam('oauth_verifier'));
    
    		// prepare access token, decode it, and obtain contact list
    		$oAccessToken = $oGetContacts->get_access_token($oAuth, $sDecodedToken, $sDecodedTokenSecret, $oAuthVerifier, false, true, true);
    		$sAccessToken = $oAuth->rfc3986_decode($oAccessToken['oauth_token']);
    		$sAccessTokenSecret = $oAuth->rfc3986_decode($oAccessToken['oauth_token_secret']);
    		$aContacts = $oGetContacts->GetContacts($oAuth, $sAccessToken, $sAccessTokenSecret, false, true, $this->_iMaxResults);
    		
    		// turn array with contacts into html string
    		$sContacts = $sContactName = '';
    		$response = array();
    		foreach($aContacts as $k => $aInfo) {
    			$sContactName = end($aInfo['title']);
    			$aLast = end($aContacts[$k]);
    			
    			foreach($aLast as $aEmail) {
    				if($aEmail['address']) {
    					$response[] = array(
    							'name' 	=> $sContactName,
    							'email' => $aEmail['address'],
    					);
    				}
    			}
    		}
    		
    		return $response;
    	}
    }
}