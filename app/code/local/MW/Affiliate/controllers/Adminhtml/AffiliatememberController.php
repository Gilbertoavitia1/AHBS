<?php
class MW_Affiliate_Adminhtml_AffiliatememberController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('affiliate/member')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Manager Members'), Mage::helper('adminhtml')->__('Manager Member'));
       
        return $this;
    }  
 
    public function indexAction() {
        $this->_initAction()->renderLayout();
    }
   
    public function networkAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_network')->toHtml());
    }
   
    public function invitationAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_invitation')->toHtml());
    }
   
    public function transactionAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_transaction')->toHtml());
    }
   
    public function viewtransactionAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_viewtransaction')->toHtml());
    }
   
    public function withdrawnAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_withdrawn')->toHtml());
    }
   
    public function credithistoryAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_credithistory')->toHtml());
    }
   
    public function programAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tab_program')->toHtml());
    }

    public function editAction() {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('affiliate/affiliatecustomers')->load($id);
       
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
           
            Mage::register('affiliate_data_member', $model);

            $this->loadLayout();
            $this->_setActiveMenu('affiliate/member');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit'))
                ->_addLeft($this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Member does not exist'));
            $this->_redirect('*/*/');
        }
    }
 
    public function newAction() {
        $model  = Mage::getModel('affiliate/affiliatecustomers');
       
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
               
        Mage::register('affiliate_data_member_new', $model);
   
        $this->loadLayout();
        $this->_setActiveMenu('affiliate/member');
   
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
       
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
       
        $this->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_add'))
             ->_addLeft($this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_add_tabs'));
       
        $this->renderLayout();
    }
   
    public function savenewAction() 
    {        
        
        $data = $this->getRequest()->getParams();
        if($data) {
            $customerId = Mage::getModel('customer/customer')
                ->getCollection()
                ->addFieldToFilter('email', array('eq' => $data['customer_email']))
                ->getFirstItem()
                ->getId();
            
            $parent = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
                ->addFieldToFilter('payment_email', array('eq' => $data['affiliate_parent']))
                ->getFirstItem()
                ->getData('customer_id');
            
            /* Save affiliate customers to db */
            $affiliateCustomerModel = Mage::getModel('affiliate/affiliatecustomers');
            $customerData = array(
                'customer_id'       => $customerId,
                'active'            => MW_Affiliate_Model_Statusactive::ACTIVE,
                'payment_gateway'   => $data['payment_gateway'],
                'anetwork'          => $data['anetwork'],
                'payment_email'     => $data['payment_email'],
                'auto_withdrawn'    => $data['auto_withdrawn'],
                'withdrawn_level'   => $data['withdrawn_level'],
                'reserve_level'     => $data['reserve_level'],
                'bank_name'         => $data['bank_name'],
                'name_account'      => $data['name_account'],
                'bank_country'      => $data['bank_country'],
                'swift_bic'         => $data['swift_bic'],
                'account_number'    => $data['account_number'],
                're_account_number' => $data['re_account_number'],
                'total_commission'  => 0,
                'total_paid'        => 0,
                'referral_code'     => '',
                'referral_sponsor'  => '',
                'customer_url'      => '',
                'customer_photo'    => '',
                'status'            => $data['affiliate_status'],
                'invitation_type'   => MW_Affiliate_Model_Typeinvitation::NON_REFERRAL,
                'customer_time'     => now(),
                'left_leg'          => $data['left_leg'],
                'right_leg'         => $data['right_leg']
            );
            
            $affiliateCustomerModel->setData($customerData)->save();
           
            /* Set Referral Code */
            Mage::helper('affiliate')->setReferralCode($customerId);
            
            /* Save affiliate websites to db */
            $referralSite = $data['referral_site'];
            if($referralSite) {
                $sites = explode(',', $referralSite);
                $websiteModel = Mage::getModel('affiliate/affiliatewebsitemember');
                foreach($sites as $url) {
                    $siteItem = array(
                        'customer_id'   => $customerId,
                        'domain_name'   => trim($url),
                        'verified_key'  => Mage::helper('affiliate')->getWebsiteVerificationKey(trim($url)),
                        'status'        => MW_Affiliate_Model_Statuswebsite::UNVERIFIED
                    );
                    
                    $websiteModel->setData($siteItem);
                    $websiteModel->save();
                }
            }
           
            /* Set group for affiliate */
            $group = array(
                'customer_id'    => $customerId,
                'group_id'        => $data['group_id']
            );
            
            Mage::getModel('affiliate/affiliategroupmember')->setData($group)->save();
           
            /* Send email to affilite to notify success */
            Mage::helper('affiliate')->sendMailCustomerActiveAffiliate($customerId);
           
            $this->_getSession()->addSuccess($this->__("The member has successfully added"));
            $this->_redirect('affiliate/adminhtml_affiliatemember');
        }
        else
        {
            $this->_getSession()->addError($this->__(""));
            $this->_redirect('affiliate/adminhtml_affiliatemember');
        }
    }
   
    public function saveAction() {
        $data = $this->getRequest()->getPost();
        
        if($data)
        {
            $member_id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('affiliate/affiliatecustomers');
            $left_leg = $data['left_leg'];
            $right_leg = $data['right_leg'];
            $affiliate_type = $data['anetwork'];
            
            //Check Referral Code First
            if ($data['referral_code']):
	   	$referral_info = Mage::getModel('affiliate/affiliatecustomers')->getCollection()->addFieldToFilter('referral_code', $data['referral_code']);
                if (sizeof($referral_info->getData()) > 1){
                    $this->_getSession()->addError($this->__('El Codigo Referido ya esta en uso'));
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    return;
                }
            endif;
            
            try
            {   
                if($member_id!='')
                {   
                    $store_id = Mage::getModel('customer/customer')->load($member_id)->getStoreId();
                    $max = (double)Mage::helper('affiliate/data')->getWithdrawMaxStore($store_id);
                    $min = (double)Mage::helper('affiliate/data')->getWithdrawMinStore($store_id);
                    $collection = $model->load($member_id);
                    
                    if (($left_leg != $collection->getLeftLeg()) || ($right_leg != $collection->getRightLeg())){                        
                        $var = "Se hizo una modificacion sobre ". Mage::getModel('customer/customer')->load($member_id)->getName(). ' con #'.$collection->getReferralCode()."<br>Izquierda: {$collection->getLeftLeg()} a {$left_leg}"."<br>Derecha: {$collection->getRightLeg()} a {$right_leg}";
                        Mage::helper('affiliate_binary')->binarychangeuser($var);                
                    }
                    
                    $collection->setAnetwork($affiliate_type);
                    $collection->setLeftLeg($left_leg);
                    $collection->setRightLeg($right_leg);
                                        
                    if($data['referral_code']) {
                        $collection->setReferralCode($data['referral_code']);
                    }
                    
                    if($data['referral_sponsor']) {
                        $collection->setReferralSponsor($data['referral_sponsor']);
                    }
                    
                    if($data['customer_url']) {
                        $collection->setCustomerUrl($data['customer_url']);
                    }

                    if($data['customer_photo']) {
                        $collection->setCustomerPhoto($data['customer_photo']);
                    }
                     
                    if(isset($data['affiliate_parent']) && $data['affiliate_parent'] != '')
                    {   
                        $affiliateFilters = Mage::getModel('customer/customer')
                            ->getCollection()
                            ->addFieldToFilter('email',$data['affiliate_parent']);
                        
                        if(sizeof($affiliateFilters) > 0) {
                            foreach ($affiliateFilters as $affiliateFilter)
                            {
                                $mw_check = Mage::helper('affiliate')->getActiveAndEnableAffiliate($affiliateFilter->getId());
                                if($affiliateFilter->getId()!= $member_id && $mw_check == 1)
                                {   
                                    $collection->setCustomerInvited($affiliateFilter->getId());
                                } else {
                                    $this->_getSession()->addError($this->__('Affiliate parent invalid'));
                                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                                    return;
                                }
                                break;
                            }
                        } else {
                            $this->_getSession()->addError($this->__('Affiliate parent invalid'));
                            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                            return;
                        }
                    } else {
                        $collection->setCustomerInvited(0);
                    }
                    
                    $collection->setStatus($data['affiliate_status']);
                    $collection->setPaymentGateway($data['payment_gateway']);
                    
                    if($data['payment_gateway'] != 'banktransfer' && $data['payment_gateway'] != 'check')
                    {
                        $collection->setPaymentEmail($data['payment_email']);
                        $collectionFilters = Mage::getModel('affiliate/affiliatecustomers')
                            ->getCollection()
                            ->addFieldToFilter('payment_email',$data['payment_email']);
                        
                        if(sizeof($collectionFilters) > 0) {
                            foreach($collectionFilters as $collectionFilter)
                            {
                                if($collectionFilter->getCustomerId()!= $member_id)
                                {   
                                    $this->_getSession()->addError($this->__('There is already an account with this emails paypal'));
                                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                                    return;
                                }
                            }
                        }
                    } else {
                        $collection ->setPaymentEmail('');
                    }
                     
                    $collection->setAutoWithdrawn($data['auto_withdrawn']);
                    $withdrawn_level = (double)$data['withdrawn_level'];
                    $auto_withdrawn = (int)$data['auto_withdrawn'];
                    if($auto_withdrawn == MW_Affiliate_Model_Autowithdrawn::AUTO)
                    { 
                        if($withdrawn_level < $min || $withdrawn_level > $max)
                        {
                            $this->_getSession()->addError($this->__('Please insert a value of Auto payment when account balance reaches that is in range of [%s, %s]',$min,$max));
                            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                            return;
                        }
                        $collection ->setWithdrawnLevel($data['withdrawn_level']);
                    }
                    
                    if(!$data['reserve_level']){
                        $data['reserve_level'] == 0;
                    }
                    
                    if(!$data['referral_site']) {
                        $data['referral_site'] == '';
                    }
                    
                    $collection->setReferralSite($data['referral_site']);
                    $collection->setReserveLevel($data['reserve_level']);
                    
                    $collection->save();

                    if($data['payment_gateway'] == 'banktransfer') {
                        $bank_name = $data['bank_name'];
                        $name_account = $data['name_account'];
                        $bank_country = $data['bank_country'];
                        $swift_bic = $data['swift_bic'];
                        $account_number = $data['account_number'];
                        $re_account_number = $data['re_account_number'];
                    } else {
                        $bank_name = '';
                        $name_account = '';
                        $bank_country = '';
                        $swift_bic = '';
                        $account_number = '';
                        $re_account_number = '';
                    }
                    
                    $data_bank  =  array(
                        'bank_name'         => $bank_name,
                        'name_account'      => $name_account,
                        'bank_country'      => $bank_country,
                        'swift_bic'         => $swift_bic,
                        'account_number'    => $account_number,
                        're_account_number' => $re_account_number);
                   
                    $model1 = Mage::getModel('affiliate/affiliatecustomers')->load($member_id);
                    $model1->setData($data_bank)->setId($member_id);
                    $model1->save();

                    // them member vao group
                    if($data['group_id'] != 0)
                    {
                        // xoa cac member trong group de update lai du lieu moi
                        $group_members = Mage::getModel('affiliate/affiliategroupmember')
                            ->getCollection()
                            ->addFieldToFilter('customer_id',$member_id);

                       if(sizeof($group_members) > 0){
                            foreach ($group_members as $group_member) {
                                $group_member ->delete();
                            }
                       }
                       
                       $datamembergroup['group_id'] = $data['group_id'];
                       $datamembergroup['customer_id'] = $member_id;
                       Mage::getModel("affiliate/affiliategroupmember")->setData($datamembergroup)->save();
                    }
                    
                    //set total member customer program
                    Mage::helper('affiliate')->setTotalMemberProgram();

                    // edit customer
                    // truong hop them bot credit do admin voi ly do.....
                    $amountCredit = $data['amount_credit'];
                    if($amountCredit != 0)
                    {
                        /* Update affiliate customer table */
                        $affiliateCustomerModel = Mage::getModel('affiliate/affiliatecustomers')->load($member_id);
                        $oldCommission = $affiliateCustomerModel->getTotalCommission();
                        $newCommission = $oldCommission + $amountCredit;
                        $newCommission = round($newCommission, 2);
                        $affiliateCustomerModel->setData('total_commission', $newCommission)->save();

                        /* Update credit customer table */
                        $creditcustomer = Mage::getModel('credit/creditcustomer')->load($member_id);
                        $oldCredit = $creditcustomer->getCredit();
                        $typeTransaction = MW_Credit_Model_Transactiontype::ADMIN_CHANGE;
                        $comment = '';
                        
                        if($data['comment']) {
                            $comment = $data['comment'];
                        }
                        
                        $newCredit = $oldCredit + $amountCredit;
                        $newCredit = round($newCredit,2);
                        $amountCredit = round($amountCredit,2);
                        $oldCredit = round($oldCredit,2);
                        $creditcustomer->setData('credit',$newCredit)->save();
               
                        // Save history transaction for customer
                        $historyData = array(
                            'customer_id'           => $member_id,
                            'type_transaction'      => MW_Credit_Model_Transactiontype::ADMIN_CHANGE,
                            'transaction_detail'    => $comment,
                            'amount'                => $amountCredit,
                            'beginning_transaction' => $oldCredit,
                            'end_transaction'       => $newCredit,
                            'status'                => MW_Credit_Model_Orderstatus::COMPLETE,
                            'created_time'          => now()
                        );
                        
                        Mage::getModel("credit/credithistory")->setData($historyData)->save();
                            
                        // gui mail cho khach hang khi admin them bot credit voi ly do.........
                        $storeId = Mage::getModel('customer/customer')->load($member_id)->getStoreId();
                        $store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
                        $sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
                        $email = Mage::getModel('customer/customer')->load($member_id)->getEmail();
                        $name = Mage::getModel('customer/customer')->load($member_id)->getName();
                        $teampale = 'affiliate/customer/email_template_credit_balance_changed';
                        $sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
                        $customer_credit_link = Mage::app()->getStore($storeId)->getUrl('credit');
                        $data_mail['customer_name'] = $name;
                        $data_mail['transaction_amount'] = Mage::helper('core')->currency($amountCredit,true,false);
                        $data_mail['customer_balance'] = Mage::helper('core')->currency($newCredit,true,false);
                        $data_mail['transaction_detail'] = $comment;
                        $data_mail['transaction_time'] = now();
                        $data_mail['sender_name'] = $sender_name;
                        $data_mail['store_name'] = $store_name;
                        $data_mail['customer_credit_link'] = $customer_credit_link;
                        Mage::helper('affiliate')->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
                    }
                     
                    // truong hop payout cho customer
                    $payout_credit = $data['payout_credit'];
                    if(isset($payout_credit) && $payout_credit !='')
                    {
                        $payout_credit = (double)$payout_credit;
                        $creditcustomer = Mage::getModel('credit/creditcustomer')->load($member_id);
                        $oldCredit = (double)$creditcustomer->getCredit();
                        
                        if($payout_credit > 0 && $payout_credit <=  $oldCredit)
                        {
                            $this ->getRequestWithdrawComplete($member_id, $payout_credit);
                        }
                        else
                        {
                            $this->_getSession()->addError($this->__('Please insert a value of Payout that is in range of (%s, %s]',0,$oldCredit));
                            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                            return;
                        }
                   } 
                }
                else if($member_id == '')
                {
                    $active = MW_Affiliate_Model_Statusactive::ACTIVE;
                    $payment_gateway = $data['getway_withdrawn'];
                    $paypal_email = $data['paypal_email'];
                    $auto_withdrawn = $data['auto_withdrawn'];
                    $payment_release_level = $data['payment_release_level'];
                    $reserve_level = $data['reserve_level'];
                    $referral_site = $data['referral_site'];
                   
                    if($payment_gateway == 'check') {
                        $paypal_email = '';
                    }
                     
                    if($payment_gateway == 'banktransfer') {
                        $paypal_email = '';
                        $bank_name = $data['bank_name'];
                        $name_account = $data['name_account'];
                        $bank_country = $data['bank_country'];
                        $swift_bic = $data['swift_bic'];
                        $account_number = $data['account_number'];
                        $re_account_number = $data['re_account_number'];
                    }
                   
                    if($auto_withdrawn == MW_Affiliate_Model_Autowithdrawn::MANUAL) {
                        $payment_release_level = 0;
                    }
                    if(!$reserve_level) {
                        $reserve_level = 0;
                    }
                    if(!$referral_site) {
                        $referral_site = '';
                    }
                   
                    $customerData = array(
                        'customer_id'       => $member_id,
                        'active'            => $active,
                        'anetwork'          => $affiliate_type,
                        'payment_gateway'   => $payment_gateway,
                        'payment_email'     => $paypal_email,
                        'auto_withdrawn'    => $auto_withdrawn,
                        'withdrawn_level'   => $payment_release_level,
                        'reserve_level'     => $reserve_level,
                        'bank_name'         => $bank_name,
                        'name_account'      => $name_account,
                        'bank_country'      => $bank_country,
                        'swift_bic'         => $swift_bic,
                        'account_number'    => $account_number,
                        're_account_number' => $re_account_number,
                        'referral_site'     => $referral_site,
                        'total_commission'  => 0,
                        'total_paid'        => 0,
                        'referral_code'     => '',
                        'referral_sponsor'  => '',
                        'customer_url'      => '',
                        'customer_photo'    => '',
                        'status'            => 1,
                        'invitation_type'   => '',
                        'customer_time'     => now(),
                        'customer_invited'  => 0
                    );
                    Mage::getModel('affiliate/affiliatecustomers')->saveCustomerAccount($customerData);
                }   
                
                
               
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('affiliate')->__('The member has successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    return;
                }
                
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Unable to find member to save'));
        $this->_redirect('*/*/');
    }
   
    public function getRequestWithdrawComplete($customer_id,$credit)
    {
        $store_id = Mage::getModel('customer/customer')->load($customer_id)->getStoreId();
       
        $fee = Mage::helper('affiliate/data')->getFeeStore($store_id);
        if(strpos($fee, '%')) {
            $percent = doubleval(substr($fee, 0, strpos($fee, '%')));
            $fee = ($percent * $credit)/100;

        } else {
            $fee = doubleval($fee);
        }
           
        $withdraw_receive = $credit - $fee;
        $affiliate_customer = Mage::getModel('affiliate/affiliatecustomers')->load($customer_id);
        $payment_gateway = $affiliate_customer->getPaymentGateway();
        $payment_email = $affiliate_customer->getPaymentEmail();
        if($payment_gateway == 'banktransfer') {
            $payment_email = '';
        }
           
          $bank_name = $affiliate_customer->getBankName();
        $name_account = $affiliate_customer->getNameAccount();
        $bank_country = $affiliate_customer->getBankCountry();
        $swift_bic = $affiliate_customer->getSwiftBic();
        $account_number= $affiliate_customer->getAccountNumber();
        $re_account_number = $affiliate_customer->getReAccountNumber();

        $withdrawnData =  array(
                                'customer_id'        => $customer_id,
                                'payment_gateway'    => $payment_gateway,
                                  'payment_email'        => $payment_email,
                                  'bank_name'            => $bank_name,
                                'name_account'        => $name_account,
                                'bank_country'        => $bank_country,
                                'swift_bic'            => $swift_bic,
                                'account_number'    => $account_number,
                                're_account_number'    => $re_account_number,
                                 'withdrawn_amount'    => $credit,
                                  'fee'                => $fee,
                                'amount_receive'    => $withdraw_receive,
                                  'status'            => MW_Affiliate_Model_Status::COMPLETE,
                                'withdrawn_time'    => now()
                            );
        Mage::getModel('affiliate/affiliatewithdrawn')->setData($withdrawnData)->save();
         
        // update lai credit
        $creditcustomer = Mage::getModel('credit/creditcustomer')->load($customer_id);
        $oldCredit = $creditcustomer->getCredit();
           $amount = - $credit;
          $newCredit = $oldCredit + $amount;
          $newCredit=round($newCredit,2);
           $creditcustomer->setCredit($newCredit)->save();
          
           $collectionWithdrawns = Mage::getModel('affiliate/affiliatewithdrawn')
                                   ->getCollection()
                                ->addFieldToFilter('customer_id',$customer_id)
                                ->setOrder('withdrawn_id','DESC');
        foreach($collectionWithdrawns as $collectionWithdrawn)
        {
            $withdrawn_id = $collectionWithdrawn->getWithdrawnId();
            break;
        }
       
           // luu vao bang credit history
           $historyData = array(
                                'customer_id'            => $customer_id,
                              'type_transaction'        => MW_Credit_Model_Transactiontype::WITHDRAWN,
                              'status'                => MW_Credit_Model_Orderstatus::COMPLETE,
                              'transaction_detail'    => $withdrawn_id,
                                'amount'                => $amount,
                              'beginning_transaction'=> $oldCredit,
                             'end_transaction'        => $newCredit,
                                'created_time'            => now()
                           );
           Mage::getModel("credit/credithistory")->setData($historyData)->save();
          
           // gui mail cho khach hang khi rut tien thanh cong, do admin rut
           Mage::helper('affiliate')->sendMailCustomerWithdrawnComplete($customer_id, $amount);
          
    }

    public function withdrawnstatusAction()
    {  
        //echo $this->getRequest()->getParam('status_program');exit;
        $withdrawn_ids = $this->getRequest()->getParam('Affiliate_member_withdrawn');
        $customer_Id = $this->getRequest()->getParam('id');
        $status     = (int)$this->getRequest()->getParam('status');
        if(!is_array($withdrawn_ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select withdrawn(s)'));
        } else {
            try {
                    Mage::helper('affiliate') ->processWithdrawn($status, $withdrawn_ids);           
             // thuc hien
            } catch (Mage_Core_Model_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
           
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/edit/id/'.$customer_Id);
    }
   
    public function massStatusAction()
    {  
        //echo $this->getRequest()->getParam('status_program');exit;
        $customerIds = $this->getRequest()->getParam('affiliatememberGrid');
        if(!is_array($customerIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select member(s)'));
        } else {
            try {
                foreach ($customerIds as $customerId) {
                    $status_affiliate = Mage::getSingleton('affiliate/affiliatecustomers')->load($customerId)->getStatus();
                    // gui mail trong truong hop khoa customer affiliate
                    if($this->getRequest()->getParam('status') == MW_Affiliate_Model_Statusreferral::LOCKED && $status_affiliate ==MW_Affiliate_Model_Statusreferral::ENABLED)
                    {
                        $storeId = Mage::getModel('customer/customer')->load($customerId)->getStoreId();
                        $store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
                        $sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
                        $email = Mage::getModel('customer/customer')->load($customerId)->getEmail();
                        $name = Mage::getModel('customer/customer')->load($customerId)->getName();
                        $teampale = 'affiliate/customer/email_template_lock';
                        $sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
                        $link = Mage::app()->getStore($storeId)->getUrl('affiliate');
                        $data_mail['customer_name'] = $name;
                        $data_mail['sender_name'] = $sender_name;
                        $data_mail['store_name'] = $store_name;
                        $data_mail['link'] = $link;
                        Mage::helper('affiliate')->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
                    }
                    // gui mail trong truong hop mo khoa customer affiliate
                    else if($this->getRequest()->getParam('status')==MW_Affiliate_Model_Statusreferral::ENABLED && $status_affiliate==MW_Affiliate_Model_Statusreferral::LOCKED)
                    {
                        $storeId = Mage::getModel('customer/customer')->load($customerId)->getStoreId();
                        $store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
                        $sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
                        $email = Mage::getModel('customer/customer')->load($customerId)->getEmail();
                        $name = Mage::getModel('customer/customer')->load($customerId)->getName();
                        $teampale = 'affiliate/customer/email_template_unlock';
                        $sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
                        $customer_affiliate_link = Mage::app()->getStore($storeId)->getUrl('affiliate');
                        $data_mail['customer_name'] = $name;
                        $data_mail['sender_name'] = $sender_name;
                        $data_mail['store_name'] = $store_name;
                        $data_mail['customer_affiliate_link'] = $customer_affiliate_link;
                        Mage::helper('affiliate')->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
                    }
                    $customer = Mage::getSingleton('affiliate/affiliatecustomers')
                        ->load($customerId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($customerIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
   
    public function massParentAffiliateAction()
    {  
        $customerIds = $this->getRequest()->getParam('affiliatememberGrid');
        if(!is_array($customerIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select member(s)'));
        } else {
            try {
                $parent_affiliate_id = 0;
                $parent_affiliate = $this->getRequest()->getParam('parent_affiliate');
                 if(isset($parent_affiliate) && $parent_affiliate != ''){
                    
                     $affiliateFilters = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('email',$parent_affiliate);
                    if(sizeof($affiliateFilters) > 0 )
                      {
                         foreach ($affiliateFilters as $affiliateFilter)
                         {
                          
                          $mw_check = Mage::helper('affiliate')->getActiveAndEnableAffiliate($affiliateFilter->getId());
                           if($mw_check == 1)
                           {   
                                 $parent_affiliate_id = $affiliateFilter->getId();  
                           }else{
                                   $this->_getSession()->addError($this->__('Affiliate parent invalid'));
                                $this->_redirect('*/*/index');
                                 return;   
                           }
                           break;
                         }
                      
                     }else{
                         $this->_getSession()->addError($this->__('Affiliate parent invalid'));
                        $this->_redirect('*/*/index');
                         return;
                     }               
                }
               
                $count = 0;
                foreach ($customerIds as $customerId) {
                    if($parent_affiliate_id && $parent_affiliate_id != $customerId){
                        $count = $count + 1;
                        Mage::getSingleton('affiliate/affiliatecustomers')->load($customerId)->setCustomerInvited($parent_affiliate_id)->save();
                    }   
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', $count)
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
   
    public function productGridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('affiliate/adminhtml_affiliateprogram_edit_tab_grid', 'admin.program.products')->toHtml()
        );
    }
   
     public function productAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
   
    public function ajaxemailAction() {
         if(!$this->getRequest()->isXmlHttpRequest()){
             return null;
         }

        $search = $this->getRequest()->getParam('customer_email');
        $search = preg_replace('/[^a-z0-9 ]/si', '', $search);
       
        $existedAffiliates = Mage::getModel('affiliate/affiliatecustomers')
                             ->getCollection()
                             ->addFieldToFilter('active', array('in' => array(MW_Affiliate_Model_Statusactive::ACTIVE,MW_Affiliate_Model_Statusactive::PENDING)));
        $ids = array();
        foreach($existedAffiliates as $affiliate) {
            $ids[] = $affiliate->getCustomerId(); 
        }
       
        $customers = Mage::getModel('customer/customer')
                     ->getCollection()
                     ->addFieldToFilter('email', array('like' => $search . '%'));
        if(count($ids)) {
            $customers->addFieldToFilter('entity_id', array('nin' => $ids));
        }

        if(sizeof($customers)) {
            $html = array();
            foreach($customers as $customer) {
                $html[] = '<li>' . $customer->getEmail() . '</li>';
            }
            $html = '<ul>' . implode('', $html) . '</ul>';
           
            echo $html;
        } else {
            echo '<ul><li>No result found</li></ul>';
        }
       
        die;       
    }
   
    public function ajaxcheckreferralcodeAction() {
        $memberId = $this->getRequest()->getParam('member_id');       
        $referralCode = $this->getRequest()->getParam('referral_code');
       
        /* Check if referral code existed or not */
        $collectionCustomers = Mage::getModel('affiliate/affiliatecustomers')
                               ->getCollection()
                               ->addFieldToFilter('customer_id', array('nin' => array($memberId)))
                               ->addFieldToFilter('referral_code', array('eq' => $referralCode));
                              
        if(count($collectionCustomers) > 0) {
            echo 0;
        } else {
            echo 1;
        }
       
        die;
    }
    public function ajaxcheckreferralsponsorAction() {
        $memberId = $this->getRequest()->getParam('member_id');       
        $referralSponsor = $this->getRequest()->getParam('referral_sponsor');
       
        /* Check if referral code existed or not */
        $collectionCustomers = Mage::getModel('affiliate/affiliatecustomers')
                               ->getCollection()
                               ->addFieldToFilter('customer_id', array('nin' => array($memberId)))
                               ->addFieldToFilter('referral_code', array('eq' => $referralSponsor));
                             
        if(count($collectionCustomers) > 0) {
            echo 0;
        } else {
            echo 1;
        }
       
        die;
    }
    
    public function ajaxcheckuniquecustomurlAction() {
        $memberId = $this->getRequest()->getParam('member_id');       
        $customer_url = $this->getRequest()->getParam('customer_url');
       
        /* Check if referral code existed or not */
        $collectionCustomers = Mage::getModel('affiliate/affiliatecustomers')
                               ->getCollection()
                               ->addFieldToFilter('customer_id', array('nin' => array($memberId)))
                               ->addFieldToFilter('customer_url', array('eq' => $customer_url));
                             
        if(count($collectionCustomers) > 0) {
            echo 0;
        } else {
            echo 1;
        }
       
        die;
    }
   
    public function exportCsvAction()
    {
        $fileName   = 'affiliate_member.csv';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_grid')->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'affiliate_member.xml';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliatemember_grid')->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
    
    public function signinAction()
    {
        $data = $this->getRequest()->getParams();
        if ($data){
            $id = $data['id'];            
            $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'mwcon/'.$id;            
            header("Location: {$url}");
            die();
        }
    }
}


