<?php
class MW_Affiliate_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
     public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
 
    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_grid_collection';
    }
 
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());$this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
//            'filter_condition_callback' => array($this, '_filterHasUrlConditionCallback')
        ));
        
        $this->addColumn('referral_code', array(
            'header'=> Mage::helper('affiliate')->__('Referral Code'),
            'width' => '80px',
            'type'  => 'text',            
            'renderer'  => 'affiliate/adminhtml_renderer_getreferral',
            'filter_condition_callback' => array($this, '_filterReferralCodeCondition')
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
//                'filter_condition_callback' => array($this, '_filterStoreHasUrlConditionCallback')
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
//            'filter_condition_callback' => array($this, '_filterCreatedHasUrlConditionCallback')
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('affiliate_network', array(
            'header' => Mage::helper('sales')->__('Affiliate Network'),
            'index' => 'affiliate_network',
            'type'  => 'options',
            'width' => '70px',
            'options' => array(
                    1 => "Uninivel",
                    2 => "Binario"
            ),
        ));
        
        $this->addColumn('affiliate_sale_type', array(
            'header' => Mage::helper('sales')->__('Affiliate Sale Type'),
            'index' => 'affiliate_sale_type',
            'type'  => 'options',
            'width' => '70px',
            'options' => array(
                    0 => "No se toma en cuenta",
                    1 => "Recompra",
                    2 => "Paquete",
                    3 => "Constructor",
                    4 => "Recompra y Paquete",
                    5 => "Recompra y Constructor",
                    6 => "Paquete y Constructor",
                    7 => "Paquete, Constructor y Recompra"
            ),
        ));
        
        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/sales_order/view'),
                            'field'   => 'order_id',
                            'data-column' => 'action',
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ));
        }
        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }
    
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
            $this->getMassactionBlock()->addItem('cancel_order', array(
                 'label'=> Mage::helper('sales')->__('Cancel'),
                 'url'  => $this->getUrl('*/sales_order/massCancel'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/hold')) {
            $this->getMassactionBlock()->addItem('hold_order', array(
                 'label'=> Mage::helper('sales')->__('Hold'),
                 'url'  => $this->getUrl('*/sales_order/massHold'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/unhold')) {
            $this->getMassactionBlock()->addItem('unhold_order', array(
                 'label'=> Mage::helper('sales')->__('Unhold'),
                 'url'  => $this->getUrl('*/sales_order/massUnhold'),
            ));
        }

        $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
             'label'=> Mage::helper('sales')->__('Print Invoices'),
             'url'  => $this->getUrl('*/sales_order/pdfinvoices'),
        ));

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
             'label'=> Mage::helper('sales')->__('Print Packingslips'),
             'url'  => $this->getUrl('*/sales_order/pdfshipments'),
        ));

        $this->getMassactionBlock()->addItem('pdfcreditmemos_order', array(
             'label'=> Mage::helper('sales')->__('Print Credit Memos'),
             'url'  => $this->getUrl('*/sales_order/pdfcreditmemos'),
        ));

        $this->getMassactionBlock()->addItem('pdfdocs_order', array(
             'label'=> Mage::helper('sales')->__('Print All'),
             'url'  => $this->getUrl('*/sales_order/pdfdocs'),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
             'label'=> Mage::helper('sales')->__('Print Shipping Labels'),
             'url'  => $this->getUrl('*/sales_order_shipment/massPrintShippingLabel'),
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }

    protected function _filterHasUrlConditionCallback($collection, $column)
    {
        if(!$value = $column->getFilter()->getValue()) {
  			return;
  		}
  		$this->getCollection()->getSelect()->where("main_table.increment_id like '%".$value."%'");
    }
    
    protected function _filterReferralCodeCondition($collection, $column){
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        
        $affiliates = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
            ->addFieldToSelect('customer_id')
            ->addFieldToFilter('referral_code', $value);
        
        $this->getCollection()->getSelect()->where("main_table.customer_id in (?)",$affiliates->getData('customer_id'));
    }
    
    protected function _filterStoreHasUrlConditionCallback($collection, $column)
    {
        if(!$value = $column->getFilter()->getValue()) {
  			return;
  		}
                
        $this->getCollection()->getSelect()->where("main_table.store_id ='".$value."'");
    }
    
    protected function _filterCreatedHasUrlConditionCallback($collection, $column)
    {
        if(!$value = $column->getFilter()->getValue()) {
  			return;
  		}
                
        $this->getCollection()->getSelect()->where("main_table.between ".$value);
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}