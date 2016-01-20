<?php
class MW_Affiliate_Block_Likebox extends Mage_Core_Block_Template
{
	public function __construct()
	{
		parent::__construct();
	}
	       
    public function getCategoryOptionArray() {
    	$categories = Mage::getModel('catalog/category')->getCollection();
    	$categories->addAttributeToSelect('name');
    
    	$options = array();
    	foreach($categories as $category) {
    		$options[$category->getId()] = $category->getName();
    	}
    	return $options;
    }
	
}