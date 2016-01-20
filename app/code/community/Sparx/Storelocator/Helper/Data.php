<?php

class Sparx_Storelocator_Helper_Data extends Mage_Core_Helper_Abstract {

   const storezoomLevel = 6;
    /*
     * Check config value for Enable 
     */
    public function checkEnableSetting() {
        return Mage::getStoreConfig('storelocator/settings/active');
    }
    
    /*
     *@param $country_id 
     * @return Country name by Id 
     */
    public function getCountryNameById($country_id) {
       $countryName = Mage::getModel('directory/country')->load($country_id)->getName();
       return $countryName;
    }
    
    /**
     *@return Array of all countries name with id
     */
    public function getCountrieslist() {
        $countries = array();

        $countrycoll = Mage::getResourceModel('directory/country_collection')->loadByStore();

        if (count($countrycoll)) {
            foreach ($countrycoll as $country) {
                $countries[$country->getId()] = $country->getName();
            }
        }

        return $countries;
    }

    /**
     * @ return Country List as Options
     */
    public function getCountryOptions() {
        $countryarr = array();
        $cntrycoll = Mage::getResourceModel('directory/country_collection')->loadByStore();
        if (count($cntrycoll)) {
            foreach ($cntrycoll as $cntry) {
                $countryarr[] = array('value' => $cntry->getId(), 'label' => $cntry->getName());
            }
        }

        return $countryarr;
    }

    /**
     *@return Store Image File Path    
     */
    public function getImagepath() {
        $path = Mage::getBaseUrl('media') . "storelocator/images/";
        return $path;
    }

    /**
     * @return storelocator page Url   
     */
    public function getStorelocatorUrl() {
        return $this->_getUrl('storelocator/index');
    }
    
    /**
     * @return storelocator page Url   
     */
    public function getDetailpageUrl($id=null) {
        
        return $this->_getUrl('storelocator/index/detail', array('id'=>$id));
    }
    
    /**
     * @param $id
     * @return store Description by loading store   
     */
    public function getStoreDescription($id) {
       $description = '';
       $storeData = Mage::getModel('storelocator/storelocator')->load($id);
       if($storeData && $storeData->getId()){
        $description = $storeData->getDescription();
       }
        return $description;
    }

}
