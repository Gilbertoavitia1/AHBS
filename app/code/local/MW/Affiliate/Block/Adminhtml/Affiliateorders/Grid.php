<?php
class MW_Affiliate_Block_Adminhtml_Affiliateorders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $array_network = array();
    protected $array_network_table = array();
    protected $_arrayResult = array();
    
    public function __construct()
    {
    	parent::__construct();
      	$this->setId('affiliatememberGrid');
      	$this->setDefaultSort('customer_id');
      	$this->setDefaultDir('ASC');
      	$this->setSaveParametersInSession(true);
      	$this->setEmptyText(Mage::helper('affiliate')->__('No Orders'));
    }
  	
    protected function _prepareCollection()
    {   
        $collection = Mage::getModel('sales/order')
            ->getCollection()
            ->addFieldToFilter('main_table.status', 'complete');
            
        $collection->getSelect()->join(
            'mw_affiliate_customers',
            'main_table.customer_id = mw_affiliate_customers.customer_id',
            array('customer_invited', 'referral_code', 'referral_sponsor','payment_email'));
            
        $collection->setOrder('main_table.created_at','DESC');
        
      	$this->setCollection($collection);
      	return parent::_prepareCollection();
    }
    
    public function showSubAffiliateNetworkTable($customer_id, $referral, $parent_id, $i)
    {
        if(!in_array($customer_id, $this->array_network_table)) {
            $this->array_network_table[] = $customer_id;

            $size = sizeof($this->getAffiliateParents($customer_id));
            $customer_childs = $this->getAffiliateParents($customer_id);
            
            $customer           = Mage::getModel('customer/customer')->load($customer_id);
            $name               = $customer->getName();
            $name_id            = $customer->getId();
            
            if($referral != '') {
                array_push($this->_arrayResult, $customer_id);
            }

            if($size > 0) {
                $i = $i + 1 ;
                foreach($customer_childs as $customer_child) {
                    $this->showSubAffiliateNetworkTable($customer_child, $name, $name_id, $i);
                }
            }
        }
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
        
    	$this->addColumn('increment_id', array(
            'header'    => $this->__('Order Id'),
            'align'     =>'right',
            'width'     => '10px',
            'index'     => 'increment_id',
        ));
        
        $this->addColumn('purchased_on', array(
            'header'    => $this->__('Sale Date'),
            'align'     =>'right',
            'width'     => '10px',
            'type'      => 'datetime',
            'index'     => 'created_at',
        ));
        
        $this->addColumn('customer_email', array(
            'header'    => $this->__('Customer Email'),
            'align'     =>'right',
            'width'     => '10px',
            'type'      => 'text',
            'index'     => 'customer_email',
        ));
        
      	$this->addColumn('referral_name', array(
            'header'    => Mage::helper('affiliate')->__('Customer Name'),
            'align'     =>'left',
            'index'     => 'customer_id',
            'renderer'  => 'affiliate/adminhtml_renderer_name',
            'filter_condition_callback' => array($this, '_filterReferralnameCondition'),
      	));
      	
      	
      	$this->addColumn('customer_invited', array(
            'header'    => Mage::helper('affiliate')->__('Affiliate Parent Email'),
            'align'     =>'left',
            'width'     => '50px',
            'index'     => 'customer_invited',
            'renderer'  => 'affiliate/adminhtml_renderer_customerinvited',
            'filter_condition_callback' => array($this, '_filterParentEmail')
      	));
        
        
        $this->addColumn('affiliate_code_email', array(
            'header'    => Mage::helper('affiliate')->__('Affiliate Sponsor Email'),
            'align'     =>'left',
            'index'     => 'customer_id',
            'renderer'  => 'affiliate/adminhtml_renderer_parentsponsoremial',
            'filter_condition_callback' => array($this, '_filterSponsorEmail'),
     	));
        
        $this->addColumn('points', array(
            'header'    =>  Mage::helper('affiliate')->__('Points'),
            'width'     => '60',
            'align'	=> 'center',
            'filter'    => false,
//            'sortable'  => false,
            'index'     => 'increment_id',
            'renderer'  => 'affiliate/adminhtml_renderer_points',
            'is_system' => false,
        ));
        
        $this->addColumn('sale_type', array(
            'header'    =>  Mage::helper('affiliate')->__('Sale Type'),
            'width'     => '60',
            'align'	=> 'center',
            'filter'    => false,
//            'sortable'  => false,
            'index'     => 'increment_id',
            'renderer'  => 'affiliate/adminhtml_renderer_saletype',
            'is_system' => false,
        ));
//        
        $this->addColumn('sale_items', array(
            'header'    =>  Mage::helper('affiliate')->__('Order Description'),
            'width'     => '60',
            'align'	=> 'left',
            'filter'    => false,
//            'sortable'  => false,
            'index'     => 'increment_id',
            'renderer'  => 'affiliate/adminhtml_renderer_saleitems',
            'is_system' => false,
        ));
        
		
        $this->addExportType('*/*/exportCsv', Mage::helper('affiliate')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('affiliate')->__('XML'));
	  
      	return parent::_prepareColumns();
  	}
        
    protected function _filterParentEmail($collection, $column)
    {       
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }        
        
        $customer_ids = array();
        $customers = array();
        $value = '%'.$value.'%';

        $customer_collections = Mage::getModel('affiliate/affiliatecustomers')
            ->getCollection()
            ->addFieldToFilter('payment_email', array('like' => "{$value}"));
            
        foreach ($customer_collections as $customer_collection) {
                 $this->showSubAffiliateNetworkTable($customer_collection->getCustomerId(),'',0); 
                 array_push($customers, $this->_arrayResult);
        }
        
        $this->getCollection()->getSelect()
                ->where("mw_affiliate_customers.customer_id in (?)",$customers);  
    }
    
    protected function _filterSponsorEmail($collection, $column)
    {       
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }        
        
        $customer_ids = array();
        $customers = array();
        $value = '%'.$value.'%';

        $customer_collections = Mage::getModel('affiliate/affiliatecustomers')
            ->getCollection()
            ->addFieldToFilter('payment_email', array('like' => "{$value}"));
            
        foreach ($customer_collections as $customer_collection) {
                 $this->showSubAffiliateNetworkTable($customer_collection->getCustomerId(),'',0); 
                 array_push($customers, $this->_arrayResult);
        }
        
        $this->getCollection()->getSelect()
                ->where("mw_affiliate_customers.customer_id in (?)",$customers);       
            
    }
    
    
	protected function _filterReferralnameCondition($collection, $column)
    {
       if (!$value = $column->getFilter()->getValue()) {
            return;
        }
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