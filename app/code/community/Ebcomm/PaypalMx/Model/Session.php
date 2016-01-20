<?php

/**
 *
 * Paypal transaction session namespace
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ebcomm_PaypalMx_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('paypalmx');
    }
}
