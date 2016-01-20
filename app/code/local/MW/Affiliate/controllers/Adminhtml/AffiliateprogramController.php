<?php

class MW_Affiliate_Adminhtml_AffiliateprogramController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('affiliate/program')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Manager Programs'), Mage::helper('adminhtml')->__('Manager Program'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('affiliate/affiliateprogram')->load($id);
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			Mage::getModel('affiliate/affiliateprogram')->getConditions()->setJsFormObject('rule_conditions_fieldset');
			Mage::getModel('affiliate/affiliateprogram')->getActions()->setJsFormObject('rule_actions_fieldset');
			Mage::register('affiliate_data_program', $model);

			$this->loadLayout();
			$this->_setActiveMenu('affiliate/program');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliateprogram_edit'))
				->_addLeft($this->getLayout()->createBlock('affiliate/adminhtml_affiliateprogram_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Program does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
	public function transactionAction()
	{
        $this->getResponse()->setBody($this->getLayout()->createBlock('affiliate/adminhtml_affiliateprogram_edit_tab_transaction')->toHtml());
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
		    //Zend_Debug::dump($data['send_mail']);die();
		    //var_dump($this->getRequest()->getParam('status'));exit;
		    $data = $this->getRequest()->getPost();
			if (isset($data['store_view'])){
				$stores = $data['store_view'];
				//zend_debug::dump($stores);die();
				$storesCount = count($stores);
				$storesIndex = 1;
				$storesData = '';
				$check = 0;
				foreach ($stores as $store){
					if($store == '0') $check = 1;
					$storesData .= $store;
					if ($storesIndex < $storesCount){
						$storesData .= ',';
					}
					$storesIndex++;
				}
				if($check == 1) $data['store_view'] = '0';
				else $data['store_view'] = $storesData;
			}
			$program_id = $this->getRequest()->getParam('id');
			$model = Mage::getModel('affiliate/affiliateprogram');
			$data['status']= $data['status_program'];
			try {	
				if($program_id != ''){
					if(Mage::app()->isSingleStoreMode()) $data['store_view'] = '0';	
					$model->setData($data)->setId($program_id);
					$model->save();
					// save conditions
					if (isset($data['rule']['conditions'])) {
	                    $data['conditions'] = $data['rule']['conditions'];
	                }
	                if (isset($data['rule']['actions'])) {
	                    $data['actions'] = $data['rule']['actions'];
	                }
					$model->load($program_id);
					unset($data['rule']);
					$model->loadPost($data);
					$model->save();
				}
				if($program_id == ''){
					//Zend_Debug::dump($data);die();
					if(Mage::app()->isSingleStoreMode()) $data['store_view'] = '0';
					$model->setData($data)->save();
					// save conditions
					if (isset($data['rule']['conditions'])) {
	                    $data['conditions'] = $data['rule']['conditions'];
	                }
	                if (isset($data['rule']['actions'])) {
	                    $data['actions'] = $data['rule']['actions'];
	                }
	                unset($data['rule']);
					$model->loadPost($data);
					$model->save();
					
					$collectionprograms = Mage::getModel('affiliate/affiliateprogram')->getCollection()
																	   ->setOrder('program_id', 'DESC');
					foreach ($collectionprograms as $collectionprogram) {
		        	 	$program_id = $collectionprogram ->getProgramId();
		        	 	break;
		        	 }
				}
				// xu ly check groups
				$_groups = $this->getRequest()->getParam('addgroup');
				//var_dump($_products);exit;
				$groups = $_groups['program'];
				//var_dump($products);exit;
				if(isset($groups))
				{   
					// xoa tat ca cac group co lien quan den chuong trinh
					$collections = Mage::getModel('affiliate/affiliategroupprogram')->getCollection()
			        									->addFieldToFilter('program_id',$program_id);
			        if(sizeof($collections)>0){
			        	 foreach ($collections as $collection) {
			        	 	$collection->delete();
			        	 }
			        	
			        }
					$group_idss = explode("&",$groups);
					//var_dump($product_idss);exit;
					$datagroup = array();
					foreach ($group_idss as $group_ids) {
						$group_id = explode("=",$group_ids);
						//var_dump($product_id);exit;
						$datagroup['group_id'] = $group_id[0];
						$datagroup['program_id'] = $program_id; 
						if($datagroup['group_id'] != 0)
						{
							Mage::getModel("affiliate/affiliategroupprogram")->setData($datagroup)->save();
						}
					}	
				}
				$group_progam = array();
				$collection_programs = Mage::getModel('affiliate/affiliategroupprogram')->getCollection()
			        									    ->addFieldToFilter('program_id',$program_id);
		        if(sizeof($collection_programs)>0){
		        	foreach ($collection_programs as $collection_program) {
		        		$group_progam[] = $collection_program ->getGroupId();
		        	}
		        	
		        }
				// gui mail cho cac thanh vien affiliate khi co chuong trinh moi dc tao
				if(isset($data['send_mail']) && $data['send_mail'] == 1)
				{
		        	$affiliate_customers = Mage::getModel('affiliate/affiliatecustomers')->getCollection()
		        	 					->addFieldToFilter('active',MW_Affiliate_Model_Statusactive::ACTIVE);
					 foreach ($affiliate_customers as $affiliate_customer)
					  {
					 	$customer_id = $affiliate_customer ->getCustomerId();
					 	$group_affiliate = 0;
					 	$customerPrograms = Mage::getModel('affiliate/affiliategroupmember')->getCollection()
															  ->addFieldToFilter('customer_id',$customer_id);
				        foreach ($customerPrograms as $customerProgram) {
				        	$group_affiliate = $customerProgram ->getGroupId();
				        }
					 	if(sizeof($group_progam) > 0 && $group_affiliate != 0 && in_array($group_affiliate, $group_progam))
					 	{
					 		$mw_store_views = explode(",",$model->getStoreView());
					 		if($mw_store_views[0]== '0'){
						 		$AllStores = Mage::app()->getStores();
								foreach ($AllStores as $storeId)
								{
									//$storeId = Mage::app()->getStore($storeId)->getId();
									$this->sendEmailNewProgram($data,$customer_id,$storeId);
								}	
					 		}else{
					 			foreach ($mw_store_views as $storeId)
								{
									$this->sendEmailNewProgram($data,$customer_id,$storeId);
								}
					 			
					 		};
						 	
						 	
					 	}
					 }
				} 
		        //set total member customer program 
				Mage::helper('affiliate')->setTotalMemberProgram();
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('affiliate')->__('The program has successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Unable to find program to save'));
        $this->_redirect('*/*/');
	}
	public function sendEmailNewProgram($data,$customer_id,$storeId)
	{
		$store_name = Mage::getStoreConfig('general/store_information/name', $storeId);
    	$sender = Mage::getStoreConfig('affiliate/customer/email_sender', $storeId);
    	$email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
    	$name = Mage::getModel('customer/customer')->load($customer_id)->getName();
    	$teampale = 'affiliate/customer/email_template_add_program';
    	$sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $storeId);
    	$customer_program_link = Mage::app()->getStore($storeId)->getUrl('affiliate/index/listprogram');
    	$data_mail['customer_name'] = $name;
    	$data_mail['program_name'] = $data['program_name'];
    	$data_mail['program_commission'] = $data['commission'];
    	$data_mail['program_discount'] = $data['discount'];
    	$data_mail['program_description'] = $data['description'];
    	$data_mail['start_date'] = $data['start_date'];
    	$data_mail['end_date'] = $data['end_date'];
    	$data_mail['sender_name'] = $sender_name;
    	$data_mail['store_name'] = $store_name;
    	$data_mail['customer_program_link'] = $customer_program_link;
    	Mage::helper('affiliate')->_sendEmailTransactionNew($sender,$email,$name,$teampale,$data_mail,$storeId);
	}
	public function groupGridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('affiliate/adminhtml_affiliateprogram_edit_tab_group', 'admin.program.groups')->toHtml()
        );
    }
 	public function groupAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') >0 ) {
			try {
				$model = Mage::getModel('affiliate/affiliateprogram');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
				$group_programs = Mage::getModel('affiliate/affiliategroupprogram')->getCollection()
                                                 ->addFieldToFilter('program_id',$this->getRequest()->getParam('id'));
                foreach ($group_programs as $group_program) {
                    $group_program->delete();
                }
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The program has successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
    	//var_dump($this->getRequest()->getParam('affiliateprogramGrid'));exit;
        $programIds = $this->getRequest()->getParam('affiliateprogramGrid');
        if(!is_array($programIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select program(s)'));
        } else {
            try {
                foreach ($programIds as $programId) {
                    $program = Mage::getModel('affiliate/affiliateprogram')->load($programId);
                    $program->delete();
                    $group_programs = Mage::getModel('affiliate/affiliategroupprogram')->getCollection()
                                                 ->addFieldToFilter('program_id',$programId);
                    foreach ($group_programs as $group_program) {
                    	$group_program->delete();
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($programIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {   
    	//echo $this->getRequest()->getParam('status_program');exit;
        $programIds = $this->getRequest()->getParam('affiliateprogramGrid');
        if(!is_array($programIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select program(s)'));
        } else {
            try {
                foreach ($programIds as $programId) {
                	/*
                    $program = Mage::getSingleton('affiliate/affiliateprogram')
                        ->load($programId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                    */
                	$programId = (int)$programId;
                	$status = $this->getRequest()->getParam('status');
                	$resource = Mage::getSingleton('core/resource');
					$query = "UPDATE  {$resource->getTableName('affiliate/affiliateprogram')} SET status=".$status." where program_id = ".$programId.";";
					$conn = $resource->getConnection('core_write');
					$conn->query($query);
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($programIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function exportCsvAction()
    {
        $fileName   = 'affiliate_program.csv';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliateprogram_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'affiliate_program.xml';
        $content    = $this->getLayout()->createBlock('affiliate/adminhtml_affiliateprogram_grid')
            ->getXml();

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
	
}