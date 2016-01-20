<?php

class MW_Affiliate_Adminhtml_AffiliateparentController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('affiliate/member')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Manager Members'), Mage::helper('adminhtml')->__('Manager Member'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	public function massParentAffiliateAction()
    {   
        $customerIds = $this->getRequest()->getParam('customerGrid');
        if(!is_array($customerIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select member(s)'));
        } else {
            try {
            	$parent_affiliate_id = 0;
            	$parent_affiliate = $this->getRequest()->getParam('parent_affiliate');
             	if(isset($parent_affiliate) && $parent_affiliate != ''){
             		
				 	$affiliateFilters = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('email',$parent_affiliate);
					if(sizeof($affiliateFilters) > 0 )
			      	{ 
			         	foreach ($affiliateFilters as $affiliateFilter) 
			         	{
			         	  
				          $mw_check = Mage::helper('affiliate')->getActiveAndEnableAffiliate($affiliateFilter->getId());
			         	  if($mw_check == 1)
				           {	
				           	  $parent_affiliate_id = $affiliateFilter->getId();   
				           }else{
				           		$this->_getSession()->addError($this->__('Affiliate parent invalid'));
					            $this->_redirect('*/*/index');
				 			    return;	
				           }
				           break;
			         	} 
			           
			         }else{
			         	$this->_getSession()->addError($this->__('Affiliate parent invalid'));
			            $this->_redirect('*/*/index');
		 			    return;
			         }  			  
				}
				
				$count = 0;
                foreach ($customerIds as $customerId) {
                	if($parent_affiliate_id && $parent_affiliate_id != $customerId){
                		$count = $count + 1;
                		$Collection_affiliates = Mage::getSingleton('affiliate/affiliatecustomers')->getCollection()
                													 ->addFieldToFilter('customer_id',$customerId);
                		if(sizeof($Collection_affiliates)>0){
                			Mage::getSingleton('affiliate/affiliatecustomers')->load($customerId)->setCustomerInvited($parent_affiliate_id)->save();
                		}else{
                			$customerData = array('customer_id'=>$customerId,
					              				  'active'=>MW_Affiliate_Model_Statusactive::INACTIVE,
					              				  'payment_gateway'=>'',
					              				  'payment_email'=>'',
					              				  'auto_withdrawn'=>0,
					              				  'withdrawn_level'=>0,
				    							  'reserve_level'=>0,
				    							  'bank_name'=>'',
										    	  'name_account'=>'',
										    	  'bank_country'=>'',
										    	  'swift_bic'=>'',
										    	  'account_number'=>'',
										    	  're_account_number'=>'',
										    	  'referral_site'=>'',
				    							  'total_commission'=>0,
				    							  'total_paid'=>0,
				    							  'referral_code' =>'',
				    							  'referral_sponsor' =>'',
				    							  'customer_url' =>'',
				    							  'customer_photo' =>'',
				    							  'status'=>1,
				    		                      'invitation_type'=> MW_Affiliate_Model_Typeinvitation::NON_REFERRAL,
				    							  'customer_time' => now(),
					              				  'customer_invited'=>$parent_affiliate_id);
	    		
	    					Mage::getModel('affiliate/affiliatecustomers')->saveCustomerAccount($customerData);
                		}
                													 
                	}	
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', $count)
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    public function exportCsvAction()
    {
        $fileName   = 'customer.csv';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliateparent_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'customer.xml';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliateparent_grid')
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