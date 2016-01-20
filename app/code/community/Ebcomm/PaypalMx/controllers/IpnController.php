<?php

class Ebcomm_PaypalMx_IpnController extends Mage_Core_Controller_Front_Action
{
    /**
     * Instantiate IPN model and pass IPN request to it
     */
    public function indexAction()
    {
        if (!$this->getRequest()->isPost()) {
            return;
        }

        try {
            $data = $this->getRequest()->getPost();
            Mage::getModel('paypalmx/ipn')->processIpnRequest($data, new Varien_Http_Adapter_Curl());
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}
