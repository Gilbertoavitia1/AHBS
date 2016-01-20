<?php
class MW_Affiliate_Block_Adminhtml_Affiliatememberpending_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
  	{
    	parent::__construct();
      	$this->setId('affiliatemember_pending');
      	$this->setDefaultSort('customer_id');
      	$this->setDefaultDir('ASC');
      	$this->setSaveParametersInSession(true);
      	$this->setEmptyText(Mage::helper('affiliate')->__('No Pending Affiliates Found'));
  	}

  	protected function _prepareCollection()
  	{
    	$resource = Mage::getModel('core/resource');
  	  	$customer_table = $resource->getTableName('customer/entity');
  	 
      	$collection = Mage::getModel('affiliate/affiliatecustomers')
      				  ->getCollection()
	     			  ->addFieldToFilter('active', array('in' => array(MW_Affiliate_Model_Statusactive::PENDING,MW_Affiliate_Model_Statusactive::NOTAPPROVED)));

  	  	$collection->getSelect()->join(
  	 							array('customer_entity'=>$customer_table),'main_table.customer_id = customer_entity.entity_id',array('email'));

      	$collection ->setOrder('main_table.customer_id', 'DESC');
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
    
   	private function _getAutowithdrawnArray()
    {
    	$arr = array();
		$arr[1] = 'Auto';
		$arr[2] = 'Manual';
        return $arr;
    }
    
   	private function _getPaymentGatewayArray()
    {
    	$arr = array();
    	$gateways = unserialize(Mage::helper('affiliate/data')->getGatewayStore());
		foreach ($gateways as $gateway) {
			$arr[$gateway['gateway_value']] =  $gateway['gateway_title'];
		}
		return $arr;
    }

  	protected function _prepareColumns()
  	{
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
      	  	'filter_condition_callback' => array($this, '_filterReferralnameCondition'),
     	));
      	$this->addColumn('affiliate_parent', array(
          	'header'    => Mage::helper('affiliate')->__('Affiliate Parent'),
          	'align'     =>'left',
          	'index'     => 'customer_id',
          	'renderer'  => 'affiliate/adminhtml_renderer_parentname',
      	  	'filter_condition_callback' => array($this, '_filterReferralnameCondition'),
     	));
        
      	$this->addColumn('affiliate_sponsor', array(
          	'header'    => Mage::helper('affiliate')->__('Affiliate Sponsor'),
          	'align'     =>'left',
          	'index'     => 'customer_id',
          	'renderer'  => 'affiliate/adminhtml_renderer_parentsponsor',
      	  	'filter_condition_callback' => array($this, '_filterReferralnameCondition'),
     	));
        
	  	$this->addColumn('email', array(
          	'header'    => Mage::helper('affiliate')->__('Affiliate Account'),
          	'align'     =>'left',
          	'index'     => 'email',
 	    ));
//      	$this->addColumn('referral_site', array(
//          	'header'    => Mage::helper('affiliate')->__('Affiliate Website(s)'),
//          	'align'     => 'left',
//          	'renderer'  => 'affiliate/adminhtml_renderer_website',
//      	));
      	$this->addColumn('payment_gateway', array(
          	'header'    => Mage::helper('affiliate')->__('Payment Method'),
         	'align'     =>'left',
          	'index'     => 'payment_gateway',
      	  	'type'      => 'options',
          	'options'   => $this->_getPaymentGatewayArray(),
      	));
     	$this->addColumn('payment_email', array(
          	'header'    => Mage::helper('affiliate')->__('Payment Email'),
         	'align'     =>'left',
          	'index'     => 'payment_email',
      	));
      	$this->addColumn('auto_withdrawn', array(
          	'header'    => Mage::helper('affiliate')->__('Withdrawal Request Method'),
          	'align'     =>'left',
          	'index'     => 'auto_withdrawn',
      	  	'width'     => '30px',
      	  	'type'      => 'options',
          	'options'   => $this->_getAutowithdrawnArray(),
      	));
      	$this->addColumn('withdrawn_level', array(
          	'header'    => Mage::helper('affiliate')->__('Auto payment when account balance reaches'),
         	'align'     =>'left',
          	'index'     => 'withdrawn_level',
      	  	'type'      =>'price',
      	  	'currency_code' => Mage::app()->getBaseCurrencyCode(),
      	));
      	$this->addColumn('reserve_level', array(
          	'header'    => Mage::helper('affiliate')->__('Reserve Level'),
          	'align'     =>'left',
          	'index'     => 'reserve_level',
      	  	'type'      =>'price',
      	  	'currency_code' => Mage::app()->getBaseCurrencyCode(),
      	));
      	$this->addColumn('active', array(
          	'header'    => Mage::helper('affiliate')->__('Active'),
          	'align'     => 'left',
          	'width'     => '100px',
          	'index'     => 'active',
          	'type'      => 'options',
          	'options'   => array(
            	1 => 'Pending',
              	4 => 'Not Approved'
          	),
      	));
      	$this->addColumn('action',
            array(
                'header'    =>  Mage::helper('affiliate')->__('Action'),
                'width'     => '100',
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
   
   	public function getRowUrl($row)
   	{
    	return $this->getUrl('*/*/edit', array('id' => $row->getId()));
   	}
   	
	protected function _filterReferralnameCondition($collection, $column)
    {
       if (!$value = $column->getFilter()->getValue()) {
            return;
        }
       $customer_ids = array();
       $value = '%'.$value.'%';

       $customer_collections = Mage::getModel('customer/customer')
       						   ->getCollection()
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

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('customer_id');
        $this->getMassactionBlock()->setFormFieldName('affiliate_pending');

        $statuses = array(
            array('value' => 1 , 'label' => 'Pending'),
            array('value' => 2 , 'label' => 'Approved'),
            array('value' => 4 , 'label' => 'Not Approved'),
			
        );
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('affiliate')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'active',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('affiliate')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
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

        foreach($this->getCollection() as $item) 
        {
            $data = array();
            foreach($this->_columns as $col_id =>$column) 
            {
                if(!$column->getIsSystem()) 
                {
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

        if($this->getCountTotals())
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