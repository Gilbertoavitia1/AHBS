<?php
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Edit_Tab_Network extends Mage_Adminhtml_Block_Widget_Grid
{
	protected $_arrayResult = array();
	protected $array_network_table = array();

 	public function __construct()
    {
        parent::__construct();
        $this->setId('Affiliate_member_network');
        $this->setUseAjax(true);
        $this->setEmptyText(Mage::helper('affiliate')->__('No Sub-affiliate found'));
    }
	public function getGridUrl()
    {
    	return $this->getUrl('affiliate/adminhtml_affiliatemember/network', array('id'=>$this->getRequest()->getParam('id')));
    }
    
	protected function _prepareCollection()
  	{
  		$this->showSubAffiliateNetworkTable($this->getRequest()->getParam('id'), '', 0);
  		$collection = new Varien_Data_Collection();
  		$i = 1;
  		foreach ($this->_arrayResult as $row) {
  			$rowObj = new Varien_Object();
  			$rowObj->setData($row);
  			$collection->addItem($rowObj);
  			$i = $i + 1;
  		}
  		/*
  		$this->setAffiliateNetworkCollection($collection);
      	$collection = Mage::getModel('affiliate/affiliatetransaction')->getCollection()
      					->addFieldToFilter('customer_invited',$this->getRequest()->getParam('id'))
						->setOrder('transaction_time', 'DESC')
						->setOrder('history_id', 'DESC');
		*/
      	$this->setCollection($collection);
      	return parent::_prepareCollection();
  	}
  	protected function _prepareColumns()
  	{
        $this->addColumn('level', array(
            'header'    => Mage::helper('affiliate')->__('Level'),
            'align'     => 'center',
            'index'     => 'level',
        	'width'		=> '10',
        ));
        
        $this->addColumn('name', array(
            'header'    =>  Mage::helper('affiliate')->__('Name'),
            'align'     =>  'left',
            'index'     =>  'name',
        ));
      	
      	$this->addColumn('Email', array(
          	'header'    => Mage::helper('affiliate')->__('Email'),
          	'index'     => 'email',
      	));
	  
		$this->addColumn('commission', array(
            'header'    	=>  Mage::helper('affiliate')->__('Commission'),
            'index'     	=>  'commission',
			'type'      	=>  'price',
			'currency_code' =>  Mage::app()->getBaseCurrencyCode(),
        ));
        
        $this->addColumn('referred_by', array(
        	'header'    	=> Mage::helper('affiliate')->__('Referred by'),
          	'align'     	=> 'left',
          	'index'     	=> 'referral'
      	));
        
        $this->addColumn('status', array(
          	'header'    	=> Mage::helper('affiliate')->__('Status'),
          	'align'     	=> 'center',
          	'index'     	=> 'status',
        	'width'			=> '10'
      	));
        
        $this->addColumn('joined_date', array(
        		'header'    =>  Mage::helper('affiliate')->__('Joinded Date'),
        		'type'      =>  'datetime',
        		'align'     =>  'center',
        		'index'     =>  'joined_date',
        		'gmtoffset' => true,
        		'default'   =>  ' ---- '
        ));
        
        
      	return parent::_prepareColumns();
  	}
  	
  	public function getAffiliateParents($customer_id)
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
  	
  	public function getSizeAffiliateNetwork()
  	{
  		$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
  		if($customer_id) {
  			return sizeof($this->getAffiliateParents($customer_id));
  		}
  		else return 0;
  	}
  	
  	public function showSubAffiliateNetwork($customer_id)
  	{
  		$result = '';
  		if(!in_array($customer_id, $this->array_network)) {
  			$this->array_network[] = $customer_id;
  	
  			$size = sizeof($this->getAffiliateParents($customer_id));
  			$customer_childs = $this->getAffiliateParents($customer_id);
  			$name = Mage::getModel('customer/customer')->load($customer_id)->getName();
  				
  			$result = '<li style="padding-left:20px;font-weight:bold; list-style:inside;">' . $name;
  			if($size > 0) {
  				$result .= '<ul>';
  				foreach ($customer_childs as $customer_child) {
  					$result .= $this->showSubAffiliateNetwork($customer_child);
  				}
  				$result .= '</ul>';
  			}
  			$result .= '</li>';
  		}
  		return $result;
  	}
  	
  	public function showSubAffiliateNetworkTable($customer_id, $referral, $i)
  	{
  		if(!in_array($customer_id, $this->array_network_table)) {
  			$this->array_network_table[] = $customer_id;
  	
  			$size = sizeof($this->getAffiliateParents($customer_id));
  			$customer_childs = $this->getAffiliateParents($customer_id);
  			$name = Mage::getModel('customer/customer')->load($customer_id)->getName();
  			$email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
  				
  			$affiliateCustomer = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id);
  			$customerCommission = $affiliateCustomer->getTotalCommission();
  			$customerJoinedDate = $affiliateCustomer->getCustomerTime();
  			$customerStatus		= $affiliateCustomer->getActive();
  				
  			$statusOptions = MW_Affiliate_Model_Statusactive::getOptionArray();
  			if($referral != '') {
  				$this->_arrayResult[] = array(
  						'level'			=> $i,
  						'name'			=> $name,
  						'email'			=> $email,
  						'referral'		=> $referral,
  						'commission'	=> $customerCommission,
  						'joined_date'	=> $customerJoinedDate,
  						'status'		=> $statusOptions[$customerStatus]
  				);
  			}
  				
  			if($size > 0) {
  				$i = $i + 1 ;
  				foreach($customer_childs as $customer_child) {
  					$this->showSubAffiliateNetworkTable($customer_child,$name, $i);
  				}
  			}
  		}
  	}
  	 
}
