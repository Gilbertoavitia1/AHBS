<?php
class Mage_Firstdatae4_Model_Status extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('firstdatae4/status');
    }
    
    public function toOptionArray()
    {
        return array(
            'CREATED'       => Mage::helper('core')->__('Created'),
            'PROCESSING'    => Mage::helper('core')->__('Processing'),
            'RUNNING'       => Mage::helper('core')->__('Running'),
            'PAUSED'        => Mage::helper('core')->__('Paused'),
            'DELETED'        => Mage::helper('core')->__('Deleted')
        );
    }
}