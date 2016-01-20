<?php
class MW_Affiliate_Block_Adminhtml_Affiliatemember_Add_Tab_Customer extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('customer/customer')->getCollection();
        
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
    
    protected function _prepareLayout() {
    	parent::_prepareLayout();
    	
    	return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer_id', array(
            'header'=> Mage::helper('affiliate')->__('Customer ID'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'entity_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('affiliate')->__('Store'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }
        
        $this->addColumn('customer_email', array(
        	'header'	=> Mage::helper('affiliate')->__('Email'),
        	'index'		=> 'email',
        	'type'		=> 'text'	
        ));

        $this->addColumn('created_at', array(
            'header' 	=> Mage::helper('affiliate')->__('Signup at'),
            'index' 	=> 'created_at',
            'type' 		=> 'datetime',
            'width' 	=> '100px',
        ));

        $this->addColumn('is_active', array(
            'header' 	=> Mage::helper('affiliate')->__('Active'),
            'index' 	=> 'is_active',
        	'width'		=> '20px'	
        ));

        return parent::_prepareColumns();
    }


    public function getRowUrl($row)
    {
    	return $this->getUrl('adminhtml/customer/view', array('entity_id' => $row->getId()));
    }

    public function getGridUrl()
    {
       
    }
    
    public function getPagerHtml()
    {
    	return $this->getChildHtml('pager');
    }
}