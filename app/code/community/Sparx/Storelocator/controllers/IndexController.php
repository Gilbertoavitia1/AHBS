<?php
class Sparx_Storelocator_IndexController extends Mage_Core_Controller_Front_Action{
    
   
    
   protected function _initAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock("head")->setTitle($this->__("Store Locator"));
        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
        "label" => $this->__("Home Page"),
        "title" => $this->__("Home Page"),
        "link"  => Mage::getBaseUrl()
           ));

        $breadcrumbs->addCrumb("retailers", array(
        "label" => $this->__("Retailers"),
        "title" => $this->__("Retailers")
           ));
        return $this;
  }
  
  public function preDispatch()
 {
    if(Mage::helper('storelocator')->checkEnableSetting()){			
        return $this;
              				
    }else{   
         header('Location: '.Mage::getUrl());          
         exit;
    }
 }
        
   public function IndexAction() {
      
	  $this->_initAction();
          $this->renderLayout(); 
	  
    }
    public function DetailAction() {
      
      $this->_initAction();
      $this->renderLayout(); 
	  
    }
    
    /**
     * Show store information on map window location using parameters
     */
    public function infowindowdescriptionAction(){
			echo '{{#location}}
			<div class="loc-name">{{name}}</div>
			<div>{{address}}</div>
			<div>{{city}}, {{state}} {{country}} {{postal}}</div>
                        <div>{{phone}}</div>
			<div>{{hours1}}</div>
			<div>{{hours2}}</div>
			<div>{{hours3}}</div>
			<div><a href="http://{{web}}" target="_blank">{{web}}</a></div>
			{{/location}}';
	}
        
      /**
       * Show store information on stores list page using parameters 
       */
	public function locationlistdescriptionAction(){
			echo '
				{{#location}}
				<li data-markerid="{{markerid}}">				
					<div class="list-label">{{marker}}</div>
					<div class="list-details">
						<div class="list-content">
							<div class="loc-name">{{name}}</div>
							<div class="loc-addr">{{address}}</div> 
							<div class="loc-addr3">{{city}}, {{state}} {{country}} {{postal}}</div> 
							<div class="loc-phone">{{phone}}</div>
							<div class="loc-web"><a href="http://{{web}}" target="_blank">{{web}}</a></div>
							{{#if distance}}<div class="loc-dist">{{distance}} {{length}}</div>
							<div class="loc-directions"><a href="http://maps.google.com/maps?saddr={{origin}}&amp;daddr={{address}} {{city}}, {{state}} {{country}} {{postal}}" target="_blank">Directions</a></div>{{/if}}
							{{#if storeid}}<div class="loc-desc"><a href="'. Mage::helper('storelocator')->getDetailpageUrl('{{storeid}}').'">Store Details</a></div>{{/if}}
                                                            
						</div>
                                                <div class="store-locator-image">
                                                    {{#if storeimage}}<img height="110px" width="120px" src="'. Mage::helper("storelocator")->getImagepath().'{{storeimage}}'.'">{{/if}}
                                                </div>
					</div>				
				</li>
				{{/location}}';
	}
        
      
	
      /**
       * get Collection of all active Stores
       * Set store parameters values for map using Marker tag of xml
       */
	public function locationAction(){
			$storeCol = Mage::getModel('storelocator/storelocator')->getCollection()
				->addFieldToFilter('status',1);
			$xml = '<?xml version="1.0"?>
			<markers>';
                        if($storeCol->count()){
                         foreach($storeCol as $storeData){	
                               $countryname = Mage::helper('storelocator')->getCountryNameById($storeData['country']);
				$xml.='<marker storeid="'.$storeData->getId().'" name="'.$storeData->getName().'" lat="'.$storeData->getLatitude().'" lng="'.$storeData->getLongitude().'" category="'.$storeData->getCategory().'" address="'.$storeData->getAddress().'" city="'.$storeData->getCity().'" state="'.$storeData->getState().'" postal="'.$storeData->getZipcode().'" country="'.$countryname.'" phone="'.$storeData->getPhone().'" email="'.$storeData->getEmail().'" web="'.$storeData->getWebUrl().'" hours1="'.$storeData->getHours1().'" hours2="'.$storeData->getHours2().'" hours3="'.$storeData->getHours3().'" storeimage="'.$storeData->getStoreimage().'" fax="'.$storeData->getFax().'" featured="" />';
			 }   
                        }else{
                              $xml.= '<marker name="No Record Found."/>';
                        }
						
			echo $xml.='</markers>';			
	}
        
      /**
       * get Search Collection of stores 
       * Set store parameters values for map using Marker tag of xml
       */	
	public function searchlocationAction(){
		$params = $this->getRequest()->getParams();		
		$storeCol = Mage::getResourceModel('storelocator/storelocator')->getSearchResult($params['zipcode']);		
											
		if($params['zipcode'] && count($storeCol) > 0){
			$xml = '<?xml version="1.0"?>
			<markers>';			
			foreach($storeCol as $storeData){
                           $countryname = Mage::helper('storelocator')->getCountryNameById($storeData['country']);
				$xml.='<marker storeid="'.$storeData['storelocator_id'].'" name="'.$storeData['name'].'" lat="'.$storeData['latitude'].'" lng="'.$storeData['longitude'].'" category="'.$storeData['category'].'" address="'.$storeData['address'].'" city="'.$storeData['city'].'" state="'.$storeData['state'].'" postal="'.$storeData['zipcode'].'" country="'.$countryname.'" phone="'.$storeData['phone'].'" email="'.$storeData['email'].'" web="'.$storeData['web_url'].'" hours1="'.$storeData['hours1'].'" hours2="'.$storeData['hours2'].'" hours3="'.$storeData['hours3'].'" storeimage="'.$storeData['storeimage'].'" fax="'.$storeData['fax'].'" featured="" />';
			}			
			echo $xml.='</markers>';			
		}else{
			echo $xml = '<?xml version="1.0"?>
			<markers><marker name="No Record Found."/></markers>';				
		}
	}
        
        
      /**
       * Show store information on store detail page using parameters 
       */
	public function infoonstoredetailAction(){
			echo '
				{{#location}}
				<li data-markerid="{{markerid}}">
					<div class="list-details">
						<div class="list-content">
							<div class="loc-name">{{name}}</div>
                                                        <div class="detail-label">Address:</div>
							<div class="loc-addr">{{address}}</div> 
							<div class="loc-addr3">{{city}}, {{state}} {{country}} {{postal}}</div> 
                                                        <div class="detail-label">Phone:</div>
							<div class="loc-phone">{{phone}}</div>
                                                        <div class="detail-label">Url:</div>
							<div class="loc-web"><a href="http://{{web}}" target="_blank">{{web}}</a></div>
						        <div class="detail-label">Description:</div>
							<div class="loc-description {{storeid}}">'. Mage::helper('storelocator')->getStoreDescription($this->getRequest()->getParam('id')). '</div>
                                                            
						</div>
                                                <div class="store-locator-image">
                                                    {{#if storeimage}}<img height="110px" width="120px" src="'. Mage::helper("storelocator")->getImagepath().'{{storeimage}}'.'">{{/if}}
                                                </div>
					</div>
				</li>
				{{/location}}';
	}
        
      /**
       * get Store object
       * Set store parameters values for map using Marker tag of xml
       */
	public function storedetaillocationAction(){
                       $id = $this->getRequest()->getParam('id');
                       $storeData = Mage::getModel('storelocator/storelocator')->load($id);
                       if($storeData && $storeData->getId()){
                        $countryname = Mage::helper('storelocator')->getCountryNameById($storeData->getCountry());
			$xml = '<?xml version="1.0"?>
			<markers>';
			$xml .='<marker storeid="'.$storeData->getId().'" name="'.$storeData->getName().'" lat="'.$storeData->getLatitude().'" lng="'.$storeData->getLongitude().'" category="'.$storeData->getCategory().'" address="'.$storeData->getAddress().'" city="'.$storeData->getCity().'" state="'.$storeData->getState().'" postal="'.$storeData->getZipcode().'" country="'.$countryname.'" phone="'.$storeData->getPhone().'" email="'.$storeData->getEmail().'" web="'.$storeData->getWebUrl().'" hours1="'.$storeData->getHours1().'" hours2="'.$storeData->getHours2().'" hours3="'.$storeData->getHours3().'" storeimage="'.$storeData->getStoreimage().'" fax="'.$storeData->getFax().'" featured="" />';
			echo $xml.='</markers>';			
                       }else{
                         echo $xml = '<?xml version="1.0"?>
			 <markers><marker name="No Record Found."/></markers>';  
                       }
	}
}
