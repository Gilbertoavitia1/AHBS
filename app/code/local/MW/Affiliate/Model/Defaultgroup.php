<?php

class MW_Affiliate_Model_Defaultgroup extends Varien_Object
{
	public function toOptionArray()
    {
        $default_groups = Mage::getModel('affiliate/affiliategroup')->getCollection();
        $options = array();
        $options[] = array(
               'value' => '',
               'label' => Mage::helper('adminhtml')->__('-- Please Select --')
            );
        foreach ($default_groups as $default_group) {
        	
        	$group_id = $default_group->getGroupId();
        	$group_name = $default_group->getGroupName();
        	
            $options[] = array(
               'value' => $group_id,
               'label' => $group_name
            );
        }
        return $options;
    }
}