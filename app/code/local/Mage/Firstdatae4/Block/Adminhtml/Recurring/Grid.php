<?php

class Mage_Firstdatae4_Block_Adminhtml_Recurring_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('recurringGrid');
        $this->setDefaultSort('recurring_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('firstdatae4/recurring')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('recurring_id', array(
            'header'    => Mage::helper('firstdatae4')->__('Recurring Id'),
            'align'     =>'right',
            'width'     => '5px',
            'index'     => 'recurring_id',
        ));

        $this->addColumn('customer_id', array(
            'header'     => Mage::helper('firstdatae4')->__('Customer Name'),
            'align'      =>'left',
            'index'      => 'customer_id',
            'renderer'  => 'firstdatae4/adminhtml_renderer_customername',
            'width'      => '150px'
        ));
        
        $this->addColumn('email', array(
            'header'     => Mage::helper('firstdatae4')->__('Email'),
            'align'      =>'left',
            'index'      => 'customer_id',
            'renderer'  => 'firstdatae4/adminhtml_renderer_customeremail',
            'width'      => '150px'
        ));
        
        $this->addColumn('type', array(
            'header'     => Mage::helper('firstdatae4')->__('Recurring Type'),
            'align'      =>'left',
            'index'      => 'type',
            'width'      => '20px',
            'type'      => 'options',
            'options'    => Mage::getModel('firstdatae4/plans')->toOptionArray()
        ));
        
        $this->addColumn('status', array(
            'header'     => Mage::helper('firstdatae4')->__('Status'),
            'align'      =>'left',
            'index'      => 'status',
            'width'      => '20px',
            'type'      => 'options',
            'options'    => Mage::getModel('firstdatae4/status')->toOptionArray()
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('firstdatae4')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('firstdatae4')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('recurring_id');
        $this->getMassactionBlock()->setFormFieldName('recurring');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('firstdatae4')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('firstdatae4')->__('Are you sure?')
        ));

//        $statuses = Mage::getSingleton('firstdatae4/status')->getOptionArray();
//
//        array_unshift($statuses, array('label'=>'', 'value'=>''));
//        $this->getMassactionBlock()->addItem('status', array(
//             'label'=> Mage::helper('firstdatae4')->__('Change status'),
//             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
//             'additional' => array(
//                    'visibility' => array(
//                         'name' => 'status',
//                         'type' => 'select',
//                         'class' => 'required-entry',
//                         'label' => Mage::helper('firstdatae4')->__('Status'),
//                         'values' => $statuses
//                     )
//             )
//        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}