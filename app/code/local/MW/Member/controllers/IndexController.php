<?php
class MW_Member_IndexController extends Mage_Core_Controller_Front_Action{
    
    public function indexAction(){
        $brandName = $this->getRequest()->getParam('member_url');
        
        $this->loadLayout();
        $block = $this->getLayout()->getBlock('members');//get the block instance
        if ($block){ //if block is valid
             if (is_null($brandName)){//if no brand name is supplied
                 $block->setTemplate('mw_member/no_member.phtml');
             }
             else{
                 $block->setTemplate('mw_member/member.phtml');
             }
        }
        $this->renderLayout();
    }
    
    public function signinAction(){
        $id = $this->getRequest()->getParam('member_id');        
        $customer = Mage::getModel('customer/customer')->load($id);
        $preferedStoreViewId = $customer->getWebsiteId();
        
        $store = Mage::getModel('core/store')->load($preferedStoreViewId)->getCode();
    
        if($customer->getId()>0){
            $userSession = Mage::getSingleton('customer/session');
            $userSession->setCustomer($customer);
            
            $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$store.'/customer/account';      
            header("Location: {$url}");
            die();
        }
    }
}