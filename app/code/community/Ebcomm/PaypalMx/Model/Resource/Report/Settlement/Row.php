<?php
class Ebcomm_PaypalMx_Model_Resource_Report_Settlement_Row extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource model initialization.
     * Set main entity table name and primary key field name.
     */
    protected function _construct()
    {
        $this->_init('paypal/settlement_report_row', 'row_id');
    }
}
