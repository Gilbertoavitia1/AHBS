<?php
class MW_Affiliate_WebsiteController extends Mage_Core_Controller_Front_Action 
{
	public function indexAction()
	{
		$store_id = Mage::app()->getStore()->getId();
		if(!Mage::helper('affiliate/data')->getEnabledStore($store_id)){
			$this->norouteAction();
			return;
		}
		$active = Mage::helper('affiliate')->getAffiliateActive();
		$lock = Mage::helper('affiliate')->getAffiliateLock();
		if($active > 0)
		{
			if($lock > 0)
			{
				$this->_redirect('affiliate/index/referralaccount/');
				return;
			}
			$this->loadLayout();
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('checkout/session');
			$this->renderLayout();
		}
		else if($active == 0)
		{
			$this->_redirect('affiliate/index/createaccount/');
		}
	}
	
	public function editAction() 
	{
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('affiliate/affiliatewebsite')->load($id);
	
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('customer/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
	
			$this->loadLayout();
			$this->renderLayout();
		} else {
			Mage::getSingleton('customer/session')->addError(Mage::helper('affiliate')->__('Domain is not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	public function newAction() {
		$this->_forward('edit');
	}
	
	public function saveAction() 
	{
		$url = $this->getRequest()->getParam('website_domain');
		
		if(Mage::helper('affiliate')->validateDomainUrl($url)) {
			
			/* Check domain is registerd by another affiliate or not */
			$isExisted = Mage::getModel('affiliate/affiliatewebsitemember')
						 ->getCollection()
						 ->addFieldToFilter('domain_name', array('eq' => trim($url)));
			if(count($isExisted) > 0) {
				Mage::getSingleton('core/session')->addError(Mage::helper('affiliate')->__('This domain is registered by another affiliate'));
				$this->_redirect('*/*/');
			}
			
			$verifiedKey = Mage::helper('affiliate')->getWebsiteVerificationKey(trim($url));
			$website = array(
							'customer_id' 	=> (int)Mage::getSingleton("customer/session")->getCustomer()->getId(),
							'domain_name' 	=> trim($url),
							'verified_key'	=> $verifiedKey,
							'status'		=> MW_Affiliate_Model_Statuswebsite::UNVERIFIED,		
					   );
			
			try {
				$model = Mage::getModel('affiliate/affiliatewebsitemember'); 
				$model->setData($website);
				$model->save();
				
				$message = 'Your new website has been added successfully. To verify, please insert this verified key: '  . htmlspecialchars($verifiedKey) . ' to your website header. Then click "Verify now" to verify.';
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('affiliate')->__($message));
			} catch(Exception $e) {
				Mage::getSingleton('core/session')->addError(Mage::helper('affiliate')->__('Something wrong. Please try again'));
			}
			$this->_redirect('*/*/');
			
		} else {
			Mage::getSingleton('core/session')->addError(Mage::helper('affiliate')->__('Invalid domain'));
			$this->_redirect('*/*/');
		}
	}
	
	public function downloadAction() 
	{
		$websiteId = $this->getRequest()->getParam('website_id');
		
		if(!$websiteId) {
			Mage::getSingleton('core/session')->addError(Mage::helper('affiliate')->__('Website id is not exist'));
			$this->_redirect('*/*/');
		}
		
		$hashDomainName = md5(Mage::getModel('affiliate/affiliatewebsitemember')->load($websiteId)->getDomainName());
		$fileName = $hashDomainName . '.txt';
		$fileContent = $hashDomainName;
		
		header('Content-Description: File Transfer');
		header('Cache-Control:public');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $fileName.'"');
		header('Content-Length: ' . strlen($fileContent));
		
		echo $fileContent;
		exit;
	}
	
	public function verifyAction() 
	{
		$websiteId = $this->getRequest()->getParam('website_id');
		
		if(!$websiteId) {
			Mage::getSingleton('core/session')->addError(Mage::helper('affiliate')->__('Website Id does not exist'));
			$this->_redirect('*/*/');
		}
		
		$model = Mage::getModel('affiliate/affiliatewebsitemember')->load($websiteId);
		$domainName = $model->getDomainName();
		
		$htmlContent = file_get_contents($domainName);
		$verifiedKey = $model->getVerifiedKey();
		
		$verifiedFileUrl  = $domainName . '/' . md5($domainName) . '.txt';
		$verifiedFileContent = file_get_contents($verifiedFileUrl);
		
		/* Check meta-tag is inserted to affiliate website OR verified-file is upload to affiliate website host? */
		if(strpos($htmlContent, $verifiedKey) !== false || strcmp($verifiedFileContent, md5($domainName)) === 0) 
		{
			$model->setId($websiteId)->setStatus(MW_Affiliate_Model_Statuswebsite::VERIFIED);
			$model->save();
			
			Mage::getSingleton('core/session')->addSuccess(Mage::helper('affiliate')->__('Your website has been verified successfully'));
			$this->_redirect('*/*/');
		}
		else {
			Mage::getSingleton('core/session')->addError(Mage::helper('affiliate')->__('Verify unsuccessfully! Please try again'));
			$this->_redirect('*/*/');
		}
	}
	public function removeAction() 
	{
		$websiteId = $this->getRequest()->getParam('website_id');
		//get customer id logged in
		$customer_current = Mage::getSingleton('customer/session')->getId();
		//get customer id in website member table
		$model = Mage::getModel('affiliate/affiliatewebsitemember')->load($websiteId);
		$customer_website = $model->getCustomerId();
		//var_dump($customer_website);die;
		if($customer_current == $customer_website && $websiteId > 0 ) {
			try {
				$model = Mage::getModel('affiliate/affiliatewebsitemember');
				 
				$model->setId($this->getRequest()->getParam('website_id'))
					->delete();
					 
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('core/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('website_id' => $this->getRequest()->getParam('website_id')));
			}
		}
		$this->_redirect('*/*/');
	}
	
}