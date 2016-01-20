<?php
class MW_Affiliate_Block_Adminhtml_Affiliatecredithistory_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  	public function __construct()
  	{
      	parent::__construct();
      	$this->setId('affiliatecreditGrid');
      	$this->setSaveParametersInSession(true);
      	$this->setEmptyText(Mage::helper('affiliate')->__('No Transaction History Found'));
  	}
  	
	private function _getTransactionDetail()
    {
    	$arr = array();
    	$collection = Mage::getModel('credit/credithistory')->getCollection();
				
		foreach($collection as $credithistory){
			$transactionDetail = MW_Credit_Model_Transactiontype::getTransactionDetail($credithistory->getTypeTransaction(),$credithistory->getTransactionDetail(),true); 
			$arr[$credithistory->getId()] = $transactionDetail;
		} 
		return $arr;
    }
    
  	protected function _prepareCollection()
  	{
  		$resource = Mage::getModel('core/resource');
  		$customer_table = $resource->getTableName('customer/entity');
      	$collection = Mage::getModel('credit/credithistory')
      				  ->getCollection()
      				  ->addFieldToFilter('status', array('neq' => MW_Credit_Model_Orderstatus::HOLDING))
      				  ->setOrder('created_time', 'DESC')
					  ->setOrder('credit_history_id', 'DESC');
		$collection->getSelect()->join(
					array('customer_entity'=>$customer_table),'main_table.customer_id = customer_entity.entity_id',array('email'));
		
      	$this->setCollection($collection);
      	return parent::_prepareCollection();
  	}

  	protected function _prepareColumns()
  	{
        $this->addColumn('credit_history_id', array(
            'header'    => Mage::helper('credit')->__('ID'),
            'align'     => 'left',
            'index'     => 'credit_history_id',
            'width'     => 10
        ));
        
      	$this->addColumn('created_time', array(
            'header'    => Mage::helper('credit')->__('Transaction Time'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'created_time',
            'gmtoffset' => true,
            'default'   => ' ---- '
        ));
	  
		$this->addColumn('email', array(
     		'header'    => Mage::helper('credit')->__('Affiliate Account'),
    	    'align'     => 'left',
   	       	'index'     => 'email',
		    'renderer'  => 'affiliate/adminhtml_renderer_emailaffiliatemember',
   	   	));
   	   	
		$this->addColumn('type_transaction', array(
          	'header'    => Mage::helper('credit')->__('Type of Transaction'),
          	'align'     => 'left',
          	'index'     => 'type_transaction',
		  	'width'     => '250px',
		  	'type'      => 'options',
          	'options'   => MW_Credit_Model_Transactiontype::getOptionArray(),
      	));
      	
        $this->addColumn('transaction_detail', array(
            'header'    => Mage::helper('credit')->__('Transaction Detail'),
            'align'     => 'left',
        	'width'		=> 400,
            'index'     => 'credit_history_id',
        	'renderer'  => 'affiliate/adminhtml_renderer_credittransaction',
        ));
        
      	$this->addColumn('amount', array(
            'header'    => Mage::helper('credit')->__('Amount'),
        	'align'     => 'center',
            'index'     => 'amount',
			'type'      => 'price',
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
        ));
      	
      	$this->addColumn('end_transaction', array(
          	'header'    => Mage::helper('credit')->__('Affiliate Balance'),
          	'index'     => 'end_transaction',
      		'type'      => 'price',
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
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
                    }
                	else if($col_id == 'transaction_detail')
                    {   
                    	$transactionDetail = MW_Credit_Model_Typecsv::getTransactionDetail($item->getTypeTransaction(),$item->getTransactionDetail()); 
                    	$data[] = '"'.str_replace(array('"', '\\'), array('""', '\\\\'), $transactionDetail).'"';
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