<?php
class MW_Affiliate_Model_Order
{
    // Use for every versions of Magento
    public function saveOrderAfter($argv)
    {
    	$store_id = $argv->getOrder()->getStoreId();
        if(Mage::helper('affiliate/data')->getEnabledStore($store_id)) 
        {     
            $discountAmount = 0;
            $referral_code = Mage::helper('affiliate') ->getReferralCodeByCheckout();
            $referral_sponsor = Mage::helper('affiliate') ->getReferralCodeByCheckout();
            $program_priority = Mage::helper('affiliate/data')->getAffiliatePositionStore($store_id);
            $position_discount = Mage::helper('affiliate/data')->getAffiliateDiscountStore($store_id);
            $affiliate_tax = Mage::helper('affiliate/data')->getAffiliateTaxtStore($store_id);

            $order_id = $argv->getOrder()->getId();
            $orderid = $argv->getOrder()->getIncrementId();
		    	    
    	    $customer_id = Mage::getModel("sales/order")->load($order_id)->getCustomerId();
    	    if($customer_id) {
    	    	$this->saveCustomerRegister($referral_code, $referral_sponsor, $customer_id);
    	    }
    	    // Set of customer_invited (direct-invited) 
            $invited_customers = array();

                    // Set of customer_invited (customer_id)
            $invited_customers_array = array();
	    	
		   	// Set of all customer_invited (union of 2 above sets) 
    	    $total_invited_customers = array();
            $items = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
            $programs = array();
            $programs = $this->getAllProgram();
            if(!Mage::app()->isSingleStoreMode()) {
                    $programs = $this->getProgramByStoreView($programs);
            }
            $programs = $this->getProgramByEnable($programs);
            $_programs = $this->getProgramByTime($programs);
            foreach($items as $item) 
            {
                $product_id = $item->getProductId();
                $qty = $item->getQty();
                if($position_discount == 1) {
                        $price = $item->getBasePrice();
                        $price_tax = $item->getBasePriceInclTax();
                }  
                else {
                        $price = $item->getBasePrice() - ($item->getBaseDiscountAmount()- $item->getMwAffiliateDiscount()- $item->getMwCreditDiscount())/$qty;
                        $price_tax = $item->getBasePriceInclTax() - ($item->getBaseDiscountAmount())/$qty;
                }
                if($affiliate_tax == 0)	{
                        $price_tax = $price;
                }
                $programs = $this->processRule($item, $_programs);
                $programs = $this->getProgramByCustomer($programs, $referral_code,$orderid,$store_id);
				    	
                if(sizeof($programs) >= 2)
                {
                    $array_customer_inviteds = array_splice($programs,sizeof($programs)-1,1);
                    foreach($array_customer_inviteds as $array_customer_invited) {
                            $customer_invited = $array_customer_invited;
                            break;
                    }
                    if(!in_array($customer_invited,$invited_customers)){
                            $invited_customers[] = $customer_invited;
                    }

                    // get program by 3 criterion
                    if($program_priority == 1)
                    {
                            $program_id=$this->getProgramByCommission($programs,$qty,$price,$customer_invited);
                    }
                    else if($program_priority == 2)
                    {
                            $program_id=$this->getProgramByDiscount($programs,$qty,$price,$customer_invited);
                    }
                    else if($program_priority == 3)
                    {
                            $program_id=$this->getProgramByPosition($programs);
                    };

                    $discount = $this->getDiscountByProgram($program_id,$qty,$price,$orderid, $customer_invited);
                    $discount = round($discount, 2);
                    if(!$customer_id) {
                            $customer_id = 0;
                    }
                    $multi_level_commission = (int)$this->getMultiLevel($program_id);
                    $invited_customers_array = $this->getArrayCustomerInvited($customer_invited,$multi_level_commission);

                    $level = 1;
                    foreach($invited_customers_array as $invited_customers_level) 
                    {
                            // Check if member is locked or restricted in 3 limits
                        $mw_check_limit = $this->checkThreeConditionCustomerInvited($customer_id, $invited_customers_level,$orderid);
                        if(Mage::helper('affiliate')->getLockAffiliate($invited_customers_level) == 1 || $mw_check_limit == 0) 
                        {
                            $level = $level + 1;
                            continue;
                        }
                        if(!in_array($invited_customers_level,$total_invited_customers))
                        {
                            $total_invited_customers[] = $invited_customers_level;
                        }
                        $commission = $this->getCommissionByProgram($program_id,$qty,$price_tax,$level);
                        $invitation_type = MW_Affiliate_Model_Typeinvitation::REFERRAL_LINK;
                        $historyData = array(
                            'customer_id'	=> $customer_id,
                            'product_id'	=> $product_id,
                            'program_id'	=> $program_id,
                            'order_id'		=> $orderid,
                            'total_amount'	=> $qty*$price,
                            'history_commission'=> $commission,
                            'history_discount'	=> $discount,
                            'customer_invited'	=> $invited_customers_level,
                            'invitation_type'	=> $invitation_type,
                            'status'		=> MW_Affiliate_Model_Status::PENDING,	
                            'transaction_time'	=> now()
                        );

                        if($program_id !=0)
                        {
                            if($commission > 0 || $discount > 0) {
                                    Mage::getModel('affiliate/affiliatehistory')->setData($historyData)->save();
                            }
                        }

                        $level = $level + 1; 
                    }
                } 
                else 
                {
                    $discount = 0;
                    $commission = 0;
                };
                        
                $discountAmount = $discountAmount + $discount;
            }
                 
            $invitation_type = $this->getInvitationType($referral_code, $order_id, $invited_customers);
            $this->setAffiliateTransaction($orderid,$total_invited_customers,$discountAmount,$invitation_type);

            // update customer_invited in case of referral-code-checkout (if config is enable) 
            $set_customer_invited = Mage::helper('affiliate/data')->setNewCustomerInvitedStore($store_id);
            if($set_customer_invited) {
                $this->setCustomerInvited($referral_code, $order_id, $invited_customers);
            }
            // destroy session of referral code
            Mage::getSingleton('checkout/session') ->unsetData('referral_code'); 
            
            $this->autoApproveCustomer($customer_id, $order_id);
            $this->levelUp($customer_id, $order_id);
            
            
        }
    }
    
    public function changeCustomRank(){        
        Mage::getStoreConfig('affiliate/rank/url',Mage::app()->getStore());
    }
    
    public function levelUp($customer_id, $order_id){ 
        $collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
            ->addFieldToFilter('customer_id', $customer_id)
            ->addFieldToFilter('status ',MW_Affiliate_Model_Statusreferral::ENABLED)
            ->addFieldToFilter('active',MW_Affiliate_Model_Statusactive::ACTIVE);       
       
        if ($collection->getFirstItem()->getData()){
            $bundles = array();
            foreach (Mage::getStoreConfig('extras/excludes',Mage::app()->getStore()) as $key => $value)
                if ($value == 0)
                    array_push($bundles, str_replace("exclude_","",$key));
            
            $order = Mage::getModel("sales/order")->load($order_id); 
            $groups = Mage::getModel("affiliate/affiliategroup")->getCollection()->getData();
            $ordered_items = $order->getAllVisibleItems(); 
            $ranks = Mage::getStoreConfig('extras/ranks',Mage::app()->getStore());
            $items = array();
            $points = 0;
            foreach($ordered_items as $item){
                $product = Mage::getModel('catalog/product')->load($item->getProductId())->getData();
                $points = number_format($product['puntos'], 0, '.', '') + $points;
                array_push($items, $item->getProductId());
                $search = array_search($item->getProductId(), $bundles);
                if (strlen($search) > 0){
                    $rid = $ranks['bundle_'.$item->getProductId()];
                    $iid = $item->getProductId();
                }
            }           
            
            $info = Mage::getModel('affiliate/affiliategroupmember')->getCollection()
                ->addFieldToFilter('customer_id', $customer_id)
                ->getLastItem()
                ->getData();
        
            $current = $info['group_id'];
            foreach ($ranks as $key => $value) {
                if ($value == $current){
                    $cp = str_replace("bundle_","",$key);
                }            
            }
        
            $pd1 = Mage::getModel('catalog/product')->load($iid);
            $pd2 = Mage::getModel('catalog/product')->load($cp);
            $fp1 =$pd1->getFinalPrice();
            $fp2 =$pd2->getFinalPrice();

            if ($fp1 > $fp2){
                foreach ($groups as $key => $value) {
                    if ($rid == $value['group_id']){
                        $levelup = array('group_id' => $rid);
                        Mage::getModel('affiliate/affiliategroupmember')
                            ->load($info['id'])
                            ->addData($levelup)
                            ->save();
                    }
                }
            }
            
            $this->addPoints('vp', $customer_id, $points);
            $parent = $collection->getFirstItem()->getData('customer_invited');
            if ($parent > 0)
                $this->addPoints('vg', $parent, $points);
        }
    }
    
    public function addPoints($type, $customer_id, $points){
        $month_date = strtotime(date('01/m/Y 00:00'));
        $volume = Mage::getModel('affiliate/affiliatevolume')
                ->getCollection()
                ->addFieldToFilter('month_date', $month_date)
                ->addFieldToFilter('customer_id', $customer_id)
                ->getFirstItem()
                ->getData();

        if ($volume){
            $data = array("{$type}" => $volume["{$type}"] + $points);
            Mage::getModel('affiliate/affiliatevolume')
                ->load($volume['volume_id'])
                ->addData($data)
                ->save(); 
        }
        else{
            $data = array("{$type}" => $points, 'customer_id' => $customer_id, 'month_date' => $month_date);
            Mage::getModel('affiliate/affiliatevolume')
                ->setData($data)
                ->save(); 
        }   
    }
    
    public function autoApproveCustomer($customer_id, $order_id){        
        $collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
            ->addFieldToFilter('customer_id',$customer_id)
            ->addFieldToFilter('status ',MW_Affiliate_Model_Statusreferral::ENABLED)
            ->addFieldToFilter('active',MW_Affiliate_Model_Statusactive::ACTIVE);
        	
        if (!(sizeof($collection))){
            if ((Mage::getStoreConfig('affiliate/config/auto_approve_payment')) && 
                    ((Mage::getStoreConfig('affiliate/config/auto_approve_payment_methods')) && 
                    (Mage::getStoreConfig('affiliate/config/auto_approve_products')))){

                $order = Mage::getModel("sales/order")->load($order_id);                
                $payment = $order->getPayment();
                $paymentCode = $payment->getMethodInstance()->getCode();

                $ordered_items = $order->getAllVisibleItems(); 
                $items = array();
                foreach($ordered_items as $item){
                    array_push($items, $item->getProductId());
                }

                $payment_methods = explode(',', Mage::getStoreConfig('affiliate/config/auto_approve_payment_methods'));
                $approvedproducts = explode(',', Mage::getStoreConfig('affiliate/config/auto_approve_products'));

                if ((preg_grep ("/^{$paymentCode}/i", $payment_methods)) && (count(array_intersect($items, $approvedproducts)))){
//                    Mage::getModel('affiliate/affiliatecustomers')->updateCustomerApproval($customer, MW_Affiliate_Model_Statusactive::ACTIVE);         
                    Mage::getSingleton('affiliate/affiliatecustomers')->load($customer_id)
                        ->setActive(MW_Affiliate_Model_Statusactive::ACTIVE)
                        ->setCustomerTime(now())
                        ->save(); 
                    // set lai referral code cho cac customer affiliate
                    Mage::helper('affiliate') ->setReferralCode($customer_id);
                    // tu dong chuyen thanh vien do vao trong default group
                    $store_id = Mage::getModel('customer/customer')->load($customer_id)->getStoreId();
                    Mage::helper('affiliate') ->setMemberDefaultGroupAffiliate($customer_id,$store_id,2);                     
                    //gui mail khi admin dong y cho gia nhap vao affiliate
                    Mage::helper('affiliate') ->sendMailCustomerActiveAffiliate($customer_id);
	    
                   //set total member customer program 
		   Mage::helper('affiliate')->setTotalMemberProgram(); 
                   
//                   $this->_getSession()->addSuccess($this->__('Your Affiliate Account has been activated'));	   
                   
                }                       
            }
        }
        
        
        //Automatic Approve!
        
        $collection = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
            ->addFieldToFilter('customer_id',$customer_id)
            ->addFieldToFilter('autoapprove', 1);
        
        if ((sizeof($collection))){
            if ((Mage::getStoreConfig('affiliate/config/auto_approve_payment')) && 
                    ((Mage::getStoreConfig('affiliate/config/auto_approve_payment_methods')) && 
                    (Mage::getStoreConfig('affiliate/config/auto_approve_products')))){

                $order = Mage::getModel("sales/order")->load($order_id);                
                $payment = $order->getPayment();
                $paymentCode = $payment->getMethodInstance()->getCode();

                $ordered_items = $order->getAllVisibleItems(); 
                $items = array();
                foreach($ordered_items as $item){
                    array_push($items, $item->getProductId());
                }

                $payment_methods = explode(',', Mage::getStoreConfig('affiliate/config/auto_approve_payment_methods'));
                $approvedproducts = explode(',', Mage::getStoreConfig('affiliate/config/auto_approve_products'));

                if ((preg_grep ("/^{$paymentCode}/i", $payment_methods)) && (count(array_intersect($items, $approvedproducts)))){
                    Mage::getSingleton('affiliate/affiliatecustomers')->load($customer_id)
                        ->setAutoapprove(3)
                        ->save(); 
                }
            }
        }
                 
    }
    	
    public function getInvitationType($referral_code, $order_id, $invited_customers)
    {
    	$invitation_type = MW_Affiliate_Model_Typeinvitation::REFERRAL_LINK;
    	$customer_id = (int)Mage::getModel("sales/order")->load($order_id)->getCustomerId();
    	if($customer_id)
    	{
	    	$customers = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id);
	    	$customer_invited = $customers ->getCustomerInvited();
	    	if(!$customer_invited) {
	    		$customer_invited = 0;
	    	}
    		$invitation_type = $customers->getInvitationType();
	    	if(isset($referral_code) && $referral_code !='' && sizeof($invited_customers) > 0)
	    	{
    			foreach ($invited_customers as $invited_customer) {
    				if($invited_customer != 0 && $invited_customer != $customer_id && $invited_customer != $customer_invited){
    					$invitation_type = MW_Affiliate_Model_Typeinvitation::REFERRAL_CODE;

    				}
    			}
	    	}
	    }
	    return $invitation_type;
    } 	
    
    public function setCustomerInvited($referral_code, $order_id, $invited_customers)
    {
    	if(isset($referral_code) && $referral_code != '' && sizeof($invited_customers) > 0)
    	{
    		$customer_id = intval(Mage::getModel("sales/order")->load($order_id)->getCustomerId());
    		if($customer_id) 
    		{
    			$customers = Mage::getModel('affiliate/affiliatecustomers') ->load($customer_id);
    			$customer_invited = $customers ->getCustomerInvited();
    			if(!$customer_invited) {
    				$customer_invited = 0;
    			}
    			foreach ($invited_customers as $invited_customer) 
    			{
    				if($invited_customer != 0 && $invited_customer != $customer_id && $invited_customer != $customer_invited)
    				{
    					$invitation_type = MW_Affiliate_Model_Typeinvitation::REFERRAL_CODE;
    					$customers->setCustomerInvited($invited_customer)->save();
    					$customers->setCustomerTime(now())->save();
    					$customers->setInvitationType($invitation_type)->save();
    				}
    			}
    		}
    	}
    } 	
    
    public function saveCustomerRegister($referral_code, $referral_sponsor, $customer_id)
    { 
    	$cookie = (int)Mage::getModel('core/cookie')->get('customer');
    	
    	// if the cookie of inviter doesn't exist then assign it to zero
    	if($cookie) {
            if(Mage::helper('affiliate')->getLockAffiliate($cookie)== 1) {
                $cookie = 0;
            }
    	} else {
            $cookie = 0;
    	}
    	$cookie_old = $cookie;
    	if($referral_code != '') {
            $cookie = Mage::helper('affiliate') ->getCustomerIdByReferralCode($referral_code, $cookie);
    	}
    	if($referral_sponsor != '') {
            $cookie = Mage::helper('affiliate') ->getCustomerIdByReferralCode($referral_sponsor, $cookie);
    	}
    	$invitation_type = MW_Affiliate_Model_Typeinvitation::NON_REFERRAL;
    	if($cookie != 0) {
            $invitation_type = MW_Affiliate_Model_Typeinvitation::REFERRAL_LINK;
    	}
    	if($cookie_old != $cookie && $cookie != 0) {
            $invitation_type = MW_Affiliate_Model_Typeinvitation::REFERRAL_CODE;
    	}
    	
    	$customers = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
            ->addFieldToFilter('customer_id',$customer_id);
    	$store_id = Mage::app()->getStore()->getId();  
        $active = MW_Affiliate_Model_Statusactive::INACTIVE;
        $auto_signup_affiliate = (int)Mage::helper('affiliate/data')->getAutoSignUpAffiliateStore($store_id); 
        if($auto_signup_affiliate)
        {
            $active = MW_Affiliate_Model_Statusactive::PENDING;
            $auto_approved = Mage::helper('affiliate/data')->getAutoApproveRegisterStore($store_id);
            if($auto_approved) {
                $active = MW_Affiliate_Model_Statusactive::ACTIVE;
            }
    	}
        if(sizeof($customers)== 0 && ($cookie != 0 || $active != MW_Affiliate_Model_Statusactive::INACTIVE))
        {	
            $customerData = array(
                'customer_id'		=> $customer_id,
                'active'		=> $active,
                'payment_gateway'	=> '',
                'payment_email'		=> '',
                'auto_withdrawn'	=> 0,
                'withdrawn_level'	=> 0,
                'reserve_level'		=> 0,
                'bank_name'		=> '',
                'name_account'		=> '',
                'bank_country'		=> '',
                'swift_bic'		=> '',
                'account_number'	=> '',
                're_account_number'	=> '',
                'referral_site'		=> '',
                'total_commission'	=> 0,
                'total_paid'		=> 0,
                'referral_code' 	=> '',
                'referral_sponsor' 	=> '',
                'status'		=> 1,
                'invitation_type'	=> $invitation_type,
                'customer_time' 	=> now(),
                'customer_invited'	=> $cookie,
                'auto_approve'          => ($active == MW_Affiliate_Model_Statusactive::ACTIVE) ? 1 : 0,
            );
            
            Mage::getModel('affiliate/affiliatecustomers')->saveCustomerAccount($customerData);  

            $clientIP = Mage::app()->getRequest()->getServer('REMOTE_ADDR');
            $referral_from = Mage::getModel('core/cookie')->get('mw_referral_from');
            $referral_to = Mage::getModel('core/cookie')->get('mw_referral_to');
            $referral_from_domain = Mage::getModel('core/cookie')->get('mw_referral_from_domain');
            if(!$referral_from) {
                $referral_from = '';
            }
            if(!$referral_to) {
                $referral_to = '';
            }
            if(!$referral_from_domain) {
                $referral_from_domain = '';
            }
            if($invitation_type == MW_Affiliate_Model_Typeinvitation::REFERRAL_CODE)
            {
                $referral_from = '';
                $referral_to = '';
                $referral_from_domain = '';
            }
            
            $email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
            $collection = Mage::getModel('affiliate/affiliateinvitation')
                ->getCollection()
                ->addFieldToFilter('customer_id',$cookie)
                ->addFieldToFilter('email',$email);
    		
		    // neu ban be dc moi dang ky lam thanh vien cua website ?
		    // voi email trung voi email moi se update lai trang thai 
		    if(sizeof($collection) > 0)
		    {
		    	foreach ($collection as $obj) 
		    	{
		    		$obj->setStatus(MW_Affiliate_Model_Statusinvitation::REGISTER);
		    		$obj->setIp($clientIP);
		    		$obj->setInvitationTime(now());
		    		$obj->setCountClickLink(0);
		    		$obj->setCountRegister(1);
		    		$obj->setCountPurChase(0);
		    		$obj->setCountSubscribe(0);
		    		$obj->setReferralFrom($referral_from);
		    		$obj->setReferralFromDomain($referral_from_domain);
		    		$obj->setReferralTo($referral_to);
		    		$obj->setOrderId('');
		    		$obj->setInvitationType($invitation_type);
		    		$obj->save();
		    	}
		    }
		    // nguoc lai luu moi vao csdl
		    else
		    {
		    	$historyData = array(
		    						 'customer_id'			=> $cookie,
	                        		 'email'				=> $email, 
	                        		 'status'				=> MW_Affiliate_Model_Statusinvitation::REGISTER, 
	                        		 'ip'					=> $clientIP,
				    				 'count_click_link'		=> 0,
                        			 'count_register'		=> 1, 
                        			 'count_purchase'		=> 0,
		    						 'count_subscribe'		=> 0,
                                 	 'referral_from'		=> $referral_from, 
		    	                     'referral_from_domain' => $referral_from_domain,
                        			 'referral_to'			=> $referral_to,
                        			 'order_id'				=> '',
		    	                     'invitation_type'		=> $invitation_type,
	                        		 'invitation_time'		=> now()
		    					);
                Mage::getModel('affiliate/affiliateinvitation')->setData($historyData)->save();
		    }			
        }
    }
    
	public function setAffiliateTransaction($order_id,$invited_customers,$discountAmount,$invitation_type)
    {   
    	$total_commission = 0;
    	$customer_id = Mage::getModel("sales/order")->loadByIncrementId($order_id)->getCustomerId();
    	if(sizeof($invited_customers) > 0)
    	{
			foreach ($invited_customers as $invited_customer) 
			{
				$affiliateHistorys = Mage::getModel('affiliate/affiliatehistory')->getCollection()
										->addFieldtoFilter('order_id',$order_id)
										->addFieldtoFilter('customer_invited',$invited_customer);
				
				if(sizeof($affiliateHistorys) > 0)
				{   
					$customer_commission = 0;
					$customer_discount = 0;
					foreach ($affiliateHistorys as $affiliateHistory) {
						$affiliateHistory->setInvitationType($invitation_type)->save();
						$commission = $affiliateHistory->getHistoryCommission();
						$discount = $affiliateHistory->getHistoryDiscount();
						$customer_commission = $customer_commission + $commission;
						$customer_discount = $customer_discount + $discount;
					}
					$historyData = array(
										 'order_id'			=> $order_id,
					                     'customer_id'      =>$customer_id,
	              						 'total_commission'	=> $customer_commission,
		    							 'total_discount'	=> $customer_discount,
		    							 'customer_invited'	=> $invited_customer,
					                     'invitation_type'	=> $invitation_type,
					                     'commission_type'	=> MW_Credit_Model_Transactiontype::BUY_PRODUCT,
		    							 'status'			=> MW_Affiliate_Model_Status::PENDING,	
	              						 'transaction_time'	=> now()
									);
					Mage::getModel('affiliate/affiliatetransaction')->setData($historyData)->save();
					$total_commission = $total_commission + $customer_commission;
				}
			}
			$transactionData = array(
									 'order_id'				=> $order_id,
			                         'customer_id'          =>$customer_id,
	              					 'total_commission'		=> $total_commission,
	    							 'total_discount'		=> $discountAmount,
									 'show_customer_invited'=> $invited_customers[0],
	    							 'customer_invited'		=> 0,
			                         'invitation_type'		=> $invitation_type,
			                         'commission_type'	    => MW_Credit_Model_Transactiontype::BUY_PRODUCT,
	    							 'status'				=> MW_Affiliate_Model_Status::PENDING,	
	              					 'transaction_time'		=> now()
								);
			if($total_commission > 0 || $discountAmount > 0) {
				Mage::getModel('affiliate/affiliatetransaction')->setData($transactionData)->save();
			}
			
			// save invitation when customer purchase product
			$clientIP = Mage::app()->getRequest()->getServer('REMOTE_ADDR');
	    	$email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
	    	$click_lick = MW_Affiliate_Model_Statusinvitation::CLICKLINK;
			$register = MW_Affiliate_Model_Statusinvitation::REGISTER;
			$purchase = MW_Affiliate_Model_Statusinvitation::PURCHASE;
			$referral_from = '';
    		$referral_to = '';
    		$referral_from_domain = '';
    		if($invitation_type == MW_Affiliate_Model_Typeinvitation::REFERRAL_LINK) 
    		{
		    	$collection_invitations = Mage::getModel('affiliate/affiliateinvitation')
		    							  ->getCollection()
		    							  ->addFieldToFilter('email',$email)
		    							  ->addFieldToFilter('status',array('in' => array($click_lick,$register,$purchase)));
		    	
		    	foreach ($collection_invitations as $collection_invitation) 
		    	{
		    		$referral_from = $collection_invitation->getReferralFrom();
		    		$referral_from_domain = $collection_invitation->getReferralFromDomain();
		    		$referral_to = $collection_invitation->getReferralTo();
		    		if($referral_from != '') {
		    			break;
		    		}
		    	}
    		}	
    		
    		/* Update Invitation Table */
    		$history = Mage::getModel('affiliate/affiliatehistory')
    				   ->getCollection()
    				   ->addFieldtoFilter('order_id',$order_id)
    				   ->addFieldtoFilter('customer_invited',$invited_customers[0]);
    		
    		if(sizeof($history)) 
    		{
    			foreach($history as $item) {
					$invitationData = array( 
											 'customer_id'			=> $invited_customers[0],
			                        		 'email'				=> $email, 
			                        		 'status'				=> MW_Affiliate_Model_Statusinvitation::PURCHASE, 
			                        		 'ip'					=> $clientIP,
											 'count_click_link'		=> 0,
		                        			 'count_register'		=> 0,
											 'count_subscribe'		=> 0,
		                        			 'count_purchase'		=> 1,
		                                 	 'referral_from'		=> $referral_from, 
					                         'referral_from_domain'	=> $referral_from_domain,
		                        			 'referral_to'			=> $referral_to,
		                        			 'order_id'				=> $order_id,
											 'commission'			=> $item->getHistoryCommission(),
				    	                     'invitation_type'		=> $invitation_type,
			                        		 'invitation_time'		=> now()
										);
		        	Mage::getModel('affiliate/affiliateinvitation')->setData($invitationData)->save();
    			}
    		}
    	}
    	
    	$amountCredit = Mage::getSingleton('checkout/session')->getCredit();
    	
    	// save credit va affiliate dicount cho moi order
    	if(!$amountCredit) {
    		$amountCredit = 0;
    	}
    	if($amountCredit > 0  || $discountAmount > 0 ) {
    		$orderData = array(
    							'order_id'	=> $order_id,
    						   	'affiliate'	=> round($discountAmount,2),
            			  	   	'credit'	=> round($amountCredit,2)
    					 );
        	Mage::getModel("credit/creditorder")->saveCreditOrder($orderData); 
    	}
    	
    }
    
	public function getAllProgram()
    {
    	$programs = array();
    	$program_collections = Mage::getModel('affiliate/affiliateprogram')->getCollection();
    	foreach($program_collections as $program_collection) {
    		$programs[] = $program_collection ->getProgramId();
    	}
    	return $programs;
    }
    
   	public function processRule(Mage_Sales_Model_Quote_Item_Abstract $item, $programs)
    {   
    	$program_ids = array();
    	foreach($programs as $program) 
    	{
    	 	$rule = Mage::getModel('affiliate/affiliateprogram')->load($program);
    	 	$rule->afterLoad();
    	 	$address = $this->getAddress_new($item);
    		if(($rule->validate($address)) && ($rule->getActions()->validate($item))) {
    			$program_ids[] = $program;
    		}
    	}
    	return $program_ids;
    }
    
	protected function getAddress_new(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $address = $item->getAddress();
        } elseif ($item->getQuote()->isVirtual()) {
            $address = $item->getQuote()->getBillingAddress();
        } else {
            $address = $item->getQuote()->getShippingAddress();
        }
        return $address;
    }
    
    public function getProgramByCommission($programs,$qty,$price,$customer_invited)
    {   
    	$array_commissions = array();
    	$max = 0;
    	$program_id = 0;
        foreach($programs as $program) 
		{
			$result_commission = 0 ;
			$commissions = Mage::getModel('affiliate/affiliateprogram')->load($program)->getCommission();
			if(substr_count($commissions,',') == 0 ) {
				$result_commission = $commissions;
			} else if(substr_count($commissions,',') >= 1) {
				$commission = explode(",",$commissions);
				$result_commission = $commission[0];
			};
			if(substr_count($result_commission,'%')==1)
            {
	        	$text = explode("%",$result_commission);
	            $percent = trim($text[0]);
	            $array_commissions[$program]=($percent*$price*$qty)/100;
            }
            else if(substr_count($result_commission,'%')==0)
            {
              	$array_commissions[$program]=$result_commission*$qty;
            };
           	if($max < $array_commissions[$program]) {
           		$max = $array_commissions[$program];
           		$program_id = $program;
           }
		}
		if($program_id == 0) {
			$program_id = $this->getProgramByDiscountOld($programs,$qty,$price,$customer_invited);
		}
    	return $program_id;
    }
    
    public function getProgramByCommissionOld($programs,$qty,$price)
    {   
    	$array_commissions = array();
    	$max = 0;
    	$program_id = 0;
        
        foreach ($programs as $program) 
        {
            $result_commission = 0 ;
            $commissions = Mage::getModel('affiliate/affiliateprogram')->load($program)->getCommission();
            if(substr_count($commissions,',') == 0 ) {
                $result_commission = $commissions;
            } else if(substr_count($commissions,',') >= 1){
                $commission = explode(",",$commissions);
                $result_commission = $commission[0];
            };
            
            if(substr_count($result_commission,'%') == 1)
            {
                $text = explode("%",$result_commission);
                $percent = trim($text[0]);
                $array_commissions[$program]=($percent*$price*$qty)/100;
            }
            else if(substr_count($result_commission,'%')==0)
            {
              	$array_commissions[$program]=$result_commission*$qty;
            };
            
            if($max < $array_commissions[$program]) {
                $max = $array_commissions[$program];
                $program_id = $program;
            }
        }
        
    	return $program_id;
    }
    
    public function getProgramByPosition($programs)
    {   
    	$program_id=0;
    	$positions = array();
    	$min_position=0;
        foreach ($programs as $program) {
            $min_position = Mage::getModel('affiliate/affiliateprogram')->load($program)->getProgramPosition();
            break;
        }
        
    	foreach ($programs as $program) {
            $positions[$program] = Mage::getModel('affiliate/affiliateprogram')->load($program)->getProgramPosition();
            if($positions[$program]<= $min_position) {
                $min_position = $positions[$program];
                $program_id=$program;
            }
        }
		
    	return $program_id;
    }
    
    public function getProgramByDiscount($programs,$qty,$price,$customer_invited)
    {
    	$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
    	if(!$customer_id) {
            $customer_id = 0;
    	}
        
    	$array_discounts = array();
    	$max = 0;
    	$program_id = 0;
        
        foreach ($programs as $program) {
            $result_discounts = 0;
            $discounts = Mage::getModel('affiliate/affiliateprogram')->load($program)->getDiscount();
            if(substr_count($discounts,',') == 0) {
                $result_discounts = $discounts;
            } else if(substr_count($discounts,',') >= 1) {
                $discount = explode(",",$discounts);
                if($customer_id == 0) {
                    $result_discounts = $discount[0];	
                } else {
                    $collection = Mage::getModel('affiliate/affiliatehistory')->getCollection()
                        ->addFieldToFilter('customer_invited',$customer_invited)
                        ->addFieldToFilter('customer_id',$customer_id)
                        ->addFieldToFilter('status',array('nin' =>array(MW_Affiliate_Model_Status::CANCELED)));
                    
                    $collection->getSelect()->group('order_id');
                    $sizeof_discount = sizeof($discount);
                    $sizeo_order = sizeof($collection);
                    if($sizeo_order < $sizeof_discount) {
                        $result_discounts = $discount[$sizeo_order];
                    } else if($sizeo_order >= $sizeof_discount) {
                        $result_discounts = $discount[$sizeof_discount - 1];
                    };
                };
            };
            
            if(substr_count($result_discounts,'%') == 1)
            {
                $text = explode("%",$result_discounts);
                $percent = trim($text[0]);
                $array_discounts[$program]=($percent*$price*$qty)/100;
            }
            
            else if(substr_count($result_discounts,'%')==0)
            {
              	$array_discounts[$program]= $result_discounts*$qty;
            }
            
            if( $max < $array_discounts[$program]) {
                $max = $array_discounts[$program];
                $program_id = $program;
            }
        }
        
        if($program_id == 0) {
            $program_id = $this->getProgramByCommissionOld($programs,$qty,$price);
        }
		
    	return $program_id;
    }
    
    public function getProgramByDiscountOld($programs,$qty,$price,$customer_invited)
    {   
    	$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
        
    	if(!$customer_id) {
            $customer_id = 0;
    	}
        
    	$array_discounts = array();
    	$max = 0;
    	$program_id = 0;
        foreach ($programs as $program) 
        {
            $result_discounts = 0;
            $discounts = Mage::getModel('affiliate/affiliateprogram')->load($program)->getDiscount();
            if(substr_count($discounts,',') == 0) {
                $result_discounts = $discounts;
            } 
            else if(substr_count($discounts,',') >= 1) {
                $discount = explode(",",$discounts);
                if($customer_id == 0) {
                    $result_discounts = $discount[0];	
                } else {
                    $collection = Mage::getModel('affiliate/affiliatehistory')->getCollection()
                        ->addFieldToFilter('customer_invited',$customer_invited)
                        ->addFieldToFilter('customer_id',$customer_id)
                        ->addFieldToFilter('status',array('nin' =>array(MW_Affiliate_Model_Status::CANCELED)));
                    
                    $collection ->getSelect()->group('order_id');
                    $sizeof_discount = sizeof($discount);
                    $sizeo_order = sizeof($collection);
                    
                    if($sizeo_order < $sizeof_discount) {
                        $result_discounts = $discount[$sizeo_order];
                    } 
                    else if($sizeo_order >= $sizeof_discount) {
                        $result_discounts = $discount[$sizeof_discount - 1];
                    };
                };
            };
            
            if(substr_count($result_discounts,'%')==1)
            {
                $text = explode("%",$result_discounts);
                $percent = trim($text[0]);
                $array_discounts[$program]=($percent*$price*$qty)/100;
            }
            else if(substr_count($result_discounts,'%')==0)
            {
              	$array_discounts[$program]= $result_discounts*$qty;
            };
            
            if( $max < $array_discounts[$program]) {
                $max = $array_discounts[$program];
                $program_id = $program;
            }
        }
		
    	return $program_id;
    }
    
    public function getDiscountByProgram($program_id,$qty,$price,$orderid, $customer_invited)
    {   
    	$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
        
    	if(!$customer_id) {
    		$customer_id = 0;
    	}
        
    	$mw_discounts = 0;	
        $discounts = Mage::getModel('affiliate/affiliateprogram')->load($program_id)->getDiscount();
        
        if(substr_count($discounts,',') == 0) 
        {
            $result_discounts = $discounts;
        } 
        else if(substr_count($discounts,',') >= 1) 
        {
            $discount = explode(",",$discounts);
            if($customer_id == 0) 
            {
                $result_discounts = $discount[0];	
            } 
            else 
            {
                $collection = Mage::getModel('affiliate/affiliatehistory')->getCollection()
                    ->addFieldToFilter('customer_invited',$customer_invited)
                    ->addFieldToFilter('customer_id',$customer_id)
                    ->addFieldToFilter('order_id',array('nin' =>array($orderid)))
                    ->addFieldToFilter('status',array('nin' =>array(MW_Affiliate_Model_Status::CANCELED)));

                $collection ->getSelect()->group('order_id');
                $sizeof_discount = sizeof($discount);
                $sizeo_order = sizeof($collection);

                if($sizeo_order < $sizeof_discount) {
                    $result_discounts = $discount[$sizeo_order];
                } 
                else if($sizeo_order >= $sizeof_discount){
                    $result_discounts = $discount[$sizeof_discount - 1];
                };
            };
        }
        
    	if(substr_count($result_discounts,'%') == 1)
        {
            $text=explode("%",$result_discounts);
            $percent=trim($text[0]);
            $mw_discounts = ($percent*$price*$qty)/100;
        }
        else if(substr_count($result_discounts,'%') == 0) {
            $mw_discounts = $result_discounts * $qty ;
        }
        
    	return $mw_discounts;
    }
    
    public function getCommissionByProgram($program_id,$qty,$price,$level)
    {   
    	$mw_commissions = 0;	
        $commissions = Mage::getModel('affiliate/affiliateprogram')->load($program_id)->getCommission();
        $commission = explode(",",$commissions);
        $result_commission = $commission[$level-1];
        
    	if(substr_count($result_commission,'%') == 1)
        {
            $text=explode("%",$result_commission);
            $percent=trim($text[0]);
            $mw_commissions = ($percent*$price*$qty)/100;
       	}
        else if(substr_count($result_commission,'%') == 0) 
        {
            $mw_commissions = $result_commission * $qty ;
        }
        
    	return $mw_commissions;
    }
    
    public function getProgramByTime($programs)
    {   
    	$program_ids = array();
    	foreach ($programs as $program) {
            $start_date = Mage::getModel('affiliate/affiliateprogram')->load($program)->getStartDate();
            $end_date = Mage::getModel('affiliate/affiliateprogram')->load($program)->getEndDate();
            if(Mage::app()->getLocale()->isStoreDateInInterval(null, $start_date, $end_date)) {
                $program_ids[] = $program;
            }
    	}
    	return $program_ids;
    }
    
    public function getProgramByStoreView($programs)
    {   
    	$program_ids = array();
    	$store_id = Mage::app()->getStore()->getId();
    	foreach ($programs as $program) {
            $store_view = Mage::getModel('affiliate/affiliateprogram')->load($program) ->getStoreView();
            $store_views = explode(',',$store_view);
            if(in_array($store_id, $store_views) OR $store_views[0] == '0') {
                $program_ids[] = $program; 
            }
    	}
    	return $program_ids;
    }
    
    public function getProgramByEnable($programs)
    {   
    	$program_ids = array();
    	foreach ($programs as $program) {
            $status = Mage::getModel('affiliate/affiliateprogram')->load($program)->getStatus();
            if($status==MW_Affiliate_Model_Statusprogram::ENABLED) {
                $program_ids[] = $program;
            }
    	}
    	return $program_ids;
    }
    
    // return list of programs and customer_invited
    public function getProgramByCustomer($programs, $referral_code,$orderid,$store_id)
    {   
    	$program_ids = array();
    	$program_id_news = array();
    	$result_new = array();
    	$result = array();
    	$cookie = (int)Mage::getModel('core/cookie')->get('customer');
    	if(!$cookie) {
            $cookie = 0;
    	}
        
    	$customer_id = (int)Mage::getSingleton("customer/session")->getCustomer()->getId();
    	
    	// check customer_id khong la thanh vien affiliate va khong co customer invited
    	$check = Mage::helper('affiliate') ->checkCustomer($customer_id);
    	$affiliate_commission = (int)Mage::helper('affiliate/data')->getAffiliateCommissionbyThemselves($store_id);
    	if($customer_id)
        {   
            $is_rererral_code = 0;
            $customer_invited = 0;
            $customer_id_new = (int)Mage::helper('affiliate') ->getCustomerIdByReferralCode($referral_code, $customer_id);
            // truong hop khong co referral code va co referral code
            if($customer_id == $customer_id_new) {
                $customer_invited = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id)->getCustomerInvited();
                if(!$customer_invited) $customer_invited = 0;
            } else {
                $customer_invited = $customer_id_new;
                $is_rererral_code = 1;
            }
	    	
            // kiem tra xem thanh vien do co customer_invited khong?
            if($customer_invited == 0) 
            {
                // kiem tra xem khach mua hang do co phai la affilite va ko bi khoa
                // tra ve mang chuong trinh va customer_invited
                if($affiliate_commission == 0 ) {
                    return $result;
                }

                if(Mage::helper('affiliate')->getActiveAndEnableAffiliate($customer_id) == 1) {
                    $result = $this ->checkThreeCondition($customer_id, $customer_id,$programs,$orderid);
                    if(sizeof($result) > 0) {
                        $result[]= $customer_id;
                        return $result;
                    }
                    else if(sizeof($result) == 0) {
                        return $result;
                    }
                }
            }
            else if($customer_invited != 0) {
                // customer invited bi khoa
                if(Mage::helper('affiliate')->getLockAffiliate($customer_invited) == 1) {
                    // neu khach hang la thanh vien cua affiliate va ko bi khoa
                    // load chuong trinh ra
                    if($affiliate_commission == 0) {
                        return $result;
                    }

                    if(Mage::helper('affiliate')->getActiveAndEnableAffiliate($customer_id) == 1) 
                    {
                        $result = $this ->checkThreeCondition($customer_id, $customer_id,$programs,$orderid);
                        if(sizeof($result) > 0) {
                            $result[]= $customer_id;
                            return $result;
                        }
                        else if(sizeof($result) == 0){
                            return $result;
                        }
                    }
                }
                // customer invited khong bi khoa
                else if(Mage::helper('affiliate')->getLockAffiliate($customer_invited) == 0) 
                {
                    if($is_rererral_code) {
                        $program_id_news = $this ->getProgramByCustomerId($customer_invited);
                        $result_new = array_intersect($program_id_news,$programs);
                    } else {
                        $result_new = $this ->checkThreeCondition($customer_id, $customer_invited,$programs,$orderid);
                    }
                    // customer invited tham gia vao chuong trinh co san pham
                    if(sizeof($result_new) > 0) {
                        $result_new[] = $customer_invited;
                        return $result_new;
                    }
                    // customer invited khong tham gia vao chuong trinh co san pham
                    else if(sizeof($result_new) == 0)
                    {
                        if($affiliate_commission == 0) {
                            return $result;
                        }

                        if(Mage::helper('affiliate')->getActiveAndEnableAffiliate($customer_id) == 1) {
                            $result = $this ->checkThreeCondition($customer_id, $customer_id,$programs,$orderid);
                            if(sizeof($result) > 0) {
                                $result[]= $customer_id;
                                return $result;
                            }
                            else if(sizeof($result) == 0) {
                                return $result;
                            }
                        }
                    }
                }
            }
            return $result;
        }
        
    	// neu khach hang mua hang khong dang ky la thanh vien cua website
    	// chi xet truong hop tim chuong trinh theo customer invited luu o cookie
    	$cookie = Mage::helper('affiliate')->getCustomerIdByReferralCode($referral_code, $cookie);
    	if($cookie)
    	{
            if(Mage::helper('affiliate')->getLockAffiliate($cookie)== 0)
            {
                $result = $this->checkThreeCondition(0, $cookie,$programs,$orderid);
                if(sizeof($result) > 0) 
                {
                        $result[]= $cookie;
                        return $result;
                }
            }
            return $result;
    	}	
    	return $result;
    }
    
	// kiem tra 3 dk config tra ve 0 or 1
    public function checkThreeConditionCustomerInvited($customer_id, $customer_invited,$orderid)
    {
    	$group_members = Mage::getModel('affiliate/affiliategroupmember')
            ->getCollection()
            ->addFieldToFilter('customer_id',$customer_invited);
    	
        $group_id = $group_members ->getFirstItem()->getGroupId();
        $group_affiliate = Mage::getModel('affiliate/affiliategroup')->load($group_id); 

        $time_day = $group_affiliate->getLimitDay();
        $total_order = $group_affiliate->getLimitOrder();
        $total_commission_customer = $group_affiliate->getLimitCommission();
		
    	// ham check dieu kien config thu nhat
    	$check_customer_time = $this->checkCustomerInvitedTime($customer_id,$time_day);
    	// ham kiem tra dieu kien config thu 2
    	$check_customer_order = $this->checkCustomerInvitedTotalOrder($customer_id,$customer_invited,$orderid,$total_order);
    	//check dieu kien thu 3
    	$check_customer_commission = $this->checkCustomerByTotalCommission($customer_id,$customer_invited,$total_commission_customer);
    	// neu thoa man 3 dieu kien config thi thuc hien binh thuong
    	if($check_customer_time == 1 && $check_customer_order == 1 && $check_customer_commission == 1) {
    		return 1;
    	}
    	return 0;
    }
    
	// kiem tra 3 dk config tra ve mang program......
    public function checkThreeCondition($customer_id, $customer_invited,$programs,$orderid)
    {
    	$result = array();
    	$program_ids = array();
    	$mw_check_limit = $this->checkThreeConditionCustomerInvited($customer_id, $customer_invited,$orderid);
    	
    	if($mw_check_limit == 1) {
            $program_ids = $this ->getProgramByCustomerId($customer_invited);
            $result = array_intersect($program_ids,$programs);
    	}
        
    	return $result;    	
    }
    
    // kiem tra thoi gian customer invited con hieu luc khong?
    public function checkCustomerInvitedTime($customer_id,$time_day)
    {
    	if($time_day == '') {
            return 0;
    	}
    	$time_day = intval($time_day);
    	if($customer_id == 0) {
    		return 1;
    	}
    	if($time_day == 0) {
    		return 1;
    	}
    	if($time_day >0) {
            $time_day_second = $time_day * 24 * 60 * 60;
            $date_now = Mage::getSingleton('core/date')->timestamp(time());
            $time_register = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id)->getCustomerTime();
            if(!$time_register) {
                return 1;
            }
            $date_register = Mage::getSingleton('core/date')->timestamp($time_register);
            if(($date_register + $time_day_second) > $date_now) {
                return 1;
            }
            if(($date_register + $time_day_second) <= $date_now) {
                return 0;
            }
    	}
    }
    
    // kiem tra so order invoice ma customer invoice moi dc
    public function checkCustomerInvitedTotalOrder($customer_id,$customer_invited,$orderid,$total_order)
    {
    	if($total_order == '') {
    		return 0;
    	}
        
    	$total_order = intval($total_order);
    	if($customer_id == 0) {
    		return 1;
    	}

    	if($total_order == 0) {
    		return 1;
    	}
        
    	$collection = Mage::getModel('affiliate/affiliatehistory')
            ->getCollection()
            ->addFieldToFilter('customer_invited',$customer_invited)
            ->addFieldToFilter('order_id',array('nin' =>array($orderid)))
            ->addFieldToFilter('customer_id',$customer_id)
            ->addFieldToFilter('status',array('nin' =>array(MW_Affiliate_Model_Status::CANCELED)));
    	
        $collection ->getSelect()->group('order_id');
    	
    	if(sizeof($collection) < $total_order) {
            return 1;
    	}
        
    	if(sizeof($collection) >= $total_order) {
            return 0;
    	}
    }

    // kiem tra theo total commission neu co cau hinh trong config?
    public function checkCustomerByTotalCommission($customer_id,$customer_invited,$total_commission_customer)
    {
    	if($total_commission_customer == '') {
            return 0;
    	}
        
    	$total_commission_customer = (double)$total_commission_customer;
    	if($customer_id == 0) {
            return 1;
    	}
        
    	if($total_commission_customer == 0) {
            return 1;
    	}
        
    	if($total_commission_customer > 0)
    	{
            $collection = Mage::getModel('affiliate/affiliatehistory')
                ->getCollection()
                ->addFieldToFilter('customer_invited',$customer_invited)
                ->addFieldToFilter('customer_id',$customer_id)
                ->addFieldToFilter('status',MW_Affiliate_Model_Status::COMPLETE);

            $collection->addExpressionFieldToSelect('history_commission_sum','sum(history_commission)','history_commission_sum');
            $total_commission = (double)$collection->getFirstItem()->getHistoryCommissionSum();

            if($total_commission >= $total_commission_customer) {
                return 0;
            }
    	}
        
    	return 1;
    }
    
    public function getProgramByCustomerId($customer_id)
    {
    	$program_ids = array();
    	$customer_groups =  Mage::getModel('affiliate/affiliategroupmember')
            ->getCollection()
            ->addFieldToFilter('customer_id',$customer_id);
        
    	if(sizeof($customer_groups) > 0)
    	{
            foreach ($customer_groups as $customer_group) {
                $group_id = $customer_group ->getGroupId();
                break;
            }
            $customer_programs = Mage::getModel('affiliate/affiliategroupprogram')
                ->getCollection()
                ->addFieldToFilter('group_id',$group_id);
            foreach ($customer_programs as $customer_program) {
                $program_ids[] = $customer_program->getProgramId();
            }
    	}
    	
    	return $program_ids;
    }
    
    public function getMultiLevel($program_id)
    {
    	$commissions = Mage::getModel('affiliate/affiliateprogram')->load($program_id)->getCommission();
    	if(substr_count($commissions,',') == 0 ) {
            return 1;
    	} 
        else if(substr_count($commissions,',') >= 1) {
            $commission = explode(",",$commissions);
            return sizeof($commission);
        };
        
        return 1;
    }
    
    public function getArrayCustomerInvited($customer_invited,$multi_level_commission)
    {
        $array_invited = array();
        $array_invited[1] = $customer_invited;
        if($multi_level_commission == 1)
        {
            return $array_invited;
        } 
        else 
        {
            $i = 1;
            while ($i < $multi_level_commission) 
            {
                $customer_invited_i = Mage::getModel('affiliate/affiliatecustomers')->load($array_invited[$i])->getCustomerInvited();
                if(!$customer_invited_i) {
                    $customer_invited_i = 0;
                }
                if($customer_invited_i == 0) {
                    break;
                } 
                else if($customer_invited_i != 0) {
                    $array_invited[$i +1] = $customer_invited_i;
                    $i = $i +1;
                }
            }

            return $array_invited;	
        } 
    }	
}