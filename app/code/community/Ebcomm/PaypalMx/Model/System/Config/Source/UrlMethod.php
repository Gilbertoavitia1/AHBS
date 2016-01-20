<?php

/**
 * Source model for url method: GET/POST
 */
class Ebcomm_PaypalMx_Model_System_Config_Source_UrlMethod
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'GET', 'label' => 'GET'),
            array('value' => 'POST', 'label' => 'POST'),
        );
    }
}
