<?php
class MW_Affiliate_Model_Products
{
    public function toOptionArray()
    {
        if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore()))
        {
            $store_id = Mage::getModel('core/store')->load($code)->getId();
        }
        elseif (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite()))
        {
            $website_id = Mage::getModel('core/website')->load($code)->getId();
            $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
        }
        else
        {
            $store_id = 0;
        }

        $array = array();
        $product = Mage::getResourceModel('catalog/product_collection')
            ->addStoreFilter($store_id)
            ->addAttributeToSelect('*');
        
        foreach ($product as $key => $value) {
            $tmp = array(
                'value' => $value->getSku(),
                'label' => $value->getSku(). ' | '.$value->getName(),
            );
            
            array_push($array, $tmp);

        }
        
        return $array;
    }

}
