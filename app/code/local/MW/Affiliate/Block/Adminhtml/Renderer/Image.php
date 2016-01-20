<?php
class MW_Affiliate_Block_Adminhtml_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if (empty($row['image_name'])) {
    		return '';
    	}
		    	
    	$bannerExtension = substr($row['image_name'], strrpos($row['image_name'], '.')+1);
    	if($bannerExtension == 'swf') {
    		return  '<object type="application/x-shockwave-flash" data="' . Mage::getBaseUrl('media').$row['image_name'] . '" width="60" height="60">'
    				. '<param name="wmode" value="transparent" />'
    				. '<param name="movie" value="' . Mage::getBaseUrl('media').$row['image_name'] . '" />'
    				. '</object>';
    	} else {
    		return '<img src="'.Mage::helper('affiliate/image')->init($row['image_name'])->resize(60,60). '" />'; 
    	}
    }

}