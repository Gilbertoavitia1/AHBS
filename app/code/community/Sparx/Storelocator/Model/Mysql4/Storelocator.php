<?php

class Sparx_Storelocator_Model_Mysql4_Storelocator extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the storelocator_id refers to the key field in your database table.
        $this->_init('storelocator/storelocator', 'storelocator_id');
    }

    /**
     * 
     * @param type $zipcode
     * @return type store collection 
     */
     public function getSearchResult($zipcode){
		$where = "status=1 AND ((zipcode like '%".$zipcode."%') OR (city like '%".$zipcode."%') OR (name like '%".$zipcode."%') OR (state like '%".$zipcode."%') OR (phone like '%".$zipcode."%') OR (country like '%".$zipcode."%'))";
		$sql = $this->_getReadAdapter()->select()->from($this->getMainTable())->where($where); 
		return $this->_getReadAdapter()->fetchAll($sql);		
	}
   }