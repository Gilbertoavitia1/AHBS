<?php
class MW_Affiliate_Block_Adminhtml_Affiliatereport_Noactives_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
    	parent::__construct();
      	$this->setId('affiliatenoactivesGrid');
      	$this->setDefaultSort('customer_id');
      	$this->setDefaultDir('ASC');
      	$this->setSaveParametersInSession(true);
      	$this->setEmptyText(Mage::helper('affiliate')->__('No Inactive Affiliates Found'));
    }
  	
    protected function _prepareCollection()
    {   
        $firstDay   = date("Y-m-d 00:00:00", strtotime("first day of this month"));
        $lastDay    = date("Y-m-d 23:59:59", strtotime("last day of this month"));
            
        $saleCollection = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('updated_at', array('from'=>$firstDay, 'to'=>$lastDay))
            ->addAttributeToFilter('status', array('eq' => Mage_Sales_Model_Order::STATE_COMPLETE));
        
        $customer_id = array();
        foreach ($saleCollection as $key => $value) {
            array_push($customer_id, $value->getCustomerId());
        }    
        
        $resource = Mage::getModel('core/resource');
        $customer_table = $resource->getTableName('customer/entity');
        
        $collection = Mage::getModel('affiliate/affiliatecustomers')
            ->getCollection()
            ->addFieldToFilter('customer_id', array('nin' => $customer_id));
        
        $collection->getSelect()->join(
            array('customer_entity'=>$customer_table),'main_table.customer_id = customer_entity.entity_id',array('website_id', 'email'));
        
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
     
     	if(!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => Mage::helper('affiliate')->__('Website'),
                'align'     => 'center',
                'width'     => '250px',
                'type'      => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index'     => 'website_id',
            ));
        }
	  
//        $this->addColumn('action',
//            array(
//                'header'    =>  Mage::helper('affiliate')->__('Action'),
//                'align'     => 'center',
//                'width'     => '40',
//                'type'      => 'action',
//                'getter'    => 'getId',
//                'actions'   => array(
//                    array(
//                        'caption'   => Mage::helper('affiliate')->__('View'),
//                        'url'       => Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit/',array('id'=> $this->getId())),
//                        'target'    => '_blank',
//                        'field'     => 'id'
//                    )
//                ),
//                'filter'    => false,
//                'sortable'  => false,
//                'index'     => 'stores',
//                'is_system' => true,
//        ));
		
        $this->addExportType('*/*/exportCsv', Mage::helper('affiliate')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('affiliate')->__('XML'));
	  
      	return parent::_prepareColumns();
    }
    

    public function getRowUrl($row)
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit/',array('id'=> $row->getId()));
    }
    
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>false));
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