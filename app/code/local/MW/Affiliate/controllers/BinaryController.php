<?php
class MW_Affiliate_BinaryController extends Mage_Core_Controller_Front_Action
{   
    public function preDispatch()
    {
        parent::preDispatch();
    	if (!$this->getRequest()->isDispatched()) {
            return;
        }
    	if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
    		$this->_redirectUrl(Mage::helper('customer')->getAccountUrl());
        }
    }
    
    public function indexAction()
    {
        $this->loadLayout(); 
        $this->renderLayout();
    }
    
    public function newregistryAction()
    {
        if ($this->getRequest()->isPost()){
            $websiteId = Mage::app()->getWebsite()->getId();
            $store = Mage::app()->getStore();

            $customer = Mage::getModel("customer/customer");
            $customer->setWebsiteId($websiteId)
                ->setStore($store)
                ->setFirstname($this->getRequest()->getPost('first_name'))
                ->setLastname($this->getRequest()->getPost('last_name'))
                ->setEmail($this->getRequest()->getPost('email'))
                ->setPassword($this->getRequest()->getPost('password'));

            if ($this->getRequest()->getPost('tax')){
                $customer->setTaxvat($this->getRequest()->getPost('tax'));
            }

            try{            
                $customer->setGroupId(Mage::getStoreConfig('extras/cgroups/default_category',Mage::app()->getStore()));
                $customer->save();

                $invited = Mage::helper('affiliate')->getCustomerIdByReferralCode($this->getRequest()->getPost('referral_code'), Mage::getSingleton('customer/session')->getCustomer()->getId());
                $customer_id = $customer->getId();                
                $customerData = array(
                    'customer_id'       => $customer_id,
                    'active'            => 2,
                    'payment_gateway'   => 'paypal',
                    'payment_email'     => $this->getRequest()->getPost('email'),
                    'auto_withdrawn'    => 2,
                    'withdrawn_level'   => 0.00,
                    'reserve_level'     => 0.00,
                    'bank_name'         => '',
                    'name_account'      => '',
                    'bank_country'      => '',
                    'swift_bic'         => '',
                    'account_number'    => '',
                    're_account_number' => '',
                    'referral_site'     => '',
                    'total_commission'  => 0,
                    'total_paid'        => 0,
                    'referral_sponsor'  => $this->getRequest()->getPost('referral_sponsor'),
                    'referral_code'     => '',
                    'status'            => 1,
                    'invitation_type'   => 0,
                    'customer_time'     => now(),
                    'customer_invited'  => $invited,
                    'auto_approve'      => 1,
                    'anetwork'          => 2,
                );

                Mage::getModel('affiliate/affiliatecustomers')->saveCustomerAccount($customerData);
                Mage::helper('affiliate')->setReferralCode($customer_id);
                Mage::helper('affiliate')->setMemberDefaultGroupAffiliate($customer_id,$websiteId,1);

                $code = $_SESSION['bcode'];
                $side = $_SESSION['bside'];
            
                $person_id = Mage::getModel('affiliate/affiliatecustomers')->load($_SESSION['bparent']);
                $new_code = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                        ->addFieldToFilter('customer_id',$customer_id)
                        ->getFirstItem()
                        ->getData();  
                
                
                $changer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();  
                $changer = Mage::getModel('affiliate/affiliatecustomers')->load($changer_id);
                    
                if ($side == 'l'){
                    $person_id->setLeftLeg($new_code['referral_code']);                    
                    $var = "El Usuario con #{$changer->getReferralCode()} ha REGISTRADO una persona con el codigo #{$new_code['referral_code']} al lado izquierdo de #{$person_id->getReferralCode()}" ;
                    Mage::helper('affiliate_binary')->binarychangeuser($var);  
                }
                else {
                    $person_id->setRightLeg($new_code['referral_code']);                    
                    $var = "El Usuario con #{$changer->getReferralCode()} ha REGISTRADO una persona con el codigo #{$new_code['referral_code']} al lado derecho de #{$person_id->getReferralCode()}" ;
                    Mage::helper('affiliate_binary')->binarychangeuser($var);
                }

                $person_id->save();
                $this->_redirect('affiliate/binary/network/');
                return; 
            }
            catch (Exception $e) {
                Mage::getSingleton('customer/session')->addError($e->getMessage());
            }
        }else{
            $code = $_SESSION['bcode'];
            $side = $_SESSION['bside'];
            if (($code) && ($side)){
                $person_id = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                    ->addFieldToFilter('referral_code',$code)
                    ->getFirstItem()
                    ->getData();  

                $_s = ($side == 'l') ? $person_id['left_leg'] : $person_id['right_leg'];

                if ($_s){
                    Mage::getSingleton('customer/session')->addError("Lo sentimos, ya existe una persona registrada en este lado. Si no lo visualiza, contacte a su administrador");
                    Mage::register('flag', 0);            
                }else{
                    Mage::register('flag', 1);        
                    $person_name = Mage::getModel('customer/customer')->load($person_id['customer_id'])->getName();
                    
                    Mage::register('code', $code);
                    Mage::register('side', $side);
                    Mage::register('name', $person_name);
                    $_SESSION['bparent'] = $person_id['customer_id'];
                    Mage::register('parent_id', $person_id['customer_id']);
                }
            }
            else{
                $this->_redirect('affiliate/binary/network/');
                return; 
            }
        }
        

        $this->loadLayout(); 
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('core/session');  
        $this->renderLayout();
    } 
    
    public function registryAction()
    {   
        $side = $this->getRequest()->getParam('s');
        $code = $this->getRequest()->getParam('n');
        
        $_SESSION['bside'] = $side;
        $_SESSION['bcode'] = $code;
        
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).Mage::app()->getStore()->getCode()."/affiliate/binary/newregistry";
        header("Location: {$url}");die();
        return;        
    }
    
    public function networkAction(){
        $store_id = Mage::app()->getStore()->getId();
    	if(!Mage::helper('affiliate/data')->getEnabledStore($store_id)){
            $this->norouteAction();
            return;
    	}
        
    	$active = Mage::helper('affiliate')->getAffiliateActive();
    	$lock = Mage::helper('affiliate')->getAffiliateLock();
        
    	if($active > 0)
    	{
            if($lock > 0)
            {
                $this->_redirect('affiliate/index/referralaccount/');
                return;
            }
            
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $this->_initLayoutMessages('core/session');
            $this->renderLayout();
    	}
    	else if($active == 0)
    	{
            $this->_redirect('affiliate/index/createaccount/');
    	}
        
    }
    
    public function ajaxcheckbinaryAction() {
//        if($this->getRequest()->isXmlHttpRequest()) {
//            $this->_redirect(Mage::getBaseUrl());
//    	}
        
        $customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();  
        $sponsor = Mage::getModel('affiliate/affiliatecustomers')->getCollection()->
                addFieldToFilter('customer_id',$customer_id )
                ->getFirstItem();
        
    	$parent = $this->getRequest()->getParam('parent');
    	$child = $this->getRequest()->getParam('item');
    	$side = $this->getRequest()->getParam('side');        
    	$level = $this->getRequest()->getParam('level');        
        
        $hold = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
//            ->addFieldToFilter('customer_invited',$customer_id)
            ->addFieldToFilter('referral_sponsor',$sponsor['referral_code'])
            ->addFieldToFilter('anetwork',array('gteq' => 2))
            ->addFieldToFilter('referral_code', $child);
        
        if (sizeof($hold->getData())>0){      
            $check = Mage::getModel('affiliate/affiliatecustomers')->getCollection();            
            $check->getSelect()->where('left_leg = ?', $child)->orWhere('right_leg = ?', $child);
            
            if (sizeof($check->getData())>0){
                echo Zend_Json::encode(array('status' => 'error', "message" => "El Código ya esta en su red"));
            }
            else{
                $customer_info = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                    ->addFieldToFilter('referral_code', $parent)
                    ->getFirstItem()
                    ->getData('customer_id');
                
                $customer = Mage::getModel('affiliate/affiliatecustomers')->load($customer_info);
                
                $changer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();  
                $changer = Mage::getModel('affiliate/affiliatecustomers')->load($changer_id);
                 
                if ($side == 1){
                    $customer->setLeftLeg($child);
                    $var = "El Usuario con #{$changer->getReferralCode()} ha agregado una persona con el codigo #{$child} al lado izquierdo de #{$customer->getReferralCode()}";
                    Mage::helper('affiliate_binary')->binarychangeuser($var);   
                }
                else {
                    $customer->setRightLeg($child);
                    $var = "El Usuario con #{$changer->getReferralCode()} ha agregado una persona con el codigo #{$child} al lado derecho de #{$customer->getReferralCode()}";
                    Mage::helper('affiliate_binary')->binarychangeuser($var); 
                }
                
                $customer_parent = $hold->getData();
                $level = intval($level) + 1;
                $network =  Mage::helper('affiliate')->binaryNetwork($customer_parent[0]['customer_id'], '', $parent, ($level));
                
                $customer->save();
                
                echo Zend_Json::encode(array('status' => 'success', "message"=> "La persona ha sido almacenada", 'info' => $network)); 
            } 
        }
        else{
            echo Zend_Json::encode(array('status' => 'error', "message" => "Ese código no pertence a tu tanque de espera"));  
        }        
    }
    
    public function panelAction()
    {
        $customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();  
        $firstDay   = date("Y-m-d 00:00:00", strtotime("last friday"));
        $lastDay    = date("Y-m-d 23:59:59", strtotime("this thursday"));
        
        $this->_arrayResult = Mage::helper('affiliate_binary')->binaryNetwork($customer_id, '', null, 0, $firstDay, $lastDay);
        
        $recompra = Mage::getModel('affiliate/affiliateexpired')->getCollection()->addFieldToFilter('customer_id',$customer_id )
            ->getFirstItem()
            ->getData();
        
        $historial = Mage::getResourceModel('sales/order_status_history_collection')
            ->addAttributeToSelect('parent_id')
            ->addAttributeToFilter('main_table.status', array('eq'=>'complete'))
            ->addAttributeToFilter('main_table.created_at', array('from'=>$firstDay, 'to'=> $lastDay))
            ->addAttributeToFilter('main_table.entity_name', array('in'=>array('invoice','shipment')));     
        
        $cp = 0;
        
        $preferred_customer = Mage::getModel('affiliate/affiliategroupmember')
            ->getCollection()
            ->addFieldToSelect('customer_id')
            ->addFieldToFilter('group_id', array('eq'=>13))
            ->getData();
        
        $code = Mage::getModel('affiliate/affiliatecustomers')
            ->getCollection()
            ->addFieldToFilter('customer_id',$customer_id)
            ->getFirstItem()
            ->getData('referral_code');

        $preferred_sponsor = Mage::getModel('affiliate/affiliatecustomers')
            ->getCollection()
            ->addFieldToSelect('customer_id')
            ->addFieldToFilter('customer_id', array('in' => array($preferred_customer))) 
            ->addFieldToFilter('main_table.active',2)
            ->addFieldToFilter('main_table.referral_sponsor', $code);

        if ((sizeof($preferred_sponsor) > 0) && ((sizeof($historial->getData('parent_id')) > 0))){
            $sale = Mage::getModel('sales/order')->getCollection()
                ->addAttributeToFilter('customer_id', array('in' => array($preferred_sponsor->getData('customer_id')))) 
                ->addAttributeToFilter('entity_id', array('in' => array($historial->getData('parent_id'))))
                ->addAttributeToFilter('affiliate_network', array('eq' => 2)); 
            
            foreach ($sale as $order) { 
                $items = $order->getAllVisibleItems();
                array_push($t, array($order->getIncrementId()));
                foreach($items as $item){
                    $single = $item->getData(); 
                    $puntos = Mage::getModel('catalog/product')->setStoreId($single['store_id'])->load($single['product_id'])->getData('puntos');
                    $cp = $cp + ($puntos * $single['qty_ordered']);
                }
            }
        }
                
                
        Mage::register('items', $this->_arrayResult);
        Mage::register('recompra', $recompra);
        Mage::register('cp', $cp);
        
        $this->loadLayout();  
        $this->renderLayout();
    }  
    
    public function checkAction()
    {
        $this->loadLayout();  
        $this->renderLayout();
    }
    
    public function preferredAction()
    {
        $this->loadLayout();  
        $this->renderLayout();
    }  
    
    public function salesAction()
    {
        $this->loadLayout();  
        $this->renderLayout();
    }  
    
    public function constructorAction()
    {
        $this->loadLayout();  
        $this->renderLayout();
    }  
    
    public function residualAction()
    {  
        $data = $this->getRequest()->getParams();        
        if ($data){ 
            Mage::getSingleton('core/session')->setPeriodc($data['period']);
            $this->_redirect('affiliate/binary/residual');
        }
        
        $customer_id = (int)Mage::getSingleton('customer/session')->getCustomer()->getId();        
        $period = Mage::getSingleton('core/session')->getPeriodc() == "" ? "thisweek": Mage::getSingleton('core/session')->getPeriodc();     
        $collection = Mage::getModel('affiliate/affiliatebinaryresidual')
            ->getCollection()
            ->addFieldToFilter('customer_id', array('eq' => $customer_id))
            ->setOrder('id','desc')
            ->getData();
         
        $network = array();
        if ($period != "thisweek"){
            $info = Mage::getModel('affiliate/affiliatebinaryresidual')
                ->getCollection()
                ->addFieldToFilter('id', array('eq' => $period))
                ->getFirstItem()
                ->getData();
            
            $package = json_decode($info['package']);
            $info['package'] = $package;
           
            Mage::register('network', $info); 
        }
        else{            
            $firstDay = date("Y-m-d 00:00:00", strtotime("last friday")); 
            $lastDay   = date("Y-m-d 23:59:59", strtotime("this thursday")); 
            $network = Mage::helper('affiliate_binary')->binaryNetwork($customer_id, '', null, 0, $firstDay, $lastDay);  
            $item = array_pop($network);
            $item['tvg'] = $item['vg'];
            $item['tleft'] = $item['vleft'];
            $item['tright'] = $item['vpright'];
            $item['paid'] = 0;
            $item['hold'] = 0;
            
            Mage::register('network', $item);            
        }
        
        Mage::register('binary', $collection);
        $this->loadLayout(); 
        $this->renderLayout();
    }
    
}
