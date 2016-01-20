<?php
class Sparx_Storelocator_Block_Storelocator extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getStorelocator()     
     { 
        if (!$this->hasData('storelocator')) {
            $this->setData('storelocator', Mage::registry('storelocator'));
        }
        return $this->getData('storelocator');
        
    }
 public function getJsandCss(){
	
	 $html.= '<link rel="stylesheet" type="text/css" href="'.$this->getSkinUrl('storelocator/css/map.css').'" media="all" />';
	if(Mage::getStoreConfig('storelocator/settings/jquery')){ 
		$html.= '<script type="text/javascript" src="'.$this->getSkinUrl('storelocator/js/jquery-1.10.1.min.js').'"></script>';
		
	}		
	$html.= '<script type="text/javascript" src="'.$this->getSkinUrl('storelocator/js/noConflict.js').'"></script>';
	$html.= '<script type="text/javascript" src="'.$this->getSkinUrl('storelocator/js/handlebars-1.0.0.min.js').'"></script>';
	$html.= '<script type="text/javascript" src="'.$this->getSkinUrl('storelocator/js/jquery.storelocator.js').'"></script>';
	
	return $html;
	
	}
}
