<?php
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
  	{
    	parent::__construct();
      	$this->setId('affiliatememberGrid');
      	$this->setDefaultSort('customer_id');
      	$this->setDefaultDir('ASC');
      	$this->setSaveParametersInSession(true);
      	$this->setEmptyText(Mage::helper('affiliate')->__('No Active Affiliates Found'));
  	}
  	
  	protected function _prepareCollection()
  	{   
  		$resource = Mage::getModel('core/resource');
  	  	$customer_table = $resource->getTableName('customer/entity');
  	  	$credit_customer = Mage::getModel('credit/creditcustomer')->getCollection();
  	  	$credit_table = $credit_customer->getTable('creditcustomer');
  	  
      	$collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection()->addFieldToFilter('active',MW_Affiliate_Model_Statusactive::ACTIVE);
      	$collection->getSelect()->join(
      							array('customer_entity'=>$customer_table),'main_table.customer_id = customer_entity.entity_id',array('website_id', 'email'));
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
        
         $this->addColumn('signin',
            array(
                'header'    =>  Mage::helper('affiliate')->__('Sign In'),
                'width'     => '90',
                'align'     => 'center',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('affiliate')->__('Sign In'),
                        'url'       => array('base'=> '*/*/signin'),
                        'target'    =>'_blank',
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));         
          
    	$this->addColumn('customer_id', array(
        	'header'    => Mage::helper('affiliate')->__('#'),
          	'align'     => 'right',
          	'width'     => '25px',
          	'index'     => 'customer_id',
      	));
        
    	$this->addColumn('referral_code', array(
        	'header'    => Mage::helper('affiliate')->__('# Distribuidor'),
          	'align'     => 'right',
          	'width'     => '50px',
          	'index'     => 'referral_code',
      	));

      	$this->addColumn('referral_name', array(
        	'header'    => Mage::helper('affiliate')->__('Affiliate Name'),
          	'align'     =>'left',
          	'index'     => 'customer_id',
      	  	'renderer'  => 'affiliate/adminhtml_renderer_name',
      	  	'filter_condition_callback' => array($this, '_filterReferralnameCondition'),
      	));
      	
      	$this->addColumn('email', array(
        	'header'    => Mage::helper('affiliate')->__('Affiliate Account'),
          	'align'     =>'left',
          	'index'     => 'email',
      	));
      	
      	$this->addColumn('customer_invited', array(
          	'header'    => Mage::helper('affiliate')->__('Affiliate Parent'). ' (email)',
          	'align'     =>'left',
            'width'     => '50px',
          	'index'     => 'customer_invited',
      	  	'renderer'  => 'affiliate/adminhtml_renderer_customerinvited',
          	'filter_condition_callback' => array($this, '_filterCustomerInvitedCondition'),
      	));
        
        $this->addColumn('affiliate_sponsor', array(
          	'header'    => "# ". Mage::helper('affiliate')->__('Affiliate Sponsor'),
          	'align'     =>'left',
          	'index'     => 'customer_id',
          	'renderer'  => 'affiliate/adminhtml_renderer_parentsponsor',
      	  	'filter_condition_callback' => array($this, '_filterReferralSponsorCondition'),
     	));
	  
	  	//affiliate group
      	$groups = array();
      	$collection_groups = Mage::getModel('affiliate/affiliategroup')->getCollection();
      	foreach ($collection_groups as $g) {
            $groups[$g->getGroupId()] = $g->getGroupName();
      	}
      
      	$this->addColumn('group_id', array(
        	'header'    	=> Mage::helper('affiliate')->__('Affiliate Group'),
          	'align'     	=> 'left',
          	'index'     	=> 'group_id',
          	'width'	  		=>  150,
      	  	'renderer'  	=> 'affiliate/adminhtml_renderer_affiliategroup',
          	'type' 			=> 'options',
          	'options' 		=> $groups,
      	  	'filter_condition_callback' => array($this, '_filterGroupCondition'),
      	));
      
//      	$this->addColumn('total_commission', array(
//          	'header'    => Mage::helper('affiliate')->__('Total Commission'),
//          	'align'     =>'left',
//          	'index'     => 'total_commission',
//      	  	'type'      => 'price',
//	  	  	'currency_code' => Mage::app()->getBaseCurrencyCode(),
//      	));
//      	
//      	$this->addColumn('total_paid', array(
//          	'header'    => Mage::helper('affiliate')->__('Total Paid Out'),
//         	'align'     =>'left',
//          	'index'     => 'total_paid',
//      	  	'type'      => 'price',
//	  	  	'currency_code' => Mage::app()->getBaseCurrencyCode(),
//     	));
//     
//     	$this->addColumn('credit', array(
//          	'header'    => Mage::helper('affiliate')->__('Current Balance'),
//          	'align'     =>'left',
//          	'index'     => 'credit',
//	  	  	'type'      => 'price',
//	  	  	'currency_code' => Mage::app()->getBaseCurrencyCode(),
//      	));
     
     	if(!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => Mage::helper('affiliate')->__('Website'),
                'align'     => 'center',
                'width'     => '80px',
                'type'      => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index'     => 'website_id',
            ));
        }
        
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
        
      	$this->addColumn('anetwork', array(
          	'header'    => Mage::helper('affiliate')->__('Affiliate Network'),
          	'align'     => 'left',
          	'width'     => '80px',
          	'index'     => 'anetwork',
          	'type'      => 'options',
          	'options'   => array(
            	1 => 'Uninivel',
              	2 => 'Binario',
              	3 => 'Uninivel y Binario',
          	),
      	));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('affiliate')->__('Action'),
                'align'     => 'center',
                'width'     => '40',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('affiliate')->__('View'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
        
       
		
		$this->addExportType('*/*/exportCsv', Mage::helper('affiliate')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('affiliate')->__('XML'));
	  
      	return parent::_prepareColumns();
  	}
        
    protected function _filterReferralnameCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) { return; }
        $customer_ids = array();
        $value = '%'.$value.'%';
        $customer_collections =  Mage::getModel('customer/customer')->getCollection()
                 ->addAttributeToFilter(array(
                     array(
                         'attribute' => 'firstname',
                         array('like' => $value),
                         ),
                     array(
                         'attribute' => 'lastname',
                         array('like' => $value),
                         ),
                     ));

        foreach ($customer_collections as $customer_collection) {
                 $customer_ids[] = $customer_collection->getId();
        }

        $this->getCollection()->getSelect()->where("main_table.customer_id in (?)",$customer_ids);

//        if (!$value = $column->getFilter()->getValue()) return;
//        
//        $customer_ids = array();
//        
//        $fullname = explode(" ", $value);        
//        $search = [];
//        foreach ($fullname as $key => $value) {
//            $search[]=array(
//                'attribute' => 'firstname',
//                array('like' => $value),
//                );           
//            $search[]=array(
//                'attribute' => 'lastname',
//                array('like' => $value),
//                );            
//        }
//            
//        $customer_collections =  Mage::getModel('customer/customer')->getCollection()
//                ->addAttributeToFilter($search);
//        
//        foreach ($customer_collections as $customer_collection) {
//       		$customer_ids[] = $customer_collection->getId();
//        }
//        
//        $this->getCollection()->getSelect()->where("main_table.customer_id in (?)",$customer_ids);
    }
    
	protected function _filterReferralSponsorCondition($collection, $column){
            if (!$value = $column->getFilter()->getValue()) {
            return;
            }
            
            
           $this->getCollection()->getSelect()->where("main_table.referral_sponsor in (?)",$column->getFilter()->getValue());
        }
        
        
	protected function _filterGroupCondition($collection, $column)
    {
       if (!$value = $column->getFilter()->getValue()) {
            return;
        }
       $customer_ids = array();
       $customer_collections =  Mage::getModel('affiliate/affiliategroupmember')->getCollection()->addFieldToFilter('group_id',array('eq' => $value));
       foreach ($customer_collections as $customer_collection) {
       		$customer_ids[] = $customer_collection->getCustomerId();
       }
       $this->getCollection()->getSelect()->where("main_table.customer_id in (?)",$customer_ids);
    }
    
    protected function _filterCustomerInvitedCondition($collection, $column)
    {
       if (!$value = $column->getFilter()->getValue()) {
            return;
        }
       $customer_ids = array();
       $value = '%'.$value.'%';
       $customer_collections =  Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('email',array('like' => $value));
       foreach ($customer_collections as $customer_collection) {
       		$customer_ids[] = $customer_collection->getId();
       }
       $this->getCollection()->getSelect()->where("main_table.customer_invited in (?)",$customer_ids);
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('customer_id');
        $this->getMassactionBlock()->setFormFieldName('affiliatememberGrid');

//        $this->getMassactionBlock()->addItem('delete', array(
//             'label'    => Mage::helper('affiliate')->__('Delete'),
//             'url'      => $this->getUrl('*/*/massDelete'),
//             'confirm'  => Mage::helper('affiliate')->__('Are you sure?')
//        ));

        $statuses = Mage::getSingleton('affiliate/statusreferral')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('affiliate')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('affiliate')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        $this->getMassactionBlock()->addItem('parent_affiliate', array(
             'label'=> Mage::helper('affiliate')->__('Change Affiliate Parent'),
             'url'  => $this->getUrl('*/*/massParentAffiliate', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'parent_affiliate',
                         'type' => 'text',
                         'class' => 'required-entry validate-email',
                         'label' => Mage::helper('affiliate')->__('Affiliate Parent'),
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
  public function getCsv()
    {
        $csv = '';
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->getCollection()->load();
        $this->_afterLoadCollection();

        $data = array();
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $data[] = '"'.$column->getExportHeader().'"';
            }
        }
        $csv.= implode(',', $data)."\n";

        foreach ($this->getCollection() as $item) {
            $data = array();
            foreach ($this->_columns as $col_id =>$column) {
                if (!$column->getIsSystem()) {
                	if($col_id == 'email')
                    {   
                    	$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getEmail()).'"';
                    }
                    else
                    {
                    	$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $column->getRowFieldExport($item)).'"';
                    }
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        if ($this->getCountTotals())
        {
            $data = array();
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $column->getRowFieldExport($this->getTotals())).'"';
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        return $csv;
    }

}