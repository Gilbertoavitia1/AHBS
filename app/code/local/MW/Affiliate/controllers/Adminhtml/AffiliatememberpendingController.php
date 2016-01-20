<?php
class MW_Affiliate_Adminhtml_AffiliatememberpendingController extends Mage_Adminhtml_Controller_Action
{   
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('affiliate/transaction')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('affiliate/affiliatecustomers')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('affiliate_data_member', $model);

			$this->loadLayout();
			$this->_setActiveMenu('affiliate/member');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliatememberpending_edit'))
				->_addLeft($this->getLayout()->createBlock('affiliate/adminhtml_affiliatememberpending_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Member does not exist'));
			$this->_redirect('*/*/');
		}
	}
	public function approveAction()
    {
    	$customer_id = $this->getRequest()->getParam('id');
    	//var_dump($this->getRequest()->getParams());die();
    	if($customer_id){
    		Mage::getSingleton('affiliate/affiliatecustomers')->load($customer_id)
		                        ->setActive(MW_Affiliate_Model_Statusactive::ACTIVE)
		                        ->setCustomerTime(now())
		                        ->save();
		                        
	    	// set lai referral code cho cac customer affiliate
	        Mage::helper('affiliate') ->setReferralCode($customer_id);
	        // tu dong chuyen thanh vien do vao trong default group
	        $store_id = Mage::getModel('customer/customer')->load($customer_id)->getStoreId();
	        Mage::helper('affiliate') ->setMemberDefaultGroupAffiliate($customer_id,$store_id,1);                     
	        //gui mail khi admin dong y cho gia nhap vao affiliate
	        Mage::helper('affiliate') ->sendMailCustomerActiveAffiliate($customer_id);
	    
           //set total member customer program 
		   Mage::helper('affiliate')->setTotalMemberProgram(); 
		   $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully updated', 1));
		   $this->_redirect('*/*/index');         	
                     	
    	}
    }
   public function massStatusAction()
    {   
    	//zend_debug::dump($this->getRequest()->getParams());exit;
        $customerIds = $this->getRequest()->getParam('affiliate_pending');
        if(!is_array($customerIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select member(s)'));
        } else {
            try {
                foreach ($customerIds as $customerId) {
                    $customer = Mage::getSingleton('affiliate/affiliatecustomers')
                        ->load($customerId)
                        ->setActive($this->getRequest()->getParam('active'))
                        ->setIsMassupdate(true)
                        ->save();
                     if($this->getRequest()->getParam('active') == MW_Affiliate_Model_Statusactive::ACTIVE)
                     {  
                     	// set lai referral code cho cac customer affiliate
                     	Mage::helper('affiliate') ->setReferralCode($customerId);
                     	// tu dong chuyen thanh vien do vao trong default group
                     	$store_id = Mage::getModel('customer/customer')->load($customerId)->getStoreId();
	        			Mage::helper('affiliate') ->setMemberDefaultGroupAffiliate($customerId,$store_id,1);
                     	
                     	//gui mail khi admin dong y cho gia nhap vao affiliate
                     	Mage::helper('affiliate') ->sendMailCustomerActiveAffiliate($customerId);
                     	
                     }
                     else if($this->getRequest()->getParam('active') == MW_Affiliate_Model_Statusactive::NOTAPPROVED)
                     {  
                     	// khi admin khong dong y cho gia nhap vao affiliate
                     	Mage::helper('affiliate') ->sendMailCustomerNotActiveAffiliate($customerId);
                     }
                }
                //set total member customer program 
				Mage::helper('affiliate')->setTotalMemberProgram();
				
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($customerIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	public function exportCsvAction()
    {
        $fileName   = 'affiliate_member_pending.csv';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatememberpending_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'affiliate_member_pending.xml';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatememberpending_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
	protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
	
}