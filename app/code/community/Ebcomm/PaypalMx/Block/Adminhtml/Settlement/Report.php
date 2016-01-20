<?php
class Ebcomm_PaypalMx_Block_Adminhtml_Settlement_Report extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Prepare grid container, add additional buttons
     */
    public function __construct()
    {
        $this->_blockGroup = 'paypalmx';
        $this->_controller = 'adminhtml_settlement_report';
        $this->_headerText = Mage::helper('paypalmx')->__('Reportes de Solución PayPal México');
        parent::__construct();
        $this->_removeButton('add');
        $message = Mage::helper('paypalmx')->__('Conectando al servidor de PayPal para buscar nuevos reportes. ¿Estas seguro que deseas contuniar?');
        $this->_addButton('fetch', array(
            'label'   => Mage::helper('paypalmx')->__('Buscar Actualizaciones'),
            'onclick' => "confirmSetLocation('{$message}', '{$this->getUrl('*/*/fetch')}')",
            'class'   => 'task'
        ));
    }
}
