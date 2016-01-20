<?php
class MW_Affiliate_Block_Adminhtml_Affiliateviewhistory_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  	public function __construct()
  	{
      	parent::__construct();
      	$this->setId('affiliate_viewhistory');
      	$this->setSaveParametersInSession(true);
  	}
  	
  	protected function _prepareCollection()
  	{   
  		$resource = Mage::getModel('core/resource');
  	  	$customer_table = $resource->getTableName('customer/entity');
  		$affiliate_program = Mage::getModel('affiliate/affiliateprogram')->getCollection();
  		$program_table = $affiliate_program->getTable('affiliateprogram');
      	$collection = Mage::getModel('affiliate/affiliatehistory')->getCollection()
      					->addFieldToFilter('order_id',$this->getRequest()->getParam('orderid'))
						->setOrder('transaction_time', 'DESC')
						->setOrder('history_id', 'DESC');
		$collection->getSelect()->join(
      							array('customer_entity'=>$customer_table),'main_table.customer_invited = customer_entity.entity_id',array('email'));
		$collection->getSelect()->join(
						array('mw_affiliate_program'=>$program_table),'main_table.program_id = mw_affiliate_program.program_id',array('program_name'));
      	$this->setCollection($collection);
      	return parent::_prepareCollection();
  	}
  	
	private function _getNameProduct()
    {
    	$arr = array();
    	$collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('name');
		foreach($collection as $item){
			$arr[$item->getId()] = $item->getName();
		}
        return $arr;
    }
    
	private function _getEmailCustomerInvited()
    {
    	$arr = array();
    	$collection = Mage::getModel('customer/customer')->getCollection();
		foreach($collection as $item){
			$arr[$item->getId()] = $item->getEmail();
		}
        return $arr;
    }
    
  	protected function _prepareColumns()
  	{
        $this->addColumn('history_id', array(
            'header'    =>  Mage::helper('affiliate')->__('ID'),
            'align'     =>  'left',
            'index'     =>  'history_id',
            'width'     =>  10
        ));
        
      	$this->addColumn('transaction_time', array(
            'header'    =>  Mage::helper('affiliate')->__('Transaction Time'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'transaction_time',
            'gmtoffset' => true,
            'default'   =>  ' ---- '
        ));
	  
		$this->addColumn('program_name', array(
     		'header'    => Mage::helper('affiliate')->__('Program Name'),
    	    'align'     =>'left',
   	       	'index'     => 'program_name',
			'type'      => 'text',
   	   	));
   	   	$this->addColumn('product_id', array(
     		'header'    => Mage::helper('affiliate')->__('Product Name'),
    	    'align'     =>'left',
   	       	'index'     => 'product_id',
			'type'      => 'text',
            'renderer'  => 'affiliate/adminhtml_renderer_productname',
   	   	));
   	   	
//		$this->addColumn('customer_invited', array(
//          	'header'    => Mage::helper('affiliate')->__('Affiliate Email'),
//          	'align'     =>'left',
//          	'index'     => 'customer_invited',
//		  	'width'     => '250px',
//		  	'type'      => 'options',
//          	'options'   => $this->_getEmailCustomerInvited(),
//      	));
      	$this->addColumn('email', array(
          	'header'    => Mage::helper('affiliate')->__('Affiliate Account'),
          	'align'     =>'left',
          	'index'     => 'email',
		  	'width'     => '250px',
		  	'type'      => 'text',
      		'renderer'  => 'affiliate/adminhtml_renderer_emailaffiliatemember',
      	));
        $this->addColumn('order_id', array(
            'header'    =>  Mage::helper('affiliate')->__('Order Number'),
            'align'     =>  'left',
        	'width'		=>  100,
            'index'     =>  'order_id',
        	'type'      => 'text',
     		'renderer'  => 'affiliate/adminhtml_renderer_orderid',
        ));
      	
      	$this->addColumn('total_amount', array(
          	'header'    => Mage::helper('affiliate')->__('Total Amount'),
          	'index'     => 'total_amount',
        	'type'      =>  'price',
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
      	));
      	
      	$this->addColumn('history_commission', array(
          	'header'    => Mage::helper('affiliate')->__('Commission'),
          	'index'     => 'history_commission',
      		'type'      =>  'price',
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
      	));
	  
      
		$this->addColumn('history_discount', array(
            'header'    =>  Mage::helper('affiliate')->__('Customer Discount'),
        	'align'     =>  'center',
            'index'     =>  'history_discount',
			'type'      =>  'price',
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
        ));
        
        $this->addColumn('status', array(
          	'header'    => Mage::helper('affiliate')->__('Status'),
          	'align'     =>'center',
          	'index'     => 'status',
		  	'type'      => 'options',
          	'options'   => Mage::getSingleton('affiliate/status')->getOptionArray(),
      	));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('affiliate')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('affiliate')->__('XML'));
	  
      	return parent::_prepareColumns();
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
                    	//zend_debug::dump($item->getOrderId());die();
                    }
                	else if($col_id == 'order_id')
                    {   
                    	$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getOrderId()).'"';
                    	//zend_debug::dump($item->getOrderId());die();
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