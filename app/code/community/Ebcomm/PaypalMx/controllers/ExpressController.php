<?php

/**
 * Express Checkout Controller
 */
class Ebcomm_PaypalMx_ExpressController extends Ebcomm_PaypalMx_Controller_Express_Abstract
{
    /**
     * Config mode type
     *
     * @var string
     */
    protected $_configType = 'paypalmx/config';

    /**
     * Config method type
     *
     * @var string
     */
    protected $_configMethod = Ebcomm_PaypalMx_Model_Config::METHOD_PPMX;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = 'paypalmx/express_checkout';
}
