<?php
class MW_Affiliate_Model_System_Config_Source_Autoapproveproducts
{
    public function toOptionArray()
    {
        $products = Mage::getModel('catalog/product')->getCollection();
        $methods = array();
        foreach($products as $prod) {
            $product = Mage::getModel('catalog/product')->load($prod->getId());
             array_push($methods, array('value' => $prod->getId(), 'label' => $product->getName()));
        }

        return $methods;
    }
}
