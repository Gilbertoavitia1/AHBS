<?php
class MW_Affiliate_CheckurlController extends Mage_Core_Controller_Front_Action
{  
    
    public function indexAction()
    {
	$post = $this->getRequest()->getPost();
	$url = $post['url'];

        if ($url){
            $collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                ->addFieldToFilter('customer_url',$url)
                ->addFieldToFilter('status ',MW_Affiliate_Model_Statusreferral::ENABLED)
                ->addFieldToFilter('active',MW_Affiliate_Model_Statusactive::ACTIVE);

            if ($collection->getData()){ 
                $data = $collection->getData();
                $data = $data[0];
                $customerData = Mage::getModel('customer/customer')->load($data['customer_id'])->getData();

                $url = trim(Mage::getBaseUrl())."home?mw_aref=".md5($customerData['email']);

                echo $url;
            }
        }
    }
    

}
