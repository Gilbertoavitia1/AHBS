<?php
class MW_Affiliate_Block_Adminhtml_Affiliatewithdrawn_Grid extends Mage_Adminhtml_Block_Widget_Grid
{ 
	public function __construct()
    {
        parent::__construct();
        $this->setId('affiliate_withdrawn');
        //$this->setDefaultSort('withdraw_time');
        //$this->setDefaultDir('desc');

        //$this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setEmptyText(Mage::helper('affiliate')->__('No Withdrawal History'));
    }
	 protected function _prepareCollection()
    {   
    	//$customer = Mage::getModel('customer/customer')->getCollection();
    	$resource = Mage::getModel('core/resource');
  	  	$table_customer = $resource->getTableName('customer/entity');
  	  	
    	$affiliate_customer = Mage::getModel('affiliate/affiliatebanner')->getCollection();
  		$customer_table = $affiliate_customer->getTable('affiliatecustomers');
        $collection = Mage::getResourceModel('affiliate/affiliatewithdrawn_collection')
						->setOrder('withdrawn_time', 'DESC')
						->setOrder('withdrawn_id', 'DESC');
		$collection->getSelect()->join(
      							array('customer_entity'=>$table_customer),'main_table.customer_id = customer_entity.entity_id',array('email'));				
       /* $collection->getSelect()->join(
        	array('mw_affiliate_customers'=>$customer_table),'main_table.customer_id = mw_affiliate_customers.customer_id',
        	array('mw_affiliate_customers.payment_email','mw_affiliate_customers.payment_gateway'));*/
        	
        //echo $collection->getSelect();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {   
    	//$a=$this->$row->getId();
    	//$credit = Mage::helper('credit')->getCreditByCustomer(); 
        $this->addColumn('withdrawn_id', array(
            'header'    =>  Mage::helper('affiliate')->__('ID'),
            'align'     =>  'left',
            'index'     =>  'withdrawn_id',
        	'name'		=>  'withdrawn_id',
            'width'     =>  15
        ));
		$this->addColumn('email', array(
          	'header'    => Mage::helper('affiliate')->__('Affiliate Account'),
          	'align'     =>'left',
          	'index'     => 'email',
		  	'width'     => '250px',
		  	'type'      => 'text',
      		'renderer'  => 'affiliate/adminhtml_renderer_emailaffiliatemember',
      	));
        $this->addColumn('payment_gateway', array(
            'header'    =>  Mage::helper('affiliate')->__('Payment Method'),
        	'align'     =>  'left',
            'index'     =>  'payment_gateway',
        	'type'      => 'options',
            'options'   => $this ->_getPaymentGatewayArray(),
            'filter' => false,
	        'sortable'  => false
        	//'options'   => MW_Affiliate_Model_Gateway::getOptionArray()
        ));
        $this->addColumn('payment_email', array(
            'header'    =>  Mage::helper('affiliate')->__('Payment Email'),
        	'align'     =>  'left',
            'index'     =>  'payment_email',
        	'type'      => 'text',
        ));
		
      	$this->addColumn('withdrawn_time', array(
            'header'    =>  Mage::helper('affiliate')->__('Withdrawal Time'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'withdrawn_time',
            //'gmtoffset' => true,
        ));
        $this->addColumn('withdrawn_amount', array(
            'header'    =>  Mage::helper('affiliate')->__('Withdrawal Amount'),
        	'align'     =>  'left',
            'type'      =>  'price',
            'index'     =>  'withdrawn_amount',
        	'currency_code' => Mage::app()->getBaseCurrencyCode(),
        ));
        $this->addColumn('fee', array(
            'header'    =>  Mage::helper('affiliate')->__('Payment Processing Fee'),
        	'align'     =>  'left',
            'type'      =>  'price',
            'index'     =>  'fee',
        	'currency_code' => Mage::app()->getBaseCurrencyCode(),
        ));
        $this->addColumn('amount_receive', array(
            'header'    =>  Mage::helper('affiliate')->__('Net Amount'),
        	'align'     =>  'center',
            'type'      =>  'price',
            'index'     =>  'amount_receive',
        	'currency_code' => Mage::app()->getBaseCurrencyCode(),
        ));
         $this->addColumn('status', array(
            'header'    =>  Mage::helper('affiliate')->__('Status'),
            'align'     =>  'center',
            'index'     =>  'status',
         	'type'      => 'options',
          	'options'   => MW_Affiliate_Model_Status::getOptionArray(),
         	'width'     =>  100
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('affiliate')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('affiliate')->__('XML'));
         return parent::_prepareColumns();
    }
  private function _getPaymentGatewayArray()
    {
    	$arr = array();
    	$gateways = unserialize(Mage::helper('affiliate/data')->getGatewayStore());
		foreach ($gateways as $gateway) 
		{
			$arr[$gateway['gateway_value']] =  $gateway['gateway_title'];
		}
		return $arr;
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
		$headerItems = array(
			0 => Mage::helper('affiliate')->__('"ID"'),
			1 => Mage::helper('affiliate')->__('"Affiliate Account"'),
			2 => Mage::helper('affiliate')->__('"Payment Method"'),
			3 => Mage::helper('affiliate')->__('"Payment Email"'),
			4 => Mage::helper('affiliate')->__('"Bank Name"'),
			5 => Mage::helper('affiliate')->__('"Name on Account"'),
			6 => Mage::helper('affiliate')->__('"Account Number"'),
			7 => Mage::helper('affiliate')->__('"Bank Country"'),
			8 => Mage::helper('affiliate')->__('"SWIFT code"'),
			9 => Mage::helper('affiliate')->__('"Withdrawal Time"'),
			10 => Mage::helper('affiliate')->__('"Withdrawal Amount"'),
			11 => Mage::helper('affiliate')->__('"Payment Processing Fee"'),
			12 => Mage::helper('affiliate')->__('"Net Amount"'),
			13 => Mage::helper('affiliate')->__('"Status"'),
			
		);
		$data = $headerItems;
		//zend_debug::dump($data);die();
        $csv.= implode(',', $data)."\n";

        foreach ($this->getCollection() as $item) {
			//zend_debug::dump($item);die();
            $data = array();
            $data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getWithdrawnId()).'"'; 
			$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getEmail()).'"';
			//zend_debug::dump($item->getOrderId());die();
			$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getPaymentGateway()).'"';
			$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getPaymentEmail()).'"';
			$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getBankName()).'"';
			$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getNameAccount()).'"';
			$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getAccountNumber()).'"';
			$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getBankCountry()).'"';
			$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getSwiftBic()).'"';
			$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getWithdrawnTime()).'"';
			$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getWithdrawnAmount()).'"';
			$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getFee()).'"';
			$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $item->getAmountReceive()).'"';
			
			
			switch ($item->getStatus()) {
			case 1:
				$getStatus = Mage::helper('affiliate')->__('Pending');
				break;
			case 2:
				$getStatus = Mage::helper('affiliate')->__('Complete');
				break;
			case 3:
				$getStatus = Mage::helper('affiliate')->__('Canceled');
				break;
			case 4:
				$getStatus = Mage::helper('affiliate')->__('Closed');
				break;
			case 6:
				$getStatus = Mage::helper('affiliate')->__('Holding');
				break;
			}
			$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $getStatus).'"';
			
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