<?php
class MW_Affiliate_Block_Adminhtml_Affiliatebinaryreportconstructor_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('affiliatebinaryreport_constructor');
        $this->setSaveParametersInSession(true);
        $this->setEmptyText(Mage::helper('affiliate')->__('No customers available'));
    }

    protected function _prepareCollection()
    {   
        $preferred = Mage::helper('affiliate_binary')->getPreferredCustomers();
        
      	$collection = Mage::getModel('affiliate/affiliatecustomers')
            ->getCollection()
            ->addFieldToFilter('customer_id', array('nin' => array($preferred)))
            ->addFieldToFilter('anetwork', array('in' => array(2,3)));
                
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
        
        $this->addColumn('referral_code', array(
            'header'    => Mage::helper('affiliate')->__('Referral Code'),
            'align'     =>'left',
            'index'     => 'referral_code',
            'width'     => '250px',
     	));
        
        $this->addColumn('affiliate_email', array(
            'header'    => Mage::helper('affiliate')->__('Email'),
            'align'     =>'left',
            'index'     => 'customer_id',
            'width'     => '200px',
            'renderer'  => 'affiliate/adminhtml_renderer_emailreferral',
            'filter_condition_callback' => array($this, '_filterReferralemailCondition'),
     	));
        
       
        
        
//        $this->addColumn('action',
//            array(
//                'header'    =>  Mage::helper('affiliate')->__('Action'),
//                'width'     => '100',
//                'type'      => 'action',
//                'getter'    => 'getId',
//                'actions'   => array(
//                    array(
//                        'caption'   => Mage::helper('affiliate')->__('Run Report'),
//                        'url'       => array('base'=> '*/*/run'),
//                        'field'     => 'customer_id'
//                    )
//                ),
//                'filter'    => false,
//                'sortable'  => false,
//                'is_system' => true,
//        ));
//        
        $this->addColumn('group_name', array(
            'header'    => Mage::helper('affiliate')->__('Group Name'),
            'align'     =>'left',
            'index'     => 'customer_id',
            'filter'    => false,
            'renderer'  => 'affiliate/adminhtml_renderer_affiliategroup',
     	));
        
        $this->addColumn('affiliate_report', array(
            'header'    => '',
            'align'     =>'left',
            'index'     => 'customer_id',
            'width'     => '200px',
            'filter'    => false,
            'renderer'  => 'affiliate/adminhtml_renderer_residualreport',
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
        $this->getMassactionBlock()->setFormFieldName('affiliatebinaryreport_residual');
        
        $this->getMassactionBlock()->addItem('run_report', array(
            'label'=> Mage::helper('affiliate')->__('Run Report'),
            'url'  => $this->getUrl('*/*/massRunReportResidual', array('_current'=>true)),
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