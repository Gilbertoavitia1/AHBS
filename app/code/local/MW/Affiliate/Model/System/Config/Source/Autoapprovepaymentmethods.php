<?php
class MW_Affiliate_Model_System_Config_Source_Autoapprovepaymentmethods
{
    public function toOptionArray()
    {        
        $groups = Mage::getSingleton('payment/config')->getActiveMethods();        
        $methods = array(); 
        foreach ($groups as $paymentCode=>$paymentModel) {
            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');           
            array_push($methods, array('value' => $paymentCode, 'label' => $paymentTitle));
        }
       
        return $methods;
    }
}
