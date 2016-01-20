<?php
class MW_Affiliate_Model_Category
{
    public function toOptionArray()
    {
        $array  = array();
        $category = Mage::getModel('customer/group')->getCollection()->getData();
        foreach ($category as $key => $value) {
            array_push($array, array('value'=> $value['customer_group_id'], 'label'=> $value['customer_group_code']));
        }
        
        return $array;
    }

}
?>