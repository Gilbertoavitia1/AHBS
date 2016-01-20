<?php
class MW_Affiliate_Block_Likebox_Render extends Mage_Core_Block_Template {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function _prepareLayout() {
		parent::_prepareLayout();
	}
	public function getCustomer()
	{
		$params = $this->getRequest()->getParams();
		return Mage::getModel('customer/customer')->load($params['customer_id']);
	}
	public function getIframeInfo() {
		$params = $this->getRequest()->getParams();
		
		$data = array();
		$data['likebox_title'] = (strlen($params['likebox_title']) > 0) 
							     ? $params['likebox_title'] 
								 : Mage::getStoreConfig('affiliate/likebox/likebox_title');
		
		$data['likebox_title_color'] = ($params['likebox_colorscheme'] > 0) ? '#0A263C' : '#FFFFFF';
		
		return $data;
	}
	
	public function getIframeProducts()
	{
		$params = $this->getRequest()->getParams();
		$products = Mage::getModel('catalog/product')->getCollection();
		
		$qty = ($params['likebox_product_qty']) ? ($params['likebox_product_qty']) : 0;
		$selectMethod = $params['product_select_method']; 
		if($selectMethod == 0) 
		{ 
			return $this->getBestsellerProducts($qty);
		} 
		else if($selectMethod == 1) 
		{ 
			return $this->getNewProducts($qty);
		} 
		else if($selectMethod == 2) 
		{
			return $this->getProductsByCategory($params['category_option'], $qty);
		}
		
	}
	
	public function getBestsellerProducts($qty) {
		$storeId = Mage::app()->getStore()->getStoreId();
	
		$visibility = array(
                      Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                      Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
                  );
                  
		$products = Mage::getResourceModel('reports/product_collection')
		            ->addAttributeToSelect('*')
					->addOrderedQty()
					->addAttributeToSelect(array('name', 'price', 'small_image'))
					->addAttributeToSort('ordered_qty', 'desc')
					->addAttributeToFilter('visibility', $visibility)
					->setStoreId($storeId)
					->addStoreFilter($storeId);
		$products->getSelect()->limit($qty);
		
		return $products;
	}
	
	public function getNewProducts($qty) {
		$storeId = Mage::app()->getStore()->getStoreId();
		
		$visibility = array(
                      Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                      Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
                  );
		
		$products = Mage::getModel('catalog/product')->getCollection()
		            ->addAttributeToSelect('*')
				 	->addAttributeToSelect(array('name', 'price'))
				 	->addAttributeToSort('entity_id', 'DESC')
				 	->addAttributeToFilter('visibility', $visibility)
				 	->setStoreId($storeId)
				 	->addStoreFilter($storeId);
		$products->getSelect()->limit($qty);

		return $products;
	}
	
	public function getProductsByCategory($categoryId, $qty) {
		$storeId = Mage::app()->getStore()->getStoreId();
		
		$visibility = array(
                      Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                      Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
                  );
		
		$products = Mage::getModel('catalog/product')->getCollection()
		            ->addAttributeToSelect('*')
					->setStoreId($storeId)
					->addStoreFilter($storeId)
					->addCategoryFilter(Mage::getModel('catalog/category')->load($categoryId))
					->addAttributeToSelect(array('name', 'price'))
					->addAttributeToFilter('visibility', $visibility)
					->addAttributeToSort('entity_id', 'DESC');
		$products->getSelect()->limit($qty);
		
		return $products;
	}
	
}