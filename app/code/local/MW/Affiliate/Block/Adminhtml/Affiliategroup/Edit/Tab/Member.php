<?php

class MW_Affiliate_Block_Adminhtml_Affiliategroup_Edit_Tab_Member extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('affiliate_group_member');
      $this->setDefaultSort('customer_id');
      //$this->setDefaultDir('ASC');
   	  $this->setUseAjax(true);
      $collection = Mage::getModel('affiliate/affiliategroupmember')->getCollection()
        				->addFieldToFilter('group_id',$this->getRequest()->getParam('id'));
	  if(sizeof($collection) > 0){
	        	$this->setDefaultFilter(array('in_group_member'=>1));
	  	}
  }
  public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/memberGrid', array('_current'=>true));
    }
 	// trong truong hop search 
    // voi yes, any.....
	protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_group_member') {
            $memberIds = $this->_getSelectedMembers();
            if (empty($memberIds)) {
                $memberIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.customer_id', array('in'=>$memberIds));
            } else {
                if($memberIds) {
                    $this->getCollection()->addFieldToFilter('main_table.customer_id', array('nin'=>$memberIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
  protected function _prepareCollection()
  {   
  	 // $customer = Mage::getModel('customer/customer')->getCollection();
  	  $resource = Mage::getModel('core/resource');
  	  $customer_table = $resource->getTableName('customer/entity');
  	  $credit_customer = Mage::getModel('credit/creditcustomer')->getCollection();
  	  $credit_table = $credit_customer->getTable('creditcustomer');
  	  
      $collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection()->addFieldToFilter('active',MW_Affiliate_Model_Statusactive::ACTIVE);
      $collection->getSelect()->join(
      							array('customer_entity'=>$customer_table),'main_table.customer_id = customer_entity.entity_id',array('email'));
      $collection->getSelect()->join(
     							 array('mw_credit_customer'=>$credit_table),'main_table.customer_id = mw_credit_customer.customer_id',array('credit'));
      
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }
  private function _getNameArray()
    {
    	$arr = array();
    	$collection = Mage::getModel('customer/customer')->getCollection()->addNameToSelect();
		foreach($collection as $item){
			$arr[$item->getId()] = $item->getName();
		}
        return $arr;
    }

  protected function _prepareColumns()
  {   
  	 $this->addColumn('in_group_member', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_products',
                'values'            => $this->_getSelectedMembers(),
                'align'             => 'center',
                'index'             => 'customer_id'
            ));
      $this->addColumn('customer_id', array(
          'header'    => Mage::helper('affiliate')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'customer_id',
      ));

      $this->addColumn('referral_name', array(
          'header'    => Mage::helper('affiliate')->__('Affiliate Name'),
          'align'     =>'left',
          'index'     => 'customer_id',
          'renderer'  => 'affiliate/adminhtml_renderer_name',
      	 // 'type'      => 'options',
          //'options'   => $this->_getNameArray(),
      ));
      $this->addColumn('email', array(
          'header'    => Mage::helper('affiliate')->__('Affiliate Account'),
          'align'     =>'left',
          'index'     => 'email',
          //'renderer'  => 'affiliate/adminhtml_renderer_email',
      ));
	  
      $this->addColumn('total_commission', array(
          'header'    => Mage::helper('affiliate')->__('Total Commission'),
          'align'     =>'left',
          'index'     => 'total_commission',
      	  'type'      => 'price',
	  	  'currency_code' => Mage::app()->getBaseCurrencyCode(),
      ));
      $this->addColumn('total_paid', array(
          'header'    => Mage::helper('affiliate')->__('Total Paid Out'),
          'align'     =>'left',
          'index'     => 'total_paid',
      	  'type'      => 'price',
	  	  'currency_code' => Mage::app()->getBaseCurrencyCode(),
      ));
     
      $this->addColumn('credit', array(
          'header'    => Mage::helper('affiliate')->__('Current Balance'),
          'align'     =>'left',
          'index'     => 'credit',
	  	  'type'      => 'price',
	  	  'currency_code' => Mage::app()->getBaseCurrencyCode(),
      ));
      
      $this->addColumn('status', array(
          'header'    => Mage::helper('affiliate')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enable',
              2 => 'Disable',
          ),
      ));
      $this->addColumn('position', array(
            'header'            => Mage::helper('catalog')->__('Position'),
            'name'              => 'position',
            'width'             => 60,
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'index'             => 'position',
            'editable'          => true,
            'edit_only'         => true,
        ));
	  
      return parent::_prepareColumns();
  }
 // tao ra mang cac program ma khach hang da tham gia
	protected function _getSelectedMembers()
    {
        $members = array_keys($this->getSelectedAddMembers());
        return $members;
    }

    public function getSelectedAddMembers()
    {
        
    	$collection = Mage::getModel('affiliate/affiliategroupmember')->getCollection()
        				->addFieldToFilter('group_id',$this->getRequest()->getParam('id'));
        $members = array();
        
        foreach ($collection as $member) {
            $members[$member->getCustomerId()] = $member->getCustomerId();
        }
        return $members;
    }

}