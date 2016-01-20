<?php
class MW_Affiliate_Block_Adminhtml_Affiliatehistory_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  	public function __construct()
  	{
      	parent::__construct();
      	$this->setId('affiliate_history');
      	$this->setSaveParametersInSession(true);
      	$this->setEmptyText(Mage::helper('affiliate')->__('No Commission History Found'));
  	}
  	
  	protected function _prepareCollection()
  	{
  		/*
  		$customerTable = Mage::getModel('core/resource')->getTableName('customer/entity');
  		
  		$collection = Mage::getModel('affiliate/affiliateinvitation')->getCollection()
				  	  ->addFieldToFilter('commission', array('gt' => '0'))
				  	  ->setOrder('invitation_time', 'DESC');
  		
  		$collection->getSelect()->join(
  				array('customer_entity' => $customerTable),'main_table.customer_id = customer_entity.entity_id',array('affiliate_account' => 'customer_entity.email'));
  		*/
  		$resource = Mage::getModel('core/resource');
  	  	$customer_table = $resource->getTableName('customer/entity');
      	$collection = Mage::getModel('affiliate/affiliatetransaction')->getCollection()
      					->addFieldtoFilter('customer_invited',0)
						->setOrder('transaction_time', 'DESC')
						->setOrder('history_id', 'DESC');
		$collection->getSelect()->joinLeft(
      							array('customer_entity'=>$customer_table),'main_table.show_customer_invited = customer_entity.entity_id',array('email'));
  		
  		$this->setCollection($collection);
  		return parent::_prepareCollection();
  	}

  	protected function _prepareColumns()
  	{
        $this->addColumn('history_id', array(
  			'header'    => Mage::helper('affiliate')->__('ID'),
  			'align'     => 'center',
  			'width'     => '50px',
  			'index'     => 'history_id',
  		));
  	
  		$this->addColumn('transaction_time', array(
            'header'    =>  Mage::helper('affiliate')->__('Transaction Time'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'index'     =>  'transaction_time',
      		'width'		=>  150,
            'gmtoffset' => true,
            'default'   =>  ' ---- '
        ));
  		
  		$this->addColumn('commission_type', array(
  			'header'    => Mage::helper('affiliate')->__('Commission Type'),
  			'align'     => 'left',
  			'index'     => 'commission_type',
  			'type'      => 'options',
  			'options'   => Mage::getModel('credit/transactiontype')->getAffiliateTypeArray()
  		));
  		
  		 $this->addColumn('email', array(
          'header'    => Mage::helper('affiliate')->__('Affiliate Account'),
          'align'     =>'left',
          'width'	  =>  150,
          'index'     => 'email',
      	  //'renderer'  => 'affiliate/adminhtml_renderer_showcustomerinvited',
      	));
  		
  		$this->addColumn('total_commission', array(
          	'header'    => Mage::helper('affiliate')->__('Affiliate Commission'),
          	'index'     => 'total_commission',
      		'width'		=>  '90',
      		'type'      =>  'price',
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
      	));
	  
      
		$this->addColumn('total_discount', array(
            'header'    =>  Mage::helper('affiliate')->__('Customer Discount'),
        	'align'     =>  'center',
			'width'		=>  '90',
            'index'     =>  'total_discount',
			'type'      =>  'price',
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
        ));
        
        $this->addColumn('grand_total', array(
	          'header'    => Mage::helper('affiliate')->__('Purchase Total'),
	          'align'     =>'right',
	          'index'     => 'grand_total',
	          'width'		=>  '100',
	          'type'      =>  'price',
	          'currency_code' => Mage::app()->getBaseCurrencyCode(),
	      	  'renderer'  => 'affiliate/adminhtml_renderer_purchasehistory',
	          'filter' => false,
	          'sortable'  => false,
      	));
  		
  		$this->addColumn('detail', array(
  			'header'    => Mage::helper('affiliate')->__('Detail'),
  			'align'     => 'left',
  			'renderer'	=> 'affiliate/adminhtml_renderer_Affiliatetransaction'
  		));
  		
  		$this->addColumn('status', array(
          	'header'    => Mage::helper('affiliate')->__('Status'),
          	'align'     =>'center',
        	'width'		=>  '100',
          	'index'     => 'status',
		  	'type'      => 'options',
          	'options'   => Mage::getSingleton('affiliate/status')->getOptionArray(),
      	));
  		
      	$this->addColumn('action', array(
            'header'    =>  Mage::helper('affiliate')->__('Action'),
            'width'     => '60',
      		'align'		=> 'center',	
            'type'      => 'action',
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
     		'renderer'	=> 'affiliate/adminhtml_renderer_invitationaction'
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('affiliate')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('affiliate')->__('XML'));
	  
      	return parent::_prepareColumns();
  	}
  	
	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('affiliate_history');
        $this->getMassactionBlock()->setFormFieldName('mw_history_id');

        $statuses = MW_Affiliate_Model_Status::getOptionAction();

        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('catalog')->__('Change status'),
             'url'  => $this->getUrl('affiliate/adminhtml_affiliatehistory/updateStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility'	=> array(
					                         'name' 	=> 'status',
					                         'type'	 	=> 'select',
					                         'class' 	=> 'required-entry',
					                         'label' 	=> Mage::helper('catalog')->__('Status'),
					                         'values' 	=> $statuses
                     					)
             )
        ));
        return $this;
    }

  	public function getRowUrl($row)
  	{
  		if($row->getStatus() == MW_Affiliate_Model_Statusinvitation::PURCHASE) {
			return $this->getUrl('*/adminhtml_affiliateviewhistory/', array('orderid' => $row->getOrderId()));
  		}
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
                    
                if($col_id == 'detail')
                    {   
                    	$transactionDetail = Mage::getModel('affiliate/statusinvitation')->getTransactionDetailCsv($item->getOrderId(),$item->getEmail(),$item->getStatus()); 
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