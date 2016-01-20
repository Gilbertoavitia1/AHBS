<?php
class MW_Affiliate_Block_Adminhtml_Affiliatebinaryreportcheck_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('affiliatebinaryreport_check');
        $this->setSaveParametersInSession(true);
        $this->setEmptyText(Mage::helper('affiliate')->__('No customers available'));
    }

    protected function _prepareCollection()
    {   
        $range = Mage::helper('affiliate_binary')->getRangeDate();
        
        $collection = Mage::getModel('affiliate/affiliatebinaryresidual')
            ->getCollection()                
            ->addFieldToFilter('start_date', array('eq'=>$range[2]))
            ->addFieldToFilter('paid', array('gt'=>0)); 
        
      	$this->setCollection($collection);
      	return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer_id', array(
            'header'    => Mage::helper('affiliate')->__('#'),
            'align'     => 'right',
            'width'     => '25px',
            'index'     => 'customer_id',
      	));
                
        $this->addColumn('affiliate_name', array(
            'header'    => Mage::helper('affiliate')->__('Name'),
            'align'     =>'left',
            'index'     => 'customer_id',
            'width'     => '250px',
            'renderer'  => 'affiliate/adminhtml_renderer_name',
            'filter_condition_callback' => array($this, '_filterReferralnameCondition'),
     	));
        
        $this->addColumn('email', array(
            'header'    => Mage::helper('affiliate')->__('Email'),
            'align'     =>'left',
            'index'     => 'customer_id',
            'width'     => '200px',
            'renderer'  => 'affiliate/adminhtml_renderer_emailreferral',
            'filter_condition_callback' => array($this, '_filterReferralemailCondition'),
     	));
        
        $this->addColumn('group_name', array(
            'header'    => Mage::helper('affiliate')->__('Group Name'),
            'align'     =>'left',
            'index'     => 'customer_id',
            'filter'    => false,
            'renderer'  => 'affiliate/adminhtml_renderer_affiliategroup',
     	));
        
        $this->addColumn('start_period', array(
          	'header'    => Mage::helper('affiliate')->__('Start Period'),
          	'align'     =>'left',
                'width'     => '25px',
          	'index'     => 'start_date',
                'type'      => 'datetime',
                'format'    => 'Y/MM/dd',
     	));
        
      	$this->addColumn('end_period', array(
          	'header'    => Mage::helper('affiliate')->__('End Period'),
          	'align'     =>'left',
                'width'     => '25px',
          	'index'     => 'end_date',
                'type'      => 'datetime',
                'format'    => 'Y/MM/dd'
     	));
        
        $this->addColumn('paid', array(
            'header'    => Mage::helper('affiliate')->__('Paid'),
            'align'     =>'left',
            'index'     => 'paid',
            'width'     => '200px',
     	));
        
        $this->addColumn('affiliate_report', array(
            'header'    => '',
            'align'     =>'left',
            'index'     => 'customer_id',
            'width'     => '200px',
            'filter'    => false,
            'renderer'  => 'affiliate/adminhtml_renderer_checkreport',
     	));
//                
//      	
//        $this->addExportType('*/*/exportbinaryresidualCsv', Mage::helper('affiliate')->__('CSV'));
//        $this->addExportType('*/*/exportbinaryresidualXml', Mage::helper('affiliate')->__('XML'));
//        
        return parent::_prepareColumns();
    }
    
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('customer_id');
        $this->getMassactionBlock()->setFormFieldName('affiliatebinaryreport_check');
        
        $this->getMassactionBlock()->addItem('run_report', array(
            'label'=> Mage::helper('affiliate')->__('Run Report'),
            'url'  => $this->getUrl('*/*/massRunReportCheck', array('_current'=>true)),
        ));
        
        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('affiliate')->__('Delete Report with this range'),
            'url'  => $this->getUrl('*/*/massDeleteReportCheck'),
            'confirm'  => Mage::helper('affiliate')->__('Are you sure?')
        ));
        
        

        
        return $this;
    }
    
    public function getRowUrl($row)
    {
        return '';//$this->getUrl('*/*/editresidual', array('id' => $row->getId()));
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