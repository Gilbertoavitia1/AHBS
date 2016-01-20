<?php
class Sparx_Storelocator_Block_Adminhtml_Region extends Mage_Core_Block_Template
{

     /**
      * @return Store Data Object by store id  
      */
      public function getStoreCollection()
	{
		$collection = null;
		$id = $this->getRequest()->getParam('id');
		if($id)
		{
		  $collection = Mage::getModel('storelocator/storelocator')->load($id);
		}
		return $collection;
	}
}
?>