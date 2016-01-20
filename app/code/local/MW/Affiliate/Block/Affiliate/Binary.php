<?php
class MW_Affiliate_Block_Affiliate_Binary extends Mage_Core_Block_Template
{
    protected $_arrayResult = array();
	
    public function __construct()
    {        
        parent::__construct();        
    	$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();        
        $item = isset($_GET['item']) ? $_GET['item'] : 1;   
        
        $firstDay   = date("Y-m-d 00:00:00", strtotime("last friday"));
        $lastDay    = date("Y-m-d 23:59:59", strtotime("today") );
        
        switch ($item) {
            case 1:                
                $this->_arrayResult = Mage::helper('affiliate_binary')->binaryNetwork($customer_id, '', null, 0, $firstDay, $lastDay);
                break;
            case 2:
                $this->_arrayResult = Mage::helper('affiliate_binary')->binaryDirectNetwork($customer_id, '', null, 0, $firstDay, $lastDay);
                break;
            case 3:
                $this->_arrayResult = Mage::helper('affiliate_binary')->binaryHoldTank($customer_id);
                break;
            default:
                $this->_arrayResult = Mage::helper('affiliate_binary')->binaryNetwork($customer_id, '', null, 0, $firstDay, $lastDay);
                break;
        }
        
        $customers = $this->_arrayResult;
        
        
        foreach ($customers as $key => $value) {
            if ($value['itemTitleColor'] == '#ff0000'){
                $id = Mage::getModel('affiliate/affiliatecustomers')->getCollection()->addFieldToFilter('referral_code',$value['id'])->getFirstItem()->getData("customer_id");
                $expired_package= Mage::getModel('affiliate/affiliateexpired')->load($id);
                
                $today  = strtotime("today");
                $last_pkt = strtotime($expired_package['expired_package']. ' -1 Month');
        
                if($expired_package['expired_package']){
                    if ($last_pkt <= $today){
                        $diff = abs($today - $last_pkt);
                        $days = floor(($diff)/(60*60*24));

                        if ($days <= 30){
                            $customers[$key]['itemTitleColor']= '#166700';
                        }
                    }            
                }                
            }            
        }
        
        Mage::register('items', $customers);
        Mage::register('flag', $item);
    }
        
    public function _prepareLayout()
    {
        parent::_prepareLayout();

//        $pager = $this->getLayout()->createBlock('page/html_pager', 'customer_affiliate_network')
//                              ->setCollection($this->getAffiliateNetworkCollection());	
//        $this->setChild('pager', $pager);
//        $this->getCollection()->load();
        
        return $this;
    }
    
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
    public function getCollection()
    {
    	return $this->getChild("pager")->getCollection();
    }
}