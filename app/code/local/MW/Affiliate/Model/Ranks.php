<?php
class MW_Affiliate_Model_Ranks
{
    public function _construct()
    {
        $this->_init('affiliate/ranks');
    }
    
    public function toOptionArray(){
        $ranks = Mage::getModel('affiliate/affiliategroup')->getCollection();
        
        $array = array();
        foreach ($ranks->getData() as $key => $value) {
            $array[$value['group_id']] = $value['group_name'];
        }
        
        return $array;
    }
}
