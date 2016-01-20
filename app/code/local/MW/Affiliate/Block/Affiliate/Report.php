<?php
class MW_Affiliate_Block_Affiliate_Report extends Mage_Core_Block_Template
{    
    protected $array_network = array();
    protected $array_network_table = array();
    protected $_arrayResult = array();
	
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
                
        if ($_POST){
            $from = new Datetime($_POST['d_from']);            
            $from = $from->format('d-m-Y');            
            $to = new Datetime($_POST['d_to']);            
            $to = $to->format('d-m-Y'); 
                    
            $rank = $_POST['rank'];    
                        
            $_SESSION['_toDate'] = $to;
            $_SESSION['_fromDate'] = $from;
            $_SESSION['_ranks'] = $rank;
            Mage::register('__fromz', $_SESSION['_fromDate']);
            Mage::register('__toz', $_SESSION['_toDate']);
            Mage::register('__ranks', $rank);
        }else{
            $from = isset($_SESSION['_fromDate']) ? $_SESSION['_fromDate'] : date("d-m-Y", strtotime("first day of this month"));
            $to =  isset($_SESSION['_toDate']) ? $_SESSION['_toDate'] : date("d-m-Y");            
            $rank =  isset($_SESSION['_ranks']) ? $_SESSION['_ranks'] : 1;            
            Mage::register('__fromz', $from);
            Mage::register('__toz', $to);                
            Mage::register('__ranks', $rank);                
        }
        
        
        switch ($rank) {
            case 1:
                $this->getAffiliateNetworkReferral($customer_id,'',0); 
                break;
            case 2:
                $this->getAffiliateNetworkSponsor($customer_id,'',0); 
                break;
            case 3:
                $this->getAffiliateNetworkReferralSponsor($customer_id,'',0); 
                break;
            case 4:
                $this->getAffiliateNetworkReferral($customer_id,'',0); 
                break;
            default:
                $this->getAffiliateNetworkReferral($customer_id,'',0); 
                break;
        }
                
        $collection = Mage::getModel('sales/order')
            ->getCollection()
            ->addFieldToFilter('main_table.status', 'complete');
          
        $collection->getSelect()->join(
            'mw_affiliate_customers',
            'main_table.customer_id = mw_affiliate_customers.customer_id',
            array('customer_invited', 'referral_code', 'referral_sponsor','payment_email'));
        
        $collection->getSelect()->join(
            'mw_affiliate_group_member',
            'mw_affiliate_customers.customer_id = mw_affiliate_group_member.customer_id',
            array('group_id'));

        $collection->getSelect()->where("mw_affiliate_customers.customer_id in (?)", $this->_arrayResult);
        
        if ($rank == 4){
            $collection->getSelect()->where("mw_affiliate_group_member.group_id IN (?)", 13);
        }else{            
            $collection->getSelect()->where("mw_affiliate_group_member.group_id NOT IN (?)", 13);
        }
        
        
        $collection->addAttributeToFilter('main_table.updated_at', array(
        'from' => $from. " 00:00:00",
        'to' => $to. " 23:59:59",
        'date' => true,
        ));

        $collection->setOrder('main_table.created_at','DESC');
        
        $pager = $this->getLayout()
            ->createBlock('page/html_pager', 'affiliate_report')
            ->setCollection($collection);
        
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        
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
    
    
    public function getAffiliateNetworkReferralSponsor($customer_id, $referral, $parent_id, $i)
    {
        if(!in_array($customer_id, $this->array_network_table)) {
            $this->array_network_table[] = $customer_id;

            $code = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id)->getReferralCode();
            $customer_childs = $this->getAffiliateReferralSponsorParents($customer_id, $code);
            
            $customer           = Mage::getModel('customer/customer')->load($customer_id);
            $name               = $customer->getName();
            $name_id            = $customer->getId();
            
            if($referral != '') {
                array_push($this->_arrayResult, $customer_id);
            }

            if(sizeof($customer_childs) > 0) {
                $i = $i + 1 ;
                foreach($customer_childs as $customer_child) {
                    $this->getAffiliateNetworkReferralSponsor($customer_child, $name, $name_id, $i);
                }
            }
        }
    }
    
    public function getAffiliateNetworkSponsor($customer_id, $referral, $parent_id, $i)
    {
        if(!in_array($customer_id, $this->array_network_table)) {
            $this->array_network_table[] = $customer_id;

            $code = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id)->getReferralCode();
            $customer_childs = $this->getAffiliateSponsorParents($customer_id, $code);
            
            $customer           = Mage::getModel('customer/customer')->load($customer_id);
            $name               = $customer->getName();
            $name_id            = $customer->getId();
            
            if($referral != '') {
                array_push($this->_arrayResult, $customer_id);
            }

            if(sizeof($customer_childs) > 0) {
                $i = $i + 1 ;
                foreach($customer_childs as $customer_child) {
                    $this->getAffiliateNetworkSponsor($customer_child, $name, $name_id, $i);
                }
            }
        }
    }
    
    public function getAffiliateNetworkReferral($customer_id, $referral, $parent_id, $i)
    {
        if(!in_array($customer_id, $this->array_network_table)) {
            $this->array_network_table[] = $customer_id;
            $customer_childs = $this->getAffiliateReferralParents($customer_id);
            
            $customer           = Mage::getModel('customer/customer')->load($customer_id);
            $name               = $customer->getName();
            $name_id            = $customer->getId();
            
            if($referral != '') {
                array_push($this->_arrayResult, $customer_id);
            }

            if(sizeof($customer_childs) > 0) {
                $i = $i + 1 ;
                foreach($customer_childs as $customer_child) {
                    $this->getAffiliateNetworkReferral($customer_child, $name, $name_id, $i);
                }
            }
        }
    }
    
    public function getAffiliateReferralParents($customer_id)
    {   
        $result = array();
        if($customer_id) {
            $collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                ->addFieldToFilter('customer_invited',$customer_id)
                ->addFieldToFilter('status ',MW_Affiliate_Model_Statusreferral::ENABLED)
                ->addFieldToFilter('active',MW_Affiliate_Model_Statusactive::ACTIVE);

            $result = array_diff($collection->getAllIds(),array($customer_id));
        }
        
        return $result;
    }
    
    public function getAffiliateReferralSponsorParents($customer_id, $code)
    {   
        $result = array();
        if($customer_id) {
            $collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                ->addFieldToFilter('customer_invited',$customer_id)
                ->addFieldToFilter('referral_sponsor',$code)
                ->addFieldToFilter('status ',MW_Affiliate_Model_Statusreferral::ENABLED)
                ->addFieldToFilter('active',MW_Affiliate_Model_Statusactive::ACTIVE);

            $result = array_diff($collection->getAllIds(),array($customer_id));
        }
        
        return $result;
    }
    
    public function getAffiliateSponsorParents($customer_id, $code)
    {   
        $result = array();
        if($customer_id) {
            $collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                ->addFieldToFilter('referral_sponsor',$code)
                ->addFieldToFilter('status ',MW_Affiliate_Model_Statusreferral::ENABLED)
                ->addFieldToFilter('active',MW_Affiliate_Model_Statusactive::ACTIVE);

            $result = array_diff($collection->getAllIds(),array($customer_id));
        }
        
        return $result;
    }
    
}